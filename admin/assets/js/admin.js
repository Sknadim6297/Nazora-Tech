(function () {
  const root = document.documentElement;
  const storageKey = 'snf-admin-theme';
  const shell = document.getElementById('adminShell') || document.querySelector('.admin-shell');
  const sidebar = document.getElementById('adminSidebar');

  function setTheme(theme) {
    root.setAttribute('data-theme', theme);
    localStorage.setItem(storageKey, theme);
    document.querySelectorAll('[data-theme-toggle] i').forEach((icon) => {
      icon.className = theme === 'dark' ? 'ri-sun-line' : 'ri-moon-line';
    });
  }

  setTheme(localStorage.getItem(storageKey) || 'light');

  document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
      setTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    });
  });

  function closeSidebar() {
    shell?.classList.remove('sidebar-open');
  }

  document.querySelectorAll('[data-sidebar-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => shell?.classList.toggle('sidebar-open'));
  });

  document.querySelectorAll('[data-sidebar-backdrop]').forEach((el) => {
    el.addEventListener('click', closeSidebar);
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeSidebar();
  });

  const titleInput = document.querySelector('[data-slug-source]');
  const slugInput = document.querySelector('[data-slug-target]');
  if (titleInput && slugInput) {
    titleInput.addEventListener('input', () => {
      if (slugInput.dataset.touched === 'true') return;
      slugInput.value = titleInput.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    });
    slugInput.addEventListener('input', () => {
      slugInput.dataset.touched = 'true';
    });
  }

  const featuredImage = document.querySelector('[data-image-preview-input]');
  const previewImage = document.querySelector('[data-image-preview-target]');
  if (featuredImage && previewImage) {
    featuredImage.addEventListener('change', () => {
      const file = featuredImage.files && featuredImage.files[0];
      previewImage.src = file ? URL.createObjectURL(file) : (previewImage.dataset.placeholder || '');
      const removeCheckbox = document.getElementById('removeFeaturedImage');
      if (removeCheckbox && file) {
        removeCheckbox.checked = false;
      }
    });
  }

  if (window.ClassicEditor) {
    const editorElement = document.querySelector('[data-rich-editor]');
    if (editorElement) {
      ClassicEditor.create(editorElement, {
        toolbar: [
          'heading', '|', 'bold', 'italic', 'link',
          'bulletedList', 'numberedList', 'blockQuote',
          'insertTable', 'undo', 'redo',
        ],
      })
        .then((editor) => {
          window.__blogEditor = editor;
        })
        .catch(() => {});
    }
  }

  document.querySelectorAll('[data-blog-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      const textarea = form.querySelector('[data-rich-editor]');
      if (window.__blogEditor && textarea) {
        textarea.value = window.__blogEditor.getData();
      }

      if (textarea && textarea.hasAttribute('data-required-content')) {
        const plain = textarea.value.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
        if (!plain) {
          event.preventDefault();
          const wrap = textarea.closest('.editor-wrap');
          if (wrap) {
            wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          alert('Please add blog content before saving.');
          if (window.__blogEditor) {
            window.__blogEditor.editing.view.focus();
          }
        }
      }
    });
  });

  document.querySelectorAll('[data-auto-dismiss]').forEach((node) => {
    setTimeout(() => {
      node.style.opacity = '0';
      node.style.transform = 'translateY(-8px)';
      node.style.transition = 'all 0.3s ease';
      setTimeout(() => node.remove(), 300);
    }, 4000);
  });
})();
