@extends('frontend.app')
@section('content')

  <!-- Page Hero -->
  <section class="hero" style="min-height: auto; padding-top: calc(var(--nav-height) + var(--space-16)); padding-bottom: var(--space-16);" aria-labelledby="hiw-title">
    <div class="container text-center" style="max-width: var(--container-narrow);">
      <span class="section-label">How It Works</span>
      <h1 id="hiw-title" class="section-title" style="font-size: var(--font-size-5xl);">Safe Rides in 3 Simple Steps</h1>
      <p class="section-subtitle">Getting started with TinyTrails takes just minutes. Here&rsquo;s how we keep your child safe from door to door.</p>
    </div>
  </section>

  <!-- Step 1 -->
  <section class="how-detail" aria-labelledby="step1-title">
    <div class="container">
      <div class="how-detail__inner">
        <div class="how-detail__content animate-on-scroll">
          <div class="step__number">1</div>
          <h2 id="step1-title">Set Locations &amp; Schedule</h2>
          <p>Download the TinyTrails app and create your family profile. Add your children, their school, home address, and any other regular destinations like soccer practice, tutoring, or grandparents&rsquo; house.</p>
          <ul class="how-detail__list">
            <li>&#x2713; Add unlimited pickup &amp; drop-off locations</li>
            <li>&#x2713; Set recurring weekday schedules</li>
            <li>&#x2713; Modify or cancel rides with same-day flexibility</li>
            <li>&#x2713; Add multiple children under one account</li>
            <li>&#x2713; Set authorized pickup contacts</li>
          </ul>
          <a href="{{ route('frontend.pricing') }}" class="btn btn--primary">Choose a Plan</a>
        </div>
        <div class="how-detail__visual animate-on-scroll" data-delay="200" aria-hidden="true">
          <div class="how-detail__mockup">
            <div class="how-detail__mockup-screen">
              <div class="how-detail__mockup-header">&#x1F4F1; TinyTrails App</div>
              <div class="how-detail__mockup-content">
                <div class="how-detail__mockup-item">&#x1F3E0; Home &mdash; 123 Oak Street</div>
                <div class="how-detail__mockup-item how-detail__mockup-item--active">&#x1F3EB; Lincoln Elementary</div>
                <div class="how-detail__mockup-item">&#x26BD; Soccer Practice</div>
                <div class="how-detail__mockup-item">&#x1F475; Grandma&rsquo;s House</div>
              </div>
              <div class="how-detail__mockup-btn">+ Add Location</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Step 2 -->
  <section class="how-detail how-detail--alt" aria-labelledby="step2-title">
    <div class="container">
      <div class="how-detail__inner how-detail__inner--reverse">
        <div class="how-detail__content animate-on-scroll">
          <div class="step__number" style="background: linear-gradient(135deg, var(--color-teal), var(--color-teal-dark));">2</div>
          <h2 id="step2-title">Choose Your GPS Device</h2>
          <p>Select the IoT tracking device that fits your child&rsquo;s lifestyle. Quarterly plans include a free basic device; Annual plans include a premium device with all advanced features.</p>
          <ul class="how-detail__list">
            <li>&#x231A; <strong>TinyBand</strong> &mdash; Wearable wristband with SOS button</li>
            <li>&#x1F4CE; <strong>TinyTag</strong> &mdash; Lightweight clip-on for clothes or bags</li>
            <li>&#x1F392; <strong>TinyPack</strong> &mdash; Backpack insert, tamper-proof</li>
          </ul>
          <p>Devices pair automatically with the app via Bluetooth. Setup takes under 2 minutes.</p>
          <a href="{{ route('frontend.home') }}#devices" class="btn btn--outline">Compare Devices</a>
        </div>
        <div class="how-detail__visual animate-on-scroll" data-delay="200" aria-hidden="true">
          <div class="how-detail__device-showcase">
            <div class="how-detail__device">
              <div class="how-detail__device-icon">&#x231A;</div>
              <p>TinyBand</p>
            </div>
            <div class="how-detail__device how-detail__device--featured">
              <div class="how-detail__device-icon">&#x1F4CE;</div>
              <p>TinyTag</p>
              <span class="how-detail__device-badge">Popular</span>
            </div>
            <div class="how-detail__device">
              <div class="how-detail__device-icon">&#x1F392;</div>
              <p>TinyPack</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Step 3 -->
  <section class="how-detail" aria-labelledby="step3-title">
    <div class="container">
      <div class="how-detail__inner">
        <div class="how-detail__content animate-on-scroll">
          <div class="step__number" style="background: linear-gradient(135deg, var(--color-accent), var(--color-accent-dark));">3</div>
          <h2 id="step3-title">Track &amp; Relax</h2>
          <p>Once your schedule is set and device is paired, TinyTrails handles the rest. Our vetted drivers pick up and drop off your child while you monitor everything in real-time.</p>
          <ul class="how-detail__list">
            <li>&#x1F4CD; Live GPS location on an interactive map</li>
            <li>&#x1F514; Instant notifications: pickup, en route, arrived</li>
            <li>&#x1F6A7; Geofencing alerts when entering/leaving safe zones</li>
            <li>&#x1F198; SOS button for emergencies (Annual plan)</li>
            <li>&#x1F4CA; Trip history and route playback</li>
          </ul>
          <a href="{{ route('frontend.pricing') }}" class="btn btn--accent">Get Started</a>
        </div>
        <div class="how-detail__visual animate-on-scroll" data-delay="200" aria-hidden="true">
          <div class="how-detail__tracking-demo">
            <div class="how-detail__tracking-header">
              <span class="hero__badge-dot"></span>
              Live Tracking &mdash; Emma
            </div>
            <div class="how-detail__tracking-map">
              <div class="how-detail__tracking-route"></div>
              <div class="how-detail__tracking-dot"></div>
            </div>
            <div class="how-detail__tracking-status">
              <strong>&#x2713; On the way to school</strong><br>
              ETA: 3 minutes &bull; Driver: Maria K.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta" aria-labelledby="hiw-cta-title">
    <div class="container">
      <h2 id="hiw-cta-title">Ready to Get Started?</h2>
      <p>Choose a plan that works for your family and start tracking your child&rsquo;s rides today.</p>
      <div class="cta__actions">
        <a href="{{ route('frontend.pricing') }}" class="btn btn--white btn--lg">View Plans &amp; Pricing</a>
        <a href="{{ route('frontend.contact') }}" class="btn btn--outline-white btn--lg">Contact Us</a>
      </div>
    </div>
  </section>
@endsection
