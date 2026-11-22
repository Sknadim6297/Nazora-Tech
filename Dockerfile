# Stage 1: install PHP dependencies (PHPMailer) exactly as pinned in composer.lock
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && a2enmod rewrite headers expires deflate \
    && rm -rf /var/lib/apt/lists/*

# Production PHP settings: hide errors from visitors, log them to stderr (Render logs)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && { \
        echo 'expose_php = Off'; \
        echo 'display_errors = Off'; \
        echo 'log_errors = On'; \
        echo 'error_log = /dev/stderr'; \
        echo 'session.cookie_httponly = 1'; \
        echo 'session.use_strict_mode = 1'; \
        echo 'upload_max_filesize = 8M'; \
        echo 'post_max_size = 10M'; \
    } > "$PHP_INI_DIR/conf.d/zz-nazora.ini" \
    && { \
        echo 'ServerTokens Prod'; \
        echo 'ServerSignature Off'; \
    } > /etc/apache2/conf-enabled/zz-nazora-security.conf

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY . /var/www/html/
COPY --from=vendor /app/vendor /var/www/html/vendor

# Site files live at the image document root (repo root). .htaccess already targets domain root.
RUN mkdir -p storage/uploads/blog storage/uploads/portfolio storage/submissions \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage

EXPOSE 80

# A Render persistent disk mounted at /var/www/html/storage starts empty and root-owned;
# recreate the folders and hand them to Apache on every boot without touching existing files.
CMD ["sh", "-c", "mkdir -p storage/uploads/blog storage/uploads/portfolio storage/submissions && chown -R www-data:www-data storage && exec apache2-foreground"]
