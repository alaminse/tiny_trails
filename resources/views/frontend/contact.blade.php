@extends('frontend.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('frontend/css/contact.css') }}">
@endsection
@section('content')

  <!-- Contact Hero -->
  <section class="hero" style="min-height: auto; padding-top: calc(var(--nav-height) + var(--space-16)); padding-bottom: var(--space-12);" aria-labelledby="contact-title">
    <div class="container text-center" style="max-width: var(--container-narrow);">
      <span class="section-label">Get in Touch</span>
      <h1 id="contact-title" class="section-title" style="font-size: var(--font-size-5xl);">We&rsquo;d Love to Hear From You</h1>
      <p class="section-subtitle">Have questions about our service, GPS devices, or subscription plans? Our team is here to help.</p>
    </div>
  </section>

  <!-- Contact Content -->
  <section class="contact-content" aria-label="Contact information and form">
    <div class="container">
      <div class="contact-grid">

        <!-- Contact Info Cards -->
        <div class="contact-info">
          <div class="contact-card animate-on-scroll">
            <div class="contact-card__icon" aria-hidden="true">&#x1F4DE;</div>
            <h3 class="contact-card__title">Call Us</h3>
            <p class="contact-card__text">Mon&ndash;Fri 7am&ndash;7pm, Sat 8am&ndash;4pm</p>
            <a href="tel:+18005551234" class="contact-card__link">1-800-555-1234</a>
          </div>

          <div class="contact-card animate-on-scroll" data-delay="100">
            <div class="contact-card__icon" aria-hidden="true">&#x2709;</div>
            <h3 class="contact-card__title">Email Us</h3>
            <p class="contact-card__text">We respond within 4 business hours</p>
            <a href="mailto:hello@tinytrails.com" class="contact-card__link">hello@tinytrails.com</a>
          </div>

          <div class="contact-card animate-on-scroll" data-delay="200">
            <div class="contact-card__icon" aria-hidden="true">&#x1F198;</div>
            <h3 class="contact-card__title">Emergency / SOS</h3>
            <p class="contact-card__text">24/7 safety coordination hotline for active riders</p>
            <a href="tel:+18005555678" class="contact-card__link">1-800-555-5678</a>
          </div>

          <div class="contact-card animate-on-scroll" data-delay="300">
            <div class="contact-card__icon" aria-hidden="true">&#x1F4CD;</div>
            <h3 class="contact-card__title">Visit Us</h3>
            <p class="contact-card__text">TinyTrails HQ</p>
            <address class="contact-card__address">
              123 Safety Lane, Suite 200<br>
              San Francisco, CA 94102
            </address>
          </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-wrapper animate-on-scroll">
          <h2 class="contact-form__heading">Send Us a Message</h2>
          <p class="contact-form__subheading">Fill out the form below and we&rsquo;ll get back to you within 4 business hours.</p>

          <form class="contact-form" id="contact-form" action="self" method="POST" novalidate>
            <div class="form-row">
              <div class="form-group">
                <label for="first-name" class="form-label">First Name <span class="form-required" aria-label="required">*</span></label>
                <input type="text" id="first-name" name="firstName" class="form-input" required autocomplete="given-name" placeholder="Jane">
                <span class="form-error" id="first-name-error" role="alert" aria-live="polite"></span>
              </div>
              <div class="form-group">
                <label for="last-name" class="form-label">Last Name <span class="form-required" aria-label="required">*</span></label>
                <input type="text" id="last-name" name="lastName" class="form-input" required autocomplete="family-name" placeholder="Doe">
                <span class="form-error" id="last-name-error" role="alert" aria-live="polite"></span>
              </div>
            </div>

            <div class="form-group">
              <label for="email" class="form-label">Email Address <span class="form-required" aria-label="required">*</span></label>
              <input type="email" id="email" name="email" class="form-input" required autocomplete="email" placeholder="jane@example.com">
              <span class="form-error" id="email-error" role="alert" aria-live="polite"></span>
            </div>

            <div class="form-group">
              <label for="phone" class="form-label">Phone Number</label>
              <input type="tel" id="phone" name="phone" class="form-input" autocomplete="tel" placeholder="(555) 123-4567">
            </div>

            <div class="form-group">
              <label for="subject" class="form-label">Subject <span class="form-required" aria-label="required">*</span></label>
              <select id="subject" name="subject" class="form-input form-select" required>
                <option value="" disabled selected>Select a topic&hellip;</option>
                <option value="general">General Inquiry</option>
                <option value="pricing">Plans &amp; Pricing</option>
                <option value="devices">GPS Devices</option>
                <option value="safety">Safety &amp; Trust</option>
                <option value="support">Technical Support</option>
                <option value="partnership">Partnership / Business</option>
              </select>
              <span class="form-error" id="subject-error" role="alert" aria-live="polite"></span>
            </div>

            <div class="form-group">
              <label for="message" class="form-label">Message <span class="form-required" aria-label="required">*</span></label>
              <textarea id="message" name="message" class="form-input form-textarea" required rows="5" placeholder="Tell us how we can help&hellip;" maxlength="2000"></textarea>
              <div class="form-meta">
                <span class="form-error" id="message-error" role="alert" aria-live="polite"></span>
                <span class="form-char-count" id="message-count">0 / 2000</span>
              </div>
            </div>

            <div class="form-group form-group--checkbox">
              <input type="checkbox" id="consent" name="consent" class="form-checkbox" required>
              <label for="consent" class="form-label form-label--checkbox">
                I agree to the <a href="#">Privacy Policy</a> and consent to TinyTrails contacting me regarding my inquiry.
                <span class="form-required" aria-label="required">*</span>
              </label>
            </div>

            <button type="submit" class="btn btn--primary btn--lg contact-form__submit" id="submit-btn">
              <span class="contact-form__submit-text">Send Message</span>
              <span class="contact-form__submit-loading" aria-hidden="true" style="display: none;">Sending&hellip;</span>
            </button>

            <!-- Success Message -->
            <div class="form-success" id="form-success" role="status" aria-live="polite" style="display: none;">
              <div class="form-success__icon" aria-hidden="true">&#x2714;</div>
              <h3 class="form-success__title">Message Sent!</h3>
              <p class="form-success__text">Thank you for reaching out. We&rsquo;ll get back to you within 4 business hours.</p>
            </div>
          </form>
        </div>

      </div>
    </div>
  </section>

  <!-- FAQ Reference -->
  <section class="features" style="padding: var(--space-12) 0;" aria-labelledby="quick-help-title">
    <div class="container text-center">
      <h2 id="quick-help-title" class="section-title" style="font-size: var(--font-size-3xl);">Quick Help</h2>
      <p class="section-subtitle">Find instant answers to common questions.</p>
      <div class="features__grid" style="max-width: 800px; margin-left: auto; margin-right: auto; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
        <a href="{{ route('frontend.home') }}#faq" class="feature-card feature-card--link animate-on-scroll">
          <div class="feature-card__icon feature-card__icon--blue" aria-hidden="true">&#x2753;</div>
          <h3 class="feature-card__title">General FAQ</h3>
          <p class="feature-card__description">Common questions about our service, scheduling, and GPS devices.</p>
        </a>
        <a href="{{ route('frontend.pricing') }}#pricing-faq" class="feature-card feature-card--link animate-on-scroll" data-delay="100">
          <div class="feature-card__icon feature-card__icon--green" aria-hidden="true">&#x1F4B0;</div>
          <h3 class="feature-card__title">Pricing FAQ</h3>
          <p class="feature-card__description">Questions about plans, free devices, subscriptions, and IoT features.</p>
        </a>
        <a href="{{ route('frontend.safety') }}" class="feature-card feature-card--link animate-on-scroll" data-delay="200">
          <div class="feature-card__icon feature-card__icon--orange" aria-hidden="true">&#x1F6E1;</div>
          <h3 class="feature-card__title">Safety Details</h3>
          <p class="feature-card__description">Driver vetting, vehicle inspections, tracking technology, and emergency response.</p>
        </a>
      </div>
    </div>
  </section>
@endsection
