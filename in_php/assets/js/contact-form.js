// Contact form validation and submission
document.addEventListener('DOMContentLoaded', function() {
    
    // Only enquiry forms that post to the mail handler
    const forms = document.querySelectorAll('form[action$="send_email.php"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (form.dataset.sending === '1') {
                return;
            }
            handleFormSubmit(form);
        });
    });
    
    // Keep textarea enter behavior predictable on long messages
    document.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && e.target.tagName === 'TEXTAREA') {
            e.stopPropagation();
        }
    });
});

function handleFormSubmit(form) {
    // Get form inputs
    const nameInput = form.querySelector('[name="name"], input[placeholder="Your name"]');
    const emailInput = form.querySelector('[name="email"], input[placeholder*="Email"]');
    const phoneInput = form.querySelector('[name="phone"], input[placeholder*="Phone"]');
    const serviceInput = form.querySelector('[name="service"]');
    const messageInput = form.querySelector('[name="message"], textarea[placeholder*="message"], textarea[name="textarea"]');
    const submitBtn = form.querySelector('button');
    
    // Get values
    const name = nameInput ? nameInput.value.trim() : '';
    const email = emailInput ? emailInput.value.trim() : '';
    const phone = phoneInput ? phoneInput.value.trim() : '';
    const service = serviceInput ? serviceInput.value.trim() : '';
    const message = messageInput ? messageInput.value.trim() : '';
    
    // Validation
    const errors = [];
    
    if (!name) {
        errors.push('Please enter your name');
        highlightError(nameInput);
    } else {
        removeError(nameInput);
    }
    
    if (!email) {
        errors.push('Please enter your email');
        highlightError(emailInput);
    } else if (!isValidEmail(email)) {
        errors.push('Please enter a valid email address');
        highlightError(emailInput);
    } else {
        removeError(emailInput);
    }
    
    if (!phone) {
        errors.push('Please enter your phone number');
        highlightError(phoneInput);
    } else if (!isValidPhone(phone)) {
        errors.push('Please enter a valid 10-digit phone number');
        highlightError(phoneInput);
    } else {
        removeError(phoneInput);
    }

    if (serviceInput) {
        if (!service) {
            errors.push('Please select a service');
            highlightError(serviceInput);
        } else {
            removeError(serviceInput);
        }
    }
    
    if (!message) {
        errors.push('Please enter your message');
        highlightError(messageInput);
    } else if (message.length < 10) {
        errors.push('Message must be at least 10 characters long');
        highlightError(messageInput);
    } else {
        removeError(messageInput);
    }
    
    // Show errors if any
    if (errors.length > 0) {
        showNotification(errors.join('\n'), 'error');
        return;
    }
    
    // Prepare form data
    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('phone', phone);
    if (service) {
        formData.append('service', service);
    }
    formData.append('message', message);
    const honeypot = form.querySelector('[name="company_website"]');
    formData.append('company_website', honeypot ? honeypot.value : '');
    formData.append('form_id', form.getAttribute('data-form-id') || 'website');
    
    // Show loading state
    const originalText = submitBtn.innerHTML;
    form.dataset.sending = '1';
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="btn-wraper">Sending...</span>';
    
    // Send form via AJAX
    fetch('send_email.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // "stored" means saved for the team but not emailed; the server message says so
            showNotification(data.message, 'success', data.delivery === 'stored' ? 9000 : 5000);
            // Analytics hook: category only, never the visitor's details
            document.dispatchEvent(new CustomEvent('nz:lead', {
                detail: {
                    formId: form.getAttribute('data-form-id') || 'contact',
                    service: service
                }
            }));
            // Clear form
            form.reset();
            // Remove error highlights
            form.querySelectorAll('input, textarea').forEach(field => removeError(field));
        } else {
            if (data.errors) {
                showNotification(data.errors.join('\n'), 'error');
            } else {
                showNotification(data.message, 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again later.', 'error');
    })
    .finally(() => {
        // Restore button
        delete form.dataset.sending;
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Email validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Phone validation
function isValidPhone(phone) {
    const phoneRegex = /^[0-9]{10}$/;
    const cleaned = phone.replace(/[\s\-]/g, '');
    return phoneRegex.test(cleaned);
}

// Highlight error field
function highlightError(field) {
    if (!field) return;
    field.style.borderColor = '#ff6b6b';
    field.style.backgroundColor = '#fff5f5';
}

// Remove error highlight
function removeError(field) {
    if (!field) return;
    field.style.borderColor = '';
    field.style.backgroundColor = '';
}

// Show notification
function showNotification(message, type, duration) {
    // Remove existing notification
    const existing = document.querySelector('.form-notification');
    if (existing) {
        existing.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = 'form-notification';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background-color: ${type === 'success' ? '#28a745' : '#dc3545'};
        color: white;
        border-radius: 5px;
        z-index: 9999;
        max-width: min(400px, calc(100vw - 40px));
        font-size: 14px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease-in-out;
        white-space: pre-wrap;
        word-wrap: break-word;
    `;
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    notification.setAttribute('role', type === 'success' ? 'status' : 'alert');

    // Auto remove after the display time (default 5 seconds)
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in-out';
        setTimeout(() => notification.remove(), 300);
    }, duration || 5000);
}

// Add animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
