@extends('frontend.app')

@section('css')
   <style>
    /* ============================================
   TinyTrails — Delete Account Page
   Matches main design system (no Bootstrap)
   ============================================ */

/* ── Contact Grid ── */
.contact-content {
  padding: var(--space-16) 0;
  background: var(--color-bg);
}

.contact-grid {
  display: grid;
  grid-template-columns: 1fr 1.6fr;
  gap: var(--space-12);
  align-items: start;
}

/* ── Info Cards ── */
.contact-info {
  display: flex;
  flex-direction: column;
  gap: var(--space-4);
}

.contact-card {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  transition: all var(--transition-base);
}

.contact-card:hover {
  border-color: var(--color-primary);
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}

.contact-card__icon {
  font-size: 1.75rem;
  margin-bottom: var(--space-3);
  display: block;
}

.contact-card__title {
  font-size: var(--font-size-base);
  font-weight: 700;
  color: var(--color-text);
  margin-bottom: var(--space-2);
}

.contact-card__text {
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
  margin-bottom: var(--space-3);
  line-height: 1.6;
}

.contact-card__link {
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: var(--color-primary);
  text-decoration: none;
  transition: color var(--transition-fast);
}

.contact-card__link:hover {
  color: var(--color-primary-dark);
  text-decoration: underline;
}

/* ── Form Wrapper ── */
.contact-form-wrapper {
  background: var(--color-bg);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-2xl);
  padding: var(--space-10);
  box-shadow: var(--shadow-sm);
}

.contact-form__heading {
  font-family: var(--font-family-display);
  font-size: var(--font-size-2xl);
  font-weight: 700;
  color: var(--color-text);
  margin-bottom: var(--space-2);
}

.contact-form__subheading {
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
  margin-bottom: var(--space-6);
  line-height: 1.6;
}

/* ── Form Elements ── */
.contact-form {
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: var(--space-5);
}

.form-label {
  display: block;
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: var(--color-text);
  margin-bottom: var(--space-2);
}

.form-required {
  color: var(--color-danger);
  margin-left: 2px;
}

.form-input {
  width: 100%;
  padding: var(--space-3) var(--space-4);
  font-family: var(--font-family);
  font-size: var(--font-size-base);
  color: var(--color-text);
  background: var(--color-bg);
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-lg);
  transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
  appearance: none;
  -webkit-appearance: none;
  line-height: 1.5;
  box-sizing: border-box;
}

.form-input:hover {
  border-color: var(--color-border-dark);
}

.form-input:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgb(37 99 235 / 0.12);
}

.form-input::placeholder {
  color: var(--color-text-light);
  opacity: 0.7;
}

.form-input.is-invalid {
  border-color: var(--color-danger);
  box-shadow: 0 0 0 3px rgb(220 38 38 / 0.1);
}

.form-select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right var(--space-4) center;
  padding-right: var(--space-10);
  cursor: pointer;
}

.form-textarea {
  resize: vertical;
  min-height: 110px;
  line-height: 1.6;
}

.form-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: var(--space-2);
}

.form-char-count {
  font-size: var(--font-size-xs);
  color: var(--color-text-light);
}

.form-error {
  display: block;
  font-size: var(--font-size-xs);
  color: var(--color-danger);
  margin-top: var(--space-1);
}

/* ── Checkbox Group ── */
.form-group--checkbox {
  display: flex;
  align-items: flex-start;
  gap: var(--space-3);
  background: var(--color-bg-alt);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: var(--space-4) var(--space-5);
  margin-bottom: var(--space-6);
}

.form-checkbox {
  width: 18px;
  height: 18px;
  min-width: 18px;
  margin-top: 2px;
  accent-color: var(--color-primary);
  cursor: pointer;
}

.form-label--checkbox {
  font-size: var(--font-size-sm);
  font-weight: 400;
  color: var(--color-text);
  cursor: pointer;
  line-height: 1.6;
  margin-bottom: 0;
}

/* ── Submit Button ── */
.contact-form__submit {
  width: 100%;
  padding: var(--space-4) var(--space-6);
  font-family: var(--font-family);
  font-size: var(--font-size-base);
  font-weight: 700;
  color: white;
  background: var(--color-danger);
  border: 2px solid var(--color-danger);
  border-radius: var(--radius-xl);
  cursor: pointer;
  transition: all var(--transition-base);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: var(--space-2);
  line-height: 1.5;
}

.contact-form__submit:hover {
  background: #b91c1c;
  border-color: #b91c1c;
  transform: translateY(-1px);
  box-shadow: var(--shadow-lg);
}

.contact-form__submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.contact-form__submit-loading {
  display: none;
}

/* ── Cancel Link ── */
.delete-cancel-link {
  display: block;
  width: 100%;
  text-align: center;
  margin-top: var(--space-3);
  padding: var(--space-3) var(--space-6);
  font-family: var(--font-family);
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: var(--color-text-light);
  background: transparent;
  border: 1.5px solid var(--color-border);
  border-radius: var(--radius-xl);
  transition: all var(--transition-fast);
  text-decoration: none;
  cursor: pointer;
  box-sizing: border-box;
}

.delete-cancel-link:hover {
  background: var(--color-bg-alt);
  border-color: var(--color-border-dark);
  color: var(--color-text);
}

/* ── Warning Banner ── */
.delete-warning {
  display: flex;
  align-items: flex-start;
  gap: var(--space-3);
  background: #fef2f2;
  border-left: 4px solid var(--color-danger);
  border-radius: 0 var(--radius-lg) var(--radius-lg) 0;
  padding: var(--space-4) var(--space-5);
  margin-bottom: var(--space-6);
}

.delete-warning__icon {
  font-size: 1.1rem;
  flex-shrink: 0;
  margin-top: 1px;
}

.delete-warning__text {
  font-size: var(--font-size-sm);
  color: #991b1b;
  line-height: 1.6;
  margin: 0;
}

/* ── Section Title ── */
.delete-section-title {
  font-size: var(--font-size-base);
  font-weight: 700;
  color: var(--color-text);
  margin-bottom: var(--space-4);
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.delete-section-label {
  font-size: var(--font-size-xs);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--color-text-light);
  margin-bottom: var(--space-4);
  display: block;
}

/* ── Steps List ── */
.steps-list {
  list-style: none;
  padding: 0;
  margin: 0 0 var(--space-6);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl);
  overflow: hidden;
}

.steps-list__item {
  display: flex;
  align-items: flex-start;
  gap: var(--space-4);
  padding: var(--space-4) var(--space-5);
  border-bottom: 1px solid var(--color-border);
  background: var(--color-bg);
  transition: background var(--transition-fast);
}

.steps-list__item:last-child {
  border-bottom: none;
}

.steps-list__item:hover {
  background: var(--color-bg-alt);
}

.steps-list__num {
  width: 32px;
  height: 32px;
  min-width: 32px;
  background: var(--color-primary);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: var(--font-size-sm);
}

.steps-list__body {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.steps-list__body strong {
  font-size: var(--font-size-sm);
  font-weight: 700;
  color: var(--color-text);
}

.steps-list__body span {
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
  line-height: 1.5;
}

/* ── Data Info Box ── */
.data-info-box {
  background: var(--color-bg-alt);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-xl);
  padding: var(--space-5) var(--space-6);
  margin-bottom: var(--space-6);
}

.data-info-box__label {
  font-size: var(--font-size-xs);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--color-text-light);
  margin-bottom: var(--space-3);
  display: block;
}

.data-row {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-2) 0;
  font-size: var(--font-size-sm);
  line-height: 1.5;
}

.data-row--deleted { color: var(--color-danger); }
.data-row--kept    { color: var(--color-text-light); }

/* ── Alerts ── */
.alert-delete-success {
  background: #f0fdf4;
  border: 1.5px solid #bbf7d0;
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  text-align: center;
  margin-bottom: var(--space-6);
}

.alert-delete-success .success-icon {
  font-size: 2rem;
  display: block;
  margin-bottom: var(--space-2);
}

.alert-delete-success strong {
  font-size: var(--font-size-base);
  color: var(--color-text);
  display: block;
  margin-bottom: var(--space-2);
}

.alert-delete-success p {
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
  margin: 0;
}

.alert-delete-warning {
  background: #fefce8;
  border: 1.5px solid #fde047;
  border-radius: var(--radius-xl);
  padding: var(--space-4) var(--space-5);
  font-size: var(--font-size-sm);
  color: #713f12;
  margin-bottom: var(--space-6);
}

/* ── Divider ── */
.section-divider {
  border: none;
  border-top: 1px solid var(--color-border);
  margin: var(--space-6) 0;
}

/* ── Email Alternative ── */
.email-alt-box {
  background: var(--color-bg-alt);
  border: 1.5px dashed var(--color-border-dark);
  border-radius: var(--radius-xl);
  padding: var(--space-6);
  text-align: center;
  margin-top: var(--space-6);
}

.email-alt-box p {
  font-size: var(--font-size-sm);
  color: var(--color-text-light);
  margin-bottom: var(--space-2);
}

.email-alt-box p:first-child {
  font-weight: 600;
  color: var(--color-text);
}

.email-alt-box a {
  color: var(--color-primary);
  font-weight: 600;
  text-decoration: none;
}

.email-alt-box a:hover {
  text-decoration: underline;
}

/* ── Responsive ── */
@media (max-width: 1024px) {
  .contact-grid {
    grid-template-columns: 1fr;
    gap: var(--space-8);
  }
}

@media (max-width: 768px) {
  .contact-form-wrapper {
    padding: var(--space-6);
  }

  .steps-list__item {
    padding: var(--space-3) var(--space-4);
  }

  .data-info-box {
    padding: var(--space-4);
  }

  .delete-warning {
    flex-direction: column;
    gap: var(--space-2);
  }
}

@media (max-width: 480px) {
  .contact-form-wrapper {
    padding: var(--space-5) var(--space-4);
    border-radius: var(--radius-xl);
  }
}
   </style>
@endsection

@section('content')
    {{-- Hero --}}
    <section class="hero"
        style="min-height: auto; padding-top: calc(var(--nav-height) + var(--space-16)); padding-bottom: var(--space-12);"
        aria-labelledby="da-title">
        <div class="container text-center" style="max-width: var(--container-narrow);">
            <span class="section-label">Account Management</span>
            <h1 id="da-title" class="section-title" style="font-size: var(--font-size-5xl);">
                Delete Your Account
            </h1>
            <p class="section-subtitle">
                We&rsquo;re sorry to see you go. Please read the information below carefully before submitting your request.
            </p>
        </div>
    </section>

    {{-- Two-column layout --}}
    <div class="container">
        <div class="da-grid">

            {{-- Left: Info Cards --}}
            <div class="da-cards">
                <div class="da-card animate-on-scroll">
                    <div class="da-card__icon">⏳</div>
                    <h3 class="da-card__title">Processing Time</h3>
                    <p class="da-card__text">Your deletion request will be completed within 30 days of submission.</p>
                </div>
                <div class="da-card animate-on-scroll" data-delay="100">
                    <div class="da-card__icon">🛡️</div>
                    <h3 class="da-card__title">Data Deleted</h3>
                    <p class="da-card__text">Profile, trip history, saved locations, and all personal data will be
                        permanently removed.</p>
                </div>
                <div class="da-card animate-on-scroll" data-delay="200">
                    <div class="da-card__icon">⚠️</div>
                    <h3 class="da-card__title">Irreversible Action</h3>
                    <p class="da-card__text">Once deleted, your account and all associated data cannot be recovered under
                        any circumstances.</p>
                </div>
                <div class="da-card animate-on-scroll" data-delay="300">
                    <div class="da-card__icon">✉️</div>
                    <h3 class="da-card__title">Need Help Instead?</h3>
                    <p class="da-card__text">Before deleting, reach out — we may be able to resolve your issue.</p>
                    <a href="{{ route('frontend.contact') }}" class="da-card__link">Contact Support →</a>
                </div>
            </div>

            {{-- Right: Form Card --}}
            <div class="da-form-wrap animate-on-scroll">
                <h2>Submit Deletion Request</h2>
                <p>Account deletion is processed by our admin team. Submit the form below and we&rsquo;ll confirm via email
                    once completed.</p>

                {{-- Alerts --}}
                @if (session('success'))
                    <div class="da-alert-success" role="status">
                        <span class="da-alert-success__icon">✅</span>
                        <h3>Request Submitted</h3>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                @if (session('warning'))
                    <div class="da-alert-warning" role="alert">
                        <strong>⚠️ Notice:</strong> {{ session('warning') }}
                    </div>
                @endif

                {{-- Warning --}}
                <div class="da-warning" role="alert">
                    <span class="da-warning__icon">⚠️</span>
                    <p><strong>This action is permanent and irreversible.</strong> Once deleted, all your data will be
                        removed and cannot be recovered.</p>
                </div>

                {{-- How it works --}}
                <p class="da-section-title">How It Works</p>
                <ul class="da-steps">
                    <li>
                        <span class="da-steps__num">1</span>
                        <div class="da-steps__body">
                            <strong>Submit your request</strong>
                            <span>Fill out the form below with your registered email and reason.</span>
                        </div>
                    </li>
                    <li>
                        <span class="da-steps__num">2</span>
                        <div class="da-steps__body">
                            <strong>Identity verification</strong>
                            <span>Our admin team verifies your identity using your registered email.</span>
                        </div>
                    </li>
                    <li>
                        <span class="da-steps__num">3</span>
                        <div class="da-steps__body">
                            <strong>Confirmation email</strong>
                            <span>You&rsquo;ll receive an email once your account has been successfully deleted.</span>
                        </div>
                    </li>
                    <li>
                        <span class="da-steps__num">4</span>
                        <div class="da-steps__body">
                            <strong>Deletion complete</strong>
                            <span>All personal data is permanently removed within 30 days of your request.</span>
                        </div>
                    </li>
                </ul>

                {{-- Data info --}}
                <p class="da-section-title">What Happens to Your Data</p>
                <div class="da-data-box">
                    <p class="da-data-label">Permanently Deleted</p>
                    <div class="da-data-row da-data-row--del"><span>🗑️</span> Your profile and personal information</div>
                    <div class="da-data-row da-data-row--del"><span>🗑️</span> Saved trips and travel history</div>
                    <div class="da-data-row da-data-row--del"><span>🗑️</span> Bookmarks and saved locations</div>
                    <div class="da-data-row da-data-row--del"><span>🗑️</span> App preferences and settings</div>
                    <p class="da-data-label" style="margin-top: var(--space-4);">May Be Retained (Legal / Compliance)</p>
                    <div class="da-data-row da-data-row--keep"><span>📜</span> Transaction records (if applicable) &ndash;
                        up to 7 years</div>
                    <div class="da-data-row da-data-row--keep"><span>📜</span> Anonymised usage analytics</div>
                </div>

                <hr class="da-divider">

                {{-- Form --}}
                <p class="da-section-title">Request Form</p>

                <form id="da-form" action="{{ route('frontend.delete-account.store') }}" method="POST" novalidate>
                    @csrf

                    {{-- Email --}}
                    <div class="da-field">
                        <label for="da-email" class="da-label">
                            Registered Email Address <span class="req">*</span>
                        </label>
                        <input type="email" id="da-email" name="email"
                            class="da-input {{ $errors->has('email') ? 'error' : '' }}" required autocomplete="email"
                            placeholder="you@example.com" value="{{ old('email') }}">
                        @error('email')
                            <span class="da-field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Reason --}}
                    <div class="da-field">
                        <label for="da-reason" class="da-label">
                            Reason for Deletion <span class="req">*</span>
                        </label>
                        <select id="da-reason" name="reason" class="da-select {{ $errors->has('reason') ? 'error' : '' }}"
                            required>
                            <option value="" disabled {{ old('reason') ? '' : 'selected' }}>Select a reason&hellip;
                            </option>
                            <option value="no-longer-use"
                                {{ old('reason') === 'no-longer-use' ? 'selected' : '' }}>I no longer use the app
                            </option>
                            <option value="privacy-concerns"
                                {{ old('reason') === 'privacy-concerns' ? 'selected' : '' }}>Privacy concerns
                            </option>
                            <option value="better-alternative"
                                {{ old('reason') === 'better-alternative' ? 'selected' : '' }}>Found a better
                                alternative</option>
                            <option value="too-many-notifications"
                                {{ old('reason') === 'too-many-notifications' ? 'selected' : '' }}>Too many notifications
                            </option>
                            <option value="other" {{ old('reason') === 'other' ? 'selected' : '' }}>
                                Other</option>
                        </select>
                        @error('reason')
                            <span class="da-field-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Comments --}}
                    <div class="da-field">
                        <label for="da-comments" class="da-label">
                            Additional Comments <span class="opt">(optional)</span>
                        </label>
                        <textarea id="da-comments" name="comments" class="da-textarea" rows="4"
                            placeholder="Tell us anything else&hellip;" maxlength="2000">{{ old('comments') }}</textarea>
                        <div class="da-field-meta">
                            <span class="da-char-count" id="da-count">0 / 2000</span>
                        </div>
                    </div>

                    {{-- Consent --}}
                    <div class="da-consent">
                        <input type="checkbox" id="da-consent" name="consent" {{ old('consent') ? 'checked' : '' }}
                            required>
                        <label for="da-consent">
                            I understand this action is <strong>permanent and irreversible</strong>,
                            and all my account data will be deleted.
                            <span class="req">*</span>
                        </label>
                    </div>
                    @error('consent')
                        <span class="da-field-error" style="margin: -12px 0 16px; display: block;"
                            role="alert">{{ $message }}</span>
                    @enderror

                    {{-- Submit --}}
                    <button type="submit" class="da-btn-submit" id="da-submit">
                        <span id="da-submit-text">🗑️&nbsp; Submit Deletion Request</span>
                        <span id="da-submit-loading" style="display:none;">Submitting&hellip;</span>
                    </button>

                    {{-- Cancel --}}
                    <a href="{{ route('frontend.home') }}" class="da-btn-cancel">
                        Cancel, keep my account
                    </a>

                </form>

                {{-- Email alternative --}}
                <div class="da-email-alt">
                    <p><strong>✉️ Prefer to email us directly?</strong></p>
                    <p>Send your request to <a href="mailto:support@tinytrails.net">support@tinytrails.net</a></p>
                    <p style="font-size: var(--font-size-xs); color: var(--color-text-light);">
                        Subject: <em>Account Deletion Request &ndash; [Your Email]</em>
                    </p>
                </div>

            </div>{{-- /.da-form-wrap --}}
        </div>{{-- /.da-grid --}}
    </div>{{-- /.container --}}

    {{-- Before You Go --}}
    <section class="features" style="padding: var(--space-12) 0;" aria-labelledby="da-help-title">
        <div class="container text-center">
            <h2 id="da-help-title" class="section-title" style="font-size: var(--font-size-3xl);">Before You Go</h2>
            <p class="section-subtitle">Maybe we can help resolve your issue instead.</p>
            <div class="features__grid"
                style="max-width: 800px; margin: 0 auto; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                <a href="{{ route('frontend.contact') }}" class="feature-card feature-card--link animate-on-scroll">
                    <div class="feature-card__icon feature-card__icon--blue">💬</div>
                    <h3 class="feature-card__title">Talk to Support</h3>
                    <p class="feature-card__description">Our team responds within 4 business hours and can help resolve
                        most issues.</p>
                </a>
                <a href="{{ route('frontend.home') }}#faq" class="feature-card feature-card--link animate-on-scroll"
                    data-delay="100">
                    <div class="feature-card__icon feature-card__icon--green">❓</div>
                    <h3 class="feature-card__title">Read the FAQ</h3>
                    <p class="feature-card__description">Find instant answers to common questions about our service and
                        features.</p>
                </a>
                <a href="{{ route('frontend.safety') }}" class="feature-card feature-card--link animate-on-scroll"
                    data-delay="200">
                    <div class="feature-card__icon feature-card__icon--orange">🛡️</div>
                    <h3 class="feature-card__title">Privacy &amp; Safety</h3>
                    <p class="feature-card__description">Learn how we protect your data and keep your information secure.
                    </p>
                </a>
            </div>
        </div>
    </section>
@endsection

@section('js')
    <script>
        // Character counter
        const textarea = document.getElementById('da-comments');
        const counter = document.getElementById('da-count');
        if (textarea && counter) {
            textarea.addEventListener('input', function() {
                counter.textContent = this.value.length + ' / 2000';
            });
        }

        // Loading state on submit
        const form = document.getElementById('da-form');
        const submitBtn = document.getElementById('da-submit');
        const submitText = document.getElementById('da-submit-text');
        const submitLoad = document.getElementById('da-submit-loading');

        if (form && submitBtn) {
            form.addEventListener('submit', function() {
                if (submitText) submitText.style.display = 'none';
                if (submitLoad) submitLoad.style.display = 'inline';
                submitBtn.disabled = true;
            });
        }
    </script>
@endsection
