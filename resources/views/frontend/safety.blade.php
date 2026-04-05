@extends('frontend.app')
@section('content')

  <!-- Safety Hero -->
  <section class="hero" style="min-height: auto; padding-top: calc(var(--nav-height) + var(--space-16)); padding-bottom: var(--space-16);" aria-labelledby="safety-title">
    <div class="container text-center" style="max-width: var(--container-narrow);">
      <span class="section-label">Safety First</span>
      <h1 id="safety-title" class="section-title" style="font-size: var(--font-size-5xl);">Your Child&rsquo;s Safety Is Our Mission</h1>
      <p class="section-subtitle">Every decision we make &mdash; from driver vetting to GPS technology &mdash; is designed to keep your child safe and give you peace of mind.</p>
    </div>
  </section>

  <!-- Safety Stats -->
  <section class="stats" aria-label="Safety statistics">
    <div class="container">
      <div class="stats__grid">
        <div class="stats__item">
          <div class="stats__item-number">99.9%</div>
          <div class="stats__item-label">Rides Without Incident</div>
        </div>
        <div class="stats__item">
          <div class="stats__item-number">12-Point</div>
          <div class="stats__item-label">Driver Verification</div>
        </div>
        <div class="stats__item">
          <div class="stats__item-number">&lt;60s</div>
          <div class="stats__item-label">SOS Response Time</div>
        </div>
        <div class="stats__item">
          <div class="stats__item-number">24/7</div>
          <div class="stats__item-label">Safety Monitoring</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Driver Safety -->
  <section class="features" aria-labelledby="driver-safety-title">
    <div class="container text-center">
      <span class="section-label">Driver Verification</span>
      <h2 id="driver-safety-title" class="section-title">Our 12-Point Driver Screening</h2>
      <p class="section-subtitle">We accept fewer than 15% of driver applicants. Every TinyTrails driver passes our comprehensive verification process.</p>

      <div class="features__grid">
        <div class="feature-card animate-on-scroll">
          <div class="feature-card__icon feature-card__icon--blue" aria-hidden="true">&#x1F50D;</div>
          <h3 class="feature-card__title">Criminal Background Check</h3>
          <p class="feature-card__description">National and county-level criminal background checks with continuous monitoring for any new offenses.</p>
        </div>
        <div class="feature-card animate-on-scroll" data-delay="100">
          <div class="feature-card__icon feature-card__icon--teal" aria-hidden="true">&#x1F697;</div>
          <h3 class="feature-card__title">Driving Record Review</h3>
          <p class="feature-card__description">Full MVR check requiring a clean driving record. Zero tolerance for DUIs, reckless driving, or major violations.</p>
        </div>
        <div class="feature-card animate-on-scroll" data-delay="200">
          <div class="feature-card__icon feature-card__icon--green" aria-hidden="true">&#x1F9EA;</div>
          <h3 class="feature-card__title">Drug &amp; Alcohol Screening</h3>
          <p class="feature-card__description">Pre-employment and random drug testing throughout employment. Zero tolerance policy for any substance use.</p>
        </div>
        <div class="feature-card animate-on-scroll" data-delay="100">
          <div class="feature-card__icon feature-card__icon--orange" aria-hidden="true">&#x1F393;</div>
          <h3 class="feature-card__title">Child Safety Certification</h3>
          <p class="feature-card__description">Mandatory child safety and first aid certification. CPR training and age-appropriate interaction protocols.</p>
        </div>
        <div class="feature-card animate-on-scroll" data-delay="200">
          <div class="feature-card__icon feature-card__icon--purple" aria-hidden="true">&#x1F6E0;</div>
          <h3 class="feature-card__title">Vehicle Inspection</h3>
          <p class="feature-card__description">Multi-point vehicle safety inspection including child locks, seat belts, car seats, and insurance verification.</p>
        </div>
        <div class="feature-card animate-on-scroll" data-delay="300">
          <div class="feature-card__icon feature-card__icon--red" aria-hidden="true">&#x1F4F9;</div>
          <h3 class="feature-card__title">In-Ride Monitoring</h3>
          <p class="feature-card__description">Optional in-ride dashcam for full transparency. All rides tracked and logged for accountability.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Technology Safety -->
  <section class="iot-value" aria-labelledby="tech-safety-title">
    <div class="container iot-value__inner">
      <div class="iot-value__content">
        <span class="section-label" style="background: rgba(255,255,255,0.15); color: white;">IoT Safety Technology</span>
        <h2 id="tech-safety-title">Technology That Protects</h2>
        <p>Our GPS-enabled IoT devices provide layers of protection beyond the ride itself. From real-time tracking to emergency response, technology is at the core of our safety promise.</p>
        <p>All data is encrypted end-to-end using AES-256 encryption. Our cloud infrastructure meets SOC 2 Type II compliance standards. Your family&rsquo;s location data is never shared or sold.</p>
      </div>
      <div class="iot-value__features">
        <div class="iot-value__feature animate-on-scroll">
          <div class="iot-value__feature-icon" aria-hidden="true">&#x1F4CD;</div>
          <p class="iot-value__feature-title">Real-Time GPS</p>
          <p class="iot-value__feature-text">Sub-meter accuracy tracking updates every 10 seconds during active rides.</p>
        </div>
        <div class="iot-value__feature animate-on-scroll" data-delay="100">
          <div class="iot-value__feature-icon" aria-hidden="true">&#x1F6A7;</div>
          <p class="iot-value__feature-title">Geofencing</p>
          <p class="iot-value__feature-text">Automatic alerts when your child enters or leaves designated safe zones.</p>
        </div>
        <div class="iot-value__feature animate-on-scroll" data-delay="200">
          <div class="iot-value__feature-icon" aria-hidden="true">&#x1F198;</div>
          <p class="iot-value__feature-title">SOS Emergency</p>
          <p class="iot-value__feature-text">One-press emergency button with under-60-second response time to parents and safety team.</p>
        </div>
        <div class="iot-value__feature animate-on-scroll" data-delay="300">
          <div class="iot-value__feature-icon" aria-hidden="true">&#x1F512;</div>
          <p class="iot-value__feature-title">Encrypted Data</p>
          <p class="iot-value__feature-text">AES-256 encryption for all location data. SOC 2 compliant infrastructure. No data sharing.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Safety Process -->
  <section class="how-it-works" aria-labelledby="safety-process-title">
    <div class="container text-center">
      <span class="section-label">Our Safety Process</span>
      <h2 id="safety-process-title" class="section-title">What Happens During Every Ride</h2>
      <p class="section-subtitle">A multi-layered safety protocol runs from the moment a ride is scheduled to the second your child is safely delivered.</p>

      <div class="how-it-works__steps">
        <div class="step animate-on-scroll">
          <div class="step__number">1</div>
          <div class="step__icon" aria-hidden="true">&#x2713;</div>
          <h3 class="step__title">Pre-Ride Verification</h3>
          <p class="step__description">Driver identity verified via app. Vehicle safety check confirmed. Route optimized for safety. Parent notified of driver details and ETA.</p>
        </div>
        <div class="step animate-on-scroll" data-delay="200">
          <div class="step__number">2</div>
          <div class="step__icon" aria-hidden="true">&#x1F4CD;</div>
          <h3 class="step__title">Active Ride Monitoring</h3>
          <p class="step__description">Real-time GPS tracking active. Route deviation detection. Speed monitoring. Parent can view live map. Driver cannot deviate from approved route.</p>
        </div>
        <div class="step animate-on-scroll" data-delay="400">
          <div class="step__number">3</div>
          <div class="step__icon" aria-hidden="true">&#x1F6E1;</div>
          <h3 class="step__title">Safe Arrival Confirmation</h3>
          <p class="step__description">Arrival notification sent to parent. Geofence confirmation at destination. Trip logged and recorded. Driver rated for quality assurance.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta" aria-labelledby="safety-cta-title">
    <div class="container">
      <h2 id="safety-cta-title">Safety You Can Trust. Technology You Can See.</h2>
      <p>Experience the peace of mind that comes with GPS-tracked, fully monitored child transportation.</p>
      <div class="cta__actions">
        <a href="{{ route('frontend.pricing') }}" class="btn btn--white btn--lg">See Plans &amp; Pricing</a>
        <a href="{{ route('frontend.contact') }}" class="btn btn--outline-white btn--lg">Contact Our Safety Team</a>
      </div>
    </div>
  </section>
@endsection
