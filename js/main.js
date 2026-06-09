// ==============================
// IKUSASA TECHNOLOGIES — MAIN JS
// ==============================

document.addEventListener('DOMContentLoaded', () => {

  // ---- MOBILE MENU ----
  const menuBtn   = document.querySelector('.mobile-menu-btn');
  const navLinks  = document.querySelector('.nav-links');
  const iconMenu  = document.querySelector('.icon-menu');
  const iconClose = document.querySelector('.icon-close');

  if (menuBtn && navLinks) {
    menuBtn.addEventListener('click', () => {
      const isOpen = navLinks.classList.toggle('open');
      if (iconMenu)  iconMenu.style.display  = isOpen ? 'none'  : 'block';
      if (iconClose) iconClose.style.display = isOpen ? 'block' : 'none';
    });

    // Close on link click
    navLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('open');
        if (iconMenu)  iconMenu.style.display  = 'block';
        if (iconClose) iconClose.style.display = 'none';
      });
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!menuBtn.contains(e.target) && !navLinks.contains(e.target)) {
        navLinks.classList.remove('open');
        if (iconMenu)  iconMenu.style.display  = 'block';
        if (iconClose) iconClose.style.display = 'none';
      }
    });
  }

  // ---- HEADER SCROLL ----
  const header = document.querySelector('header');
  let lastScroll = 0;

  window.addEventListener('scroll', () => {
    const scrollY = window.scrollY;
    if (scrollY > lastScroll && scrollY > 120) {
      header.style.transform = 'translateY(-100%)';
    } else {
      header.style.transform = 'translateY(0)';
    }
    lastScroll = scrollY;
  }, { passive: true });

  // ---- SCROLL ANIMATIONS ----
  const animEls = document.querySelectorAll('[data-anim]');

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const delay = el.dataset.delay || 0;
        setTimeout(() => el.classList.add('visible'), Number(delay));
        observer.unobserve(el);
      }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

  animEls.forEach(el => observer.observe(el));

  // ---- STAGGER CHILDREN ----
  document.querySelectorAll('[data-stagger]').forEach(parent => {
    const children = parent.querySelectorAll('[data-anim]');
    children.forEach((child, i) => {
      child.dataset.delay = i * 80;
    });
  });

  // ---- COUNTER ANIMATION ----
  function animateCounter(el) {
    const target = parseInt(el.dataset.count, 10);
    const suffix = el.dataset.suffix || '';
    const duration = 1600;
    const start = performance.now();

    function step(now) {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * target) + suffix;
      if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  const counters = document.querySelectorAll('[data-count]');
  if (counters.length) {
    const counterObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          counterObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });

    counters.forEach(c => counterObserver.observe(c));
  }

  // ---- FAQ ACCORDION ----
  document.querySelectorAll('.faq-trigger').forEach(trigger => {
    trigger.addEventListener('click', () => {
      const item = trigger.closest('.faq-item');
      const isOpen = item.classList.contains('open');

      // Close all
      document.querySelectorAll('.faq-item.open').forEach(openItem => {
        openItem.classList.remove('open');
      });

      // Open clicked if it was closed
      if (!isOpen) item.classList.add('open');
    });
  });

  // ---- CONTACT FORM ----
  const form = document.getElementById('contactForm');
  if (form) {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const name    = form.fullName.value.trim();
      const email   = form.email.value.trim();
      const message = form.message.value.trim();
      const emailRx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (name.length < 2) { showToast('Please enter your full name.', 'error'); return; }
      if (!emailRx.test(email)) { showToast('Please enter a valid email address.', 'error'); return; }
      if (message.length < 10) { showToast('Please enter a message (at least 10 characters).', 'error'); return; }

      const submitBtn = form.querySelector('.btn-submit');
      const originalHTML = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.style.opacity = '0.7';
      submitBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Sending...';

      // Add spin keyframe once
      if (!document.getElementById('spin-style')) {
        const s = document.createElement('style');
        s.id = 'spin-style';
        s.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
        document.head.appendChild(s);
      }

      try {
        const response = await fetch(form.action, {
          method: 'POST',
          body: new FormData(form),
          headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (response.ok && data.success) {
          submitBtn.innerHTML = originalHTML;
          setTimeout(() => { window.location.href = 'thank-you.html'; }, 400);
        } else {
          showToast(data.message || 'Something went wrong. Please try again.', 'error');
          submitBtn.disabled = false;
          submitBtn.style.opacity = '1';
          submitBtn.innerHTML = originalHTML;
        }
      } catch (err) {
        showToast('Could not connect. Please call us on 083 293 2025.', 'error');
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
        submitBtn.innerHTML = originalHTML;
      }
    });
  }

  // ---- TOAST NOTIFICATION ----
  window.showToast = function(message, type = 'success') {
    const existing = document.querySelectorAll('.toast');
    existing.forEach(t => t.remove());

    const icons = {
      success: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`,
      error:   `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`
    };

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
      <div class="toast-icon ${type}">${icons[type]}</div>
      <p class="toast-text">${message}</p>
      <button class="toast-close" aria-label="Close">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    `;

    document.body.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));

    const dismiss = () => {
      toast.classList.remove('show');
      setTimeout(() => toast.remove(), 350);
    };

    toast.querySelector('.toast-close').addEventListener('click', dismiss);
    setTimeout(dismiss, 5000);
  };

  // ---- SMOOTH SCROLL ----
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', (e) => {
      const id = a.getAttribute('href').slice(1);
      const target = document.getElementById(id);
      if (target) {
        e.preventDefault();
        window.scrollTo({ top: target.offsetTop - 88, behavior: 'smooth' });
      }
    });
  });

});
