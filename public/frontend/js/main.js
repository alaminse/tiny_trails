/* ============================================
   TinyTrails — Main JavaScript
   Secure, no eval(), no innerHTML with user data
   ============================================ */

(function () {
  'use strict';

  /* -------------------------------------------
     1. Mobile Navigation Toggle
     ------------------------------------------- */
  function initNavigation() {
    var toggle = document.querySelector('.nav__toggle');
    var navLinks = document.getElementById('nav-links');
    var nav = document.querySelector('.nav');

    if (!toggle || !navLinks) return;

    toggle.addEventListener('click', function () {
      var isOpen = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!isOpen));
      navLinks.classList.toggle('nav__links--open');
      document.body.classList.toggle('nav-open');
    });

    // Close mobile nav when clicking a link
    var links = navLinks.querySelectorAll('.nav__link');
    for (var i = 0; i < links.length; i++) {
      links[i].addEventListener('click', function () {
        toggle.setAttribute('aria-expanded', 'false');
        navLinks.classList.remove('nav__links--open');
        document.body.classList.remove('nav-open');
      });
    }

    // Close mobile nav on Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && navLinks.classList.contains('nav__links--open')) {
        toggle.setAttribute('aria-expanded', 'false');
        navLinks.classList.remove('nav__links--open');
        document.body.classList.remove('nav-open');
        toggle.focus();
      }
    });

    // Nav scroll shadow
    if (nav) {
      var lastScrollY = 0;
      window.addEventListener('scroll', function () {
        var scrollY = window.pageYOffset || document.documentElement.scrollTop;
        if (scrollY > 10) {
          nav.classList.add('nav--scrolled');
        } else {
          nav.classList.remove('nav--scrolled');
        }
        lastScrollY = scrollY;
      }, { passive: true });
    }
  }

  /* -------------------------------------------
     2. Scroll Animations (IntersectionObserver)
     ------------------------------------------- */
  function initScrollAnimations() {
    var elements = document.querySelectorAll('.animate-on-scroll');
    if (!elements.length) return;

    // Check for reduced-motion preference
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    if (prefersReducedMotion.matches) {
      // Immediately show all elements without animation
      for (var i = 0; i < elements.length; i++) {
        elements[i].classList.add('animate-on-scroll--visible');
      }
      return;
    }

    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        for (var j = 0; j < entries.length; j++) {
          if (entries[j].isIntersecting) {
            entries[j].target.classList.add('animate-on-scroll--visible');
            observer.unobserve(entries[j].target);
          }
        }
      }, {
        threshold: 0.1,
        rootMargin: '0px 0px -40px 0px'
      });

      for (var i = 0; i < elements.length; i++) {
        observer.observe(elements[i]);
      }
    } else {
      // Fallback for older browsers
      for (var k = 0; k < elements.length; k++) {
        elements[k].classList.add('animate-on-scroll--visible');
      }
    }
  }

  /* -------------------------------------------
     3. FAQ Accordion
     ------------------------------------------- */
  function initFAQ() {
    var faqItems = document.querySelectorAll('.faq__item');
    if (!faqItems.length) return;

    for (var i = 0; i < faqItems.length; i++) {
      var question = faqItems[i].querySelector('.faq__question');
      if (!question) continue;

      question.addEventListener('click', handleFAQClick);
      question.addEventListener('keydown', handleFAQKeydown);
    }

    function handleFAQClick(e) {
      var button = e.currentTarget;
      var item = button.closest('.faq__item');
      var answer = item.querySelector('.faq__answer');
      var isOpen = item.classList.contains('faq__item--open');

      // Close all other items
      var allItems = document.querySelectorAll('.faq__item--open');
      for (var j = 0; j < allItems.length; j++) {
        if (allItems[j] !== item) {
          allItems[j].classList.remove('faq__item--open');
          var otherBtn = allItems[j].querySelector('.faq__question');
          var otherAnswer = allItems[j].querySelector('.faq__answer');
          if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
          if (otherAnswer) otherAnswer.setAttribute('aria-hidden', 'true');
        }
      }

      // Toggle clicked item
      if (isOpen) {
        item.classList.remove('faq__item--open');
        button.setAttribute('aria-expanded', 'false');
        if (answer) answer.setAttribute('aria-hidden', 'true');
      } else {
        item.classList.add('faq__item--open');
        button.setAttribute('aria-expanded', 'true');
        if (answer) answer.setAttribute('aria-hidden', 'false');
      }
    }

    function handleFAQKeydown(e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        e.currentTarget.click();
      }
    }
  }

  /* -------------------------------------------
     4. Stats Counter Animation
     ------------------------------------------- */
  function initStatsCounter() {
    var statNumbers = document.querySelectorAll('.stats__item-number[data-target]');
    if (!statNumbers.length) return;

    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    function animateCounter(el) {
      var target = parseInt(el.getAttribute('data-target'), 10);
      var suffix = el.getAttribute('data-suffix') || '';
      var duration = 2000;
      var startTime = null;

      if (prefersReducedMotion.matches) {
        el.textContent = formatNumber(target) + suffix;
        return;
      }

      function step(timestamp) {
        if (!startTime) startTime = timestamp;
        var progress = Math.min((timestamp - startTime) / duration, 1);
        // Ease out quad
        var easedProgress = 1 - (1 - progress) * (1 - progress);
        var current = Math.floor(easedProgress * target);
        el.textContent = formatNumber(current) + (progress >= 1 ? suffix : '');
        if (progress < 1) {
          requestAnimationFrame(step);
        }
      }

      requestAnimationFrame(step);
    }

    function formatNumber(num) {
      return num.toLocaleString('en-US');
    }

    if ('IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        for (var i = 0; i < entries.length; i++) {
          if (entries[i].isIntersecting) {
            animateCounter(entries[i].target);
            observer.unobserve(entries[i].target);
          }
        }
      }, { threshold: 0.3 });

      for (var i = 0; i < statNumbers.length; i++) {
        observer.observe(statNumbers[i]);
      }
    } else {
      for (var j = 0; j < statNumbers.length; j++) {
        var target = parseInt(statNumbers[j].getAttribute('data-target'), 10);
        var suffix = statNumbers[j].getAttribute('data-suffix') || '';
        statNumbers[j].textContent = formatNumber(target) + suffix;
      }
    }
  }

  /* -------------------------------------------
     5. Smooth Scroll for Anchor Links
     ------------------------------------------- */
  function initSmoothScroll() {
    var anchors = document.querySelectorAll('a[href*="#"]');

    for (var i = 0; i < anchors.length; i++) {
      anchors[i].addEventListener('click', function (e) {
        var href = this.getAttribute('href');
        if (!href || href === '#') return;

        // Only handle same-page anchors
        var hashIndex = href.indexOf('#');
        if (hashIndex === -1) return;

        var path = href.substring(0, hashIndex);
        var hash = href.substring(hashIndex);

        // Check if linking to same page
        if (path && path !== '' && !isSamePage(path)) return;

        var targetEl = document.querySelector(hash);
        if (targetEl) {
          e.preventDefault();
          var navHeight = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--nav-height'), 10) || 72;
          var targetPosition = targetEl.getBoundingClientRect().top + window.pageYOffset - navHeight - 16;

          window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
          });

          // Update URL hash without jumping
          if (history.pushState) {
            history.pushState(null, null, hash);
          }
        }
      });
    }

    function isSamePage(path) {
      var currentPath = window.location.pathname;
      var linkPath = path.replace(/^\.\//, '');
      return currentPath.endsWith(linkPath);
    }
  }

  /* -------------------------------------------
     6. Contact Form Validation & Handling
     ------------------------------------------- */
  function initContactForm() {
    var form = document.getElementById('contact-form');
    if (!form) return;

    var submitBtn = document.getElementById('submit-btn');
    var submitText = form.querySelector('.contact-form__submit-text');
    var submitLoading = form.querySelector('.contact-form__submit-loading');
    var successMessage = document.getElementById('form-success');
    var messageTextarea = document.getElementById('message');
    var messageCount = document.getElementById('message-count');

    // Character counter for message
    if (messageTextarea && messageCount) {
      messageTextarea.addEventListener('input', function () {
        var count = messageTextarea.value.length;
        messageCount.textContent = count + ' / 2000';
      });
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      // Clear previous errors
      clearErrors();

      // Validate
      var isValid = validateForm();
      if (!isValid) return;

      // Show loading state
      if (submitBtn) submitBtn.disabled = true;
      if (submitText) submitText.style.display = 'none';
      if (submitLoading) submitLoading.style.display = 'inline';

      // Simulate submission (no backend)
      setTimeout(function () {
        form.style.display = 'none';
        if (successMessage) successMessage.style.display = 'block';
        if (submitBtn) submitBtn.disabled = false;
        if (submitText) submitText.style.display = 'inline';
        if (submitLoading) submitLoading.style.display = 'none';
      }, 1500);
    });

    function validateForm() {
      var valid = true;

      var firstName = document.getElementById('first-name');
      var lastName = document.getElementById('last-name');
      var email = document.getElementById('email');
      var subject = document.getElementById('subject');
      var message = document.getElementById('message');
      var consent = document.getElementById('consent');

      if (firstName && !firstName.value.trim()) {
        showError('first-name-error', 'First name is required.');
        valid = false;
      }

      if (lastName && !lastName.value.trim()) {
        showError('last-name-error', 'Last name is required.');
        valid = false;
      }

      if (email) {
        var emailValue = email.value.trim();
        if (!emailValue) {
          showError('email-error', 'Email address is required.');
          valid = false;
        } else if (!isValidEmail(emailValue)) {
          showError('email-error', 'Please enter a valid email address.');
          valid = false;
        }
      }

      if (subject && !subject.value) {
        showError('subject-error', 'Please select a subject.');
        valid = false;
      }

      if (message && !message.value.trim()) {
        showError('message-error', 'Message is required.');
        valid = false;
      }

      if (consent && !consent.checked) {
        // Highlight the checkbox area
        var checkboxGroup = consent.closest('.form-group--checkbox');
        if (checkboxGroup) {
          checkboxGroup.style.outline = '2px solid var(--color-danger)';
          checkboxGroup.style.outlineOffset = '4px';
          checkboxGroup.style.borderRadius = 'var(--radius-sm)';
          setTimeout(function () {
            checkboxGroup.style.outline = '';
            checkboxGroup.style.outlineOffset = '';
          }, 3000);
        }
        valid = false;
      }

      return valid;
    }

    function showError(errorId, message) {
      var errorEl = document.getElementById(errorId);
      if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.add('form-error--visible');
      }
    }

    function clearErrors() {
      var errors = form.querySelectorAll('.form-error');
      for (var i = 0; i < errors.length; i++) {
        errors[i].textContent = '';
        errors[i].classList.remove('form-error--visible');
      }
    }

    function isValidEmail(email) {
      // Basic email validation regex
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
  }

  /* -------------------------------------------
     7. Pricing Toggle (if on pricing page)
     ------------------------------------------- */
  function initPricingFAQ() {
    // Pricing page also has FAQ sections, handled by the generic FAQ init
    // This handles any pricing-specific toggle if needed in the future
  }

  /* -------------------------------------------
     Initialize All Modules
     ------------------------------------------- */
  function init() {
    initNavigation();
    initScrollAnimations();
    initFAQ();
    initStatsCounter();
    initSmoothScroll();
    initContactForm();
    initPricingFAQ();
  }

  // Run when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
