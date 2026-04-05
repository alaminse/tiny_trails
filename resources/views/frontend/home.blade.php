@extends('frontend.app')
@section('content')
  <!-- ========== HERO ========== -->
  <section class="hero" aria-labelledby="hero-title">
    <div class="hero__bg-shapes" aria-hidden="true">
      <div class="hero__shape hero__shape--1"></div>
      <div class="hero__shape hero__shape--2"></div>
      <div class="hero__shape hero__shape--3"></div>
    </div>

    <div class="container hero__inner">
      <div class="hero__content">
        <div class="hero__badge">
          <span class="hero__badge-dot" aria-hidden="true"></span>
          Trusted by 5,000+ families nationwide
        </div>

        <h1 id="hero-title" class="hero__title">
          Safe Rides.<br>
          <span class="hero__title-highlight">Happy Kids.</span><br>
          Peace of Mind.
        </h1>

        <p class="hero__description">
          TinyTrails provides GPS-tracked drop-off and pickup services for your children.
          Know exactly where they are, every step of the way &mdash; from home to school and back.
        </p>

        <div class="hero__actions">
          <a href="{{ route('frontend.pricing') }}" class="btn btn--accent btn--lg">See Plans &amp; Pricing</a>
          <a href="{{ route('frontend.how_it_works') }}" class="btn btn--outline btn--lg">How It Works</a>
        </div>

        <div class="hero__stats">
          <div class="hero__stat">
            <div class="hero__stat-number">99.9%</div>
            <div class="hero__stat-label">On-time rate</div>
          </div>
          <div class="hero__stat">
            <div class="hero__stat-number">50K+</div>
            <div class="hero__stat-label">Rides completed</div>
          </div>
          <div class="hero__stat">
            <div class="hero__stat-number">4.9&#9733;</div>
            <div class="hero__stat-label">Parent rating</div>
          </div>
        </div>
      </div>

      <div class="hero__visual">
        <!-- Phone mockup -->
        <div class="hero__phone-mockup" aria-hidden="true">
          <div class="hero__phone-screen">
            <div class="hero__phone-header">
              <h4>TinyTrails</h4>
              <h3>Live Tracking</h3>
            </div>
            <div class="hero__phone-map">
              <div class="hero__phone-map-line"></div>
              <div class="hero__phone-map-dot"></div>
            </div>
            <div class="hero__phone-card">
              <div class="hero__phone-card-icon">&#x2713;</div>
              <div class="hero__phone-card-text">
                <strong>Emma is on the way</strong>
                ETA: 3 min to Lincoln Elementary
              </div>
            </div>
          </div>
        </div>

        <!-- Floating badges -->
        <div class="hero__float-badge hero__float-badge--gps">
          <span class="hero__float-badge-icon hero__float-badge-icon--green" aria-hidden="true">&#x1F4CD;</span>
          GPS Tracking
        </div>
        <div class="hero__float-badge hero__float-badge--safe">
          <span class="hero__float-badge-icon hero__float-badge-icon--blue" aria-hidden="true">&#x1F6E1;</span>
          Vetted Drivers
        </div>
        <div class="hero__float-badge hero__float-badge--sos">
          <span class="hero__float-badge-icon hero__float-badge-icon--red" aria-hidden="true">&#x1F198;</span>
          SOS Button
        </div>
      </div>
    </div>
  </section>

  <!-- ========== TRUST BAR ========== -->
  <section class="trust-bar" aria-label="Trusted partners">
    <div class="container">
      <p class="trust-bar__label">Trusted by schools &amp; organizations across the country</p>
      <div class="trust-bar__logos">
        <span class="trust-bar__logo">Lincoln Schools</span>
        <span class="trust-bar__logo">SafeKids Alliance</span>
        <span class="trust-bar__logo">EduTransport</span>
        <span class="trust-bar__logo">ParentFirst</span>
        <span class="trust-bar__logo">KidGuard</span>
      </div>
    </div>
  </section>

  <!-- ========== HOW IT WORKS ========== -->
  <section class="how-it-works" id="how-it-works" aria-labelledby="how-title">
    <div class="container text-center">
      <span class="section-label">How It Works</span>
      <h2 id="how-title" class="section-title">Getting Started Is Simple</h2>
      <p class="section-subtitle">Three easy steps to safe, tracked rides for your children. Set it up once, and enjoy peace of mind every day.</p>

      <div class="how-it-works__steps">
        <div class="step animate-on-scroll">
          <div class="step__number">1</div>
          <div class="step__icon" aria-hidden="true">&#x1F4F1;</div>
          <h3 class="step__title">Set Locations &amp; Schedule</h3>
          <p class="step__description">
            Add your child's school, home, and any other destinations. Set recurring or one-time pickup/drop-off schedules through our app.
          </p>
        </div>

        <div class="step animate-on-scroll" data-delay="200">
          <div class="step__number">2</div>
          <div class="step__icon" aria-hidden="true">&#x1F4E6;</div>
          <h3 class="step__title">Choose Your GPS Device</h3>
          <p class="step__description">
            Select from our range of IoT tracking devices &mdash; wearable bands, clip-on tags, or backpack trackers. Some plans include free hardware.
          </p>
        </div>

        <div class="step animate-on-scroll" data-delay="400">
          <div class="step__number">3</div>
          <div class="step__icon" aria-hidden="true">&#x1F6E1;</div>
          <h3 class="step__title">Track &amp; Relax</h3>
          <p class="step__description">
            Monitor your child in real-time on our app. Get instant notifications on arrival, departure, and route deviations. Always know they&rsquo;re safe.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== FEATURES ========== -->
  <section class="features" id="features" aria-labelledby="features-title">
    <div class="container text-center">
      <span class="section-label">Features</span>
      <h2 id="features-title" class="section-title">Everything You Need for Safe Rides</h2>
      <p class="section-subtitle">Our platform combines transportation logistics with IoT security technology to keep your children safe.</p>

      <div class="features__grid">
        <div class="feature-card animate-on-scroll">
          <div class="feature-card__icon feature-card__icon--blue" aria-hidden="true">&#x1F4CD;</div>
          <h3 class="feature-card__title">Real-Time GPS Tracking</h3>
          <p class="feature-card__description">Track your child's exact location on a live map. See route progress, speed, and estimated arrival time in real-time.</p>
        </div>

        <div class="feature-card animate-on-scroll" data-delay="100">
          <div class="feature-card__icon feature-card__icon--teal" aria-hidden="true">&#x1F6E1;</div>
          <h3 class="feature-card__title">Geofencing Alerts</h3>
          <p class="feature-card__description">Set safe zones around school, home, and activities. Get instant alerts when your child enters or leaves designated areas.</p>
        </div>

        <div class="feature-card animate-on-scroll" data-delay="200">
          <div class="feature-card__icon feature-card__icon--red" aria-hidden="true">&#x1F198;</div>
          <h3 class="feature-card__title">SOS Emergency Button</h3>
          <p class="feature-card__description">One-press SOS on the GPS device immediately alerts parents and our safety team. Help is always one tap away.</p>
        </div>

        <div class="feature-card animate-on-scroll" data-delay="100">
          <div class="feature-card__icon feature-card__icon--orange" aria-hidden="true">&#x1F514;</div>
          <h3 class="feature-card__title">Smart Notifications</h3>
          <p class="feature-card__description">Pickup started, arrived at school, driver approaching &mdash; stay informed with customizable push notifications.</p>
        </div>

        <div class="feature-card animate-on-scroll" data-delay="200">
          <div class="feature-card__icon feature-card__icon--green" aria-hidden="true">&#x2713;</div>
          <h3 class="feature-card__title">Vetted &amp; Verified Drivers</h3>
          <p class="feature-card__description">Every driver passes a 12-point background check including criminal, driving record, and child safety certification.</p>
        </div>

        <div class="feature-card animate-on-scroll" data-delay="300">
          <div class="feature-card__icon feature-card__icon--purple" aria-hidden="true">&#x1F4CA;</div>
          <h3 class="feature-card__title">Ride History &amp; Reports</h3>
          <p class="feature-card__description">View complete trip history with routes, times, and driver details. Export reports for your records anytime.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== GPS DEVICES ========== -->
  <section class="devices" id="devices" aria-labelledby="devices-title">
    <div class="container text-center">
      <span class="section-label">IoT Devices</span>
      <h2 id="devices-title" class="section-title">Choose Your Tracking Device</h2>
      <p class="section-subtitle">Our GPS devices pair seamlessly with the TinyTrails app. Select the one that fits your child&rsquo;s lifestyle.</p>

      <div class="devices__grid">
        <div class="device-card animate-on-scroll">
          <div class="device-card__visual" aria-hidden="true">&#x231A;</div>
          <h3 class="device-card__title">TinyBand</h3>
          <p class="device-card__subtitle">Wearable GPS wristband</p>
          <div class="device-card__features">
            <div class="device-card__feature">
              <span class="device-card__feature-check" aria-hidden="true">&#x2713;</span>
              Real-time location tracking
            </div>
            <div class="device-card__feature">
              <span class="device-card__feature-check" aria-hidden="true">&#x2713;</span>
              Water-resistant design
            </div>
            <div class="device-card__feature">
              <span class="device-card__feature-check" aria-hidden="true">&#x2713;</span>
              3-day battery life
            </div>
            <div class="device-card__feature">
              <span class="device-card__feature-check" aria-hidden="true">&#x2713;</span>
              SOS button built-in
            </div>
          </div>
          <p class="device-card__subtitle">Included free with Quarterly &amp; Annual plans</p>
        </div>

        <div class="device-card animate-on-scroll" data-delay="200">
          <span class="device-card__badge">Most Popular</span>
          <div class="device-card__visual" aria-hidden="true">&#x1F4CE;</div>
          <h3 class="device-card__title">TinyTag</h3>
          <p class="device-card__subtitle">Clip-on GPS tracker</p>
          <div class="device-card__features">
            <div class="device-card__feature">
              <span class="device-card__feature-check" aria-hidden="true">&#x2713;</span>
              Clips to clothing or bag
            </div>
            <div class="device-card__feature">
              <span class="device-card__feature-check" aria-hidden="true">&#x2713;</span>
              Ultra-lightweight (18g)
            </div>
            <div class="device-card__feature">
              <span class="device-card__feature-check" aria-hidden="true">&#x2713;</span>
              5-day battery life
            </div>
            <div class="device-card__feature">
              <span class="device-card__feature-check" aria-hidden="true">&#x2713;</span>
              Geofencing enabled
            </div>
          </div>
          <p class="device-card__subtitle">Included free with Quarterly &amp; Annual plans</p>
        </div>

        <div class="device-card animate-on-scroll" data-delay="400">
          <div class="device-card__visual" aria-hidden="true">&#x1F392;</div>
          <h3 class="device-card__title">TinyPack</h3>
          <p class="device-card__subtitle">Backpack GPS insert</p>
          <div class="device-card__features">
            <div class="device-card__feature">
              <span class="device-card__feature-check" aria-hidden="true">&#x2713;</span>
              Slides into any backpack
            </div>
            <div class="device-card__feature">
              <span class="device-card__feature-check" aria-hidden="true">&#x2713;</span>
              Tamper-proof design
            </div>
            <div class="device-card__feature">
              <span class="device-card__feature-check" aria-hidden="true">&#x2713;</span>
              7-day battery life
            </div>
            <div class="device-card__feature">
              <span class="device-card__feature-check" aria-hidden="true">&#x2713;</span>
              Full premium features
            </div>
          </div>
          <p class="device-card__subtitle">Included free with Annual plan</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== PRICING PREVIEW ========== -->
  <section class="pricing" id="pricing" aria-labelledby="pricing-title">
    <div class="container text-center">
      <span class="section-label">Plans &amp; Pricing</span>
      <h2 id="pricing-title" class="section-title">Simple, Transparent Pricing</h2>
      <p class="section-subtitle">More than just rides &mdash; every plan includes IoT-powered safety features that give you real-time visibility and control over your child&rsquo;s journey.</p>

      <div class="pricing__grid">
        <!-- Per-Trip -->
        <div class="pricing-card animate-on-scroll">
          <div class="pricing-card__header">
            <div class="pricing-card__icon" aria-hidden="true">&#x1F697;</div>
            <h3 class="pricing-card__name">Per-Trip</h3>
            <p class="pricing-card__description">Ultimate flexibility for occasional rides</p>
            <div class="pricing-card__price">
              <span class="pricing-card__currency">$</span>
              <span class="pricing-card__amount">14</span>
              <span class="pricing-card__period">/trip</span>
            </div>
          </div>
          <div class="pricing-card__features">
            <div class="pricing-card__feature-group">
              <p class="pricing-card__feature-group-title">Transport</p>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Single pickup or drop-off
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Vetted, background-checked driver
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Real-time ride tracking
              </div>
            </div>
            <div class="pricing-card__feature-group">
              <p class="pricing-card__feature-group-title">IoT &amp; Safety</p>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                App-based GPS tracking
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross" aria-hidden="true">&#x2717;</span>
                <span style="opacity:0.5">GPS device not included</span>
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross" aria-hidden="true">&#x2717;</span>
                <span style="opacity:0.5">No geofencing</span>
              </div>
            </div>
          </div>
          <div class="pricing-card__cta">
            <a href="{{ route('frontend.pricing') }}" class="btn btn--outline btn--full">Book a Ride</a>
          </div>
        </div>

        <!-- Monthly -->
        <div class="pricing-card animate-on-scroll" data-delay="100">
          <div class="pricing-card__header">
            <div class="pricing-card__icon" aria-hidden="true">&#x1F4C5;</div>
            <h3 class="pricing-card__name">Monthly</h3>
            <p class="pricing-card__description">For regular school commuters</p>
            <div class="pricing-card__price">
              <span class="pricing-card__currency">$</span>
              <span class="pricing-card__amount">89</span>
              <span class="pricing-card__period">/month</span>
            </div>
          </div>
          <div class="pricing-card__features">
            <div class="pricing-card__feature-group">
              <p class="pricing-card__feature-group-title">Transport</p>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Daily pickup &amp; drop-off (weekdays)
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Consistent assigned driver
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Schedule flexibility
              </div>
            </div>
            <div class="pricing-card__feature-group">
              <p class="pricing-card__feature-group-title">IoT &amp; Safety</p>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                App-based GPS tracking
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Basic trip history
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross" aria-hidden="true">&#x2717;</span>
                <span style="opacity:0.5">GPS device not included</span>
              </div>
            </div>
          </div>
          <div class="pricing-card__cta">
            <a href="{{ route('frontend.pricing') }}" class="btn btn--outline btn--full">Start Monthly</a>
          </div>
        </div>

        <!-- Quarterly — Featured -->
        <div class="pricing-card pricing-card--featured animate-on-scroll" data-delay="200">
          <span class="pricing-card__popular">Best Value</span>
          <div class="pricing-card__header">
            <div class="pricing-card__icon" aria-hidden="true">&#x2B50;</div>
            <h3 class="pricing-card__name">Quarterly</h3>
            <p class="pricing-card__description">Commit &amp; save with IoT included</p>
            <div class="pricing-card__price">
              <span class="pricing-card__currency">$</span>
              <span class="pricing-card__amount">69</span>
              <span class="pricing-card__period">/month</span>
            </div>
            <span class="pricing-card__savings">Save 22% vs. monthly</span>
          </div>
          <div class="pricing-card__iot-highlight">
            <p>&#x1F381; Free GPS Device Included (TinyBand or TinyTag)</p>
          </div>
          <div class="pricing-card__features">
            <div class="pricing-card__feature-group">
              <p class="pricing-card__feature-group-title">Transport</p>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Daily pickup &amp; drop-off (weekdays)
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Priority driver matching
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Multi-stop capability
              </div>
            </div>
            <div class="pricing-card__feature-group">
              <p class="pricing-card__feature-group-title">IoT &amp; Safety</p>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Free GPS tracking device
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Basic GPS location tracking
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Basic trip history
              </div>
            </div>
          </div>
          <div class="pricing-card__cta">
            <a href="{{ route('frontend.pricing') }}" class="btn btn--primary btn--full">Start Quarterly</a>
          </div>
        </div>

        <!-- Annual -->
        <div class="pricing-card animate-on-scroll" data-delay="300">
          <div class="pricing-card__header">
            <div class="pricing-card__icon" aria-hidden="true">&#x1F451;</div>
            <h3 class="pricing-card__name">Annual</h3>
            <p class="pricing-card__description">Maximum savings, premium IoT features</p>
            <div class="pricing-card__price">
              <span class="pricing-card__currency">$</span>
              <span class="pricing-card__amount">49</span>
              <span class="pricing-card__period">/month</span>
            </div>
            <span class="pricing-card__savings">Save 45% vs. monthly</span>
          </div>
          <div class="pricing-card__iot-highlight">
            <p>&#x1F451; Free Premium GPS Device + All Premium Features</p>
          </div>
          <div class="pricing-card__features">
            <div class="pricing-card__feature-group">
              <p class="pricing-card__feature-group-title">Transport</p>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Unlimited rides (weekdays)
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Dedicated driver assignment
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Weekend &amp; activity rides
              </div>
            </div>
            <div class="pricing-card__feature-group">
              <p class="pricing-card__feature-group-title">IoT &amp; Safety &#x2014; Premium</p>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Free premium GPS device (any model)
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Geofencing &amp; safe zone alerts
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                SOS emergency button
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Advanced alerts &amp; notifications
              </div>
              <div class="pricing-card__feature">
                <span class="pricing-card__feature-icon pricing-card__feature-icon--check" aria-hidden="true">&#x2713;</span>
                Location history timeline
              </div>
            </div>
          </div>
          <div class="pricing-card__cta">
            <a href="{{ route('frontend.pricing') }}" class="btn btn--accent btn--full">Start Annual</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== IoT VALUE PROPOSITION ========== -->
  <section class="iot-value" aria-labelledby="iot-title">
    <div class="container iot-value__inner">
      <div class="iot-value__content">
        <span class="section-label" style="background: rgba(255,255,255,0.15); color: white;">Why IoT Matters</span>
        <h2 id="iot-title">More Than Just a Ride.<br>It&rsquo;s Security You Can See.</h2>
        <p>
          Traditional transport services end at drop-off. TinyTrails continues protecting your child with GPS-enabled IoT devices that keep you connected 24/7. Our subscription isn&rsquo;t just for rides &mdash; it powers the technology that gives you real-time visibility, emergency response, and historical tracking for complete peace of mind.
        </p>
      </div>

      <div class="iot-value__features">
        <div class="iot-value__feature animate-on-scroll">
          <div class="iot-value__feature-icon" aria-hidden="true">&#x1F4CD;</div>
          <p class="iot-value__feature-title">Live Location</p>
          <p class="iot-value__feature-text">See your child on the map in real-time with sub-meter accuracy GPS tracking.</p>
        </div>
        <div class="iot-value__feature animate-on-scroll" data-delay="100">
          <div class="iot-value__feature-icon" aria-hidden="true">&#x1F6A7;</div>
          <p class="iot-value__feature-title">Geofencing</p>
          <p class="iot-value__feature-text">Create virtual boundaries. Get alerted when your child arrives at or leaves safe zones.</p>
        </div>
        <div class="iot-value__feature animate-on-scroll" data-delay="200">
          <div class="iot-value__feature-icon" aria-hidden="true">&#x1F6A8;</div>
          <p class="iot-value__feature-title">SOS Response</p>
          <p class="iot-value__feature-text">One-press emergency button connects instantly to parents and safety coordinators.</p>
        </div>
        <div class="iot-value__feature animate-on-scroll" data-delay="300">
          <div class="iot-value__feature-icon" aria-hidden="true">&#x1F4C8;</div>
          <p class="iot-value__feature-title">History Timeline</p>
          <p class="iot-value__feature-text">Review complete location history. Every route, every stop, every arrival time logged.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== STATS ========== -->
  <section class="stats" aria-label="TinyTrails statistics">
    <div class="container">
      <div class="stats__grid">
        <div class="stats__item">
          <div class="stats__item-number" data-target="50000">0</div>
          <div class="stats__item-label">Safe Rides Completed</div>
        </div>
        <div class="stats__item">
          <div class="stats__item-number" data-target="5000">0</div>
          <div class="stats__item-label">Happy Families</div>
        </div>
        <div class="stats__item">
          <div class="stats__item-number" data-target="200">0</div>
          <div class="stats__item-label">Partner Schools</div>
        </div>
        <div class="stats__item">
          <div class="stats__item-number" data-target="99" data-suffix=".9%">0</div>
          <div class="stats__item-label">Safety Record</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== TESTIMONIALS ========== -->
  <section class="testimonials" id="testimonials" aria-labelledby="testimonials-title">
    <div class="container text-center">
      <span class="section-label">Testimonials</span>
      <h2 id="testimonials-title" class="section-title">Trusted by Parents Like You</h2>
      <p class="section-subtitle">Hear from families who found peace of mind with TinyTrails GPS-tracked rides.</p>

      <div class="testimonials__grid">
        <div class="testimonial-card animate-on-scroll">
          <div class="testimonial-card__stars" aria-label="5 out of 5 stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
          <p class="testimonial-card__text">
            &ldquo;The GPS tracking gives me so much peace of mind. I can see exactly when my daughter arrives at school and get a notification the second she&rsquo;s picked up. It&rsquo;s worth every penny.&rdquo;
          </p>
          <div class="testimonial-card__author">
            <div class="testimonial-card__avatar" aria-hidden="true">SJ</div>
            <div>
              <p class="testimonial-card__name">Sarah Johnson</p>
              <p class="testimonial-card__role">Mom of 2 &bull; Annual Plan</p>
            </div>
          </div>
        </div>

        <div class="testimonial-card animate-on-scroll" data-delay="200">
          <div class="testimonial-card__stars" aria-label="5 out of 5 stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
          <p class="testimonial-card__text">
            &ldquo;We switched from a regular carpool to TinyTrails and the difference is night and day. The SOS button on the TinyBand gives my kids confidence and gives us security.&rdquo;
          </p>
          <div class="testimonial-card__author">
            <div class="testimonial-card__avatar" aria-hidden="true">MR</div>
            <div>
              <p class="testimonial-card__name">Michael Rivera</p>
              <p class="testimonial-card__role">Dad of 3 &bull; Quarterly Plan</p>
            </div>
          </div>
        </div>

        <div class="testimonial-card animate-on-scroll" data-delay="400">
          <div class="testimonial-card__stars" aria-label="5 out of 5 stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
          <p class="testimonial-card__text">
            &ldquo;The geofencing feature is brilliant. I get an alert the moment my son reaches school grounds and another when the driver starts the return trip. No more anxious waiting.&rdquo;
          </p>
          <div class="testimonial-card__author">
            <div class="testimonial-card__avatar" aria-hidden="true">AP</div>
            <div>
              <p class="testimonial-card__name">Aisha Patel</p>
              <p class="testimonial-card__role">Mom of 1 &bull; Annual Plan</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== FAQ ========== -->
  <section class="faq" id="faq" aria-labelledby="faq-title">
    <div class="container text-center">
      <span class="section-label">FAQ</span>
      <h2 id="faq-title" class="section-title">Frequently Asked Questions</h2>
      <p class="section-subtitle">Everything you need to know about TinyTrails, our GPS devices, and subscription plans.</p>

      <div class="faq__list" role="list">
        <div class="faq__item" role="listitem">
          <button class="faq__question" aria-expanded="false">
            How does the GPS tracking work?
            <svg class="faq__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
          </button>
          <div class="faq__answer" aria-hidden="true">
            <div class="faq__answer-inner">
              Our GPS devices use a combination of GPS, Wi-Fi, and cellular connectivity to provide accurate, real-time location data. The device communicates with our secure cloud platform, which sends updates to your TinyTrails app. Basic tracking is available through the app on all plans, while dedicated GPS hardware devices are included free with Quarterly and Annual subscriptions.
            </div>
          </div>
        </div>

        <div class="faq__item" role="listitem">
          <button class="faq__question" aria-expanded="false">
            What is included with the free GPS device?
            <svg class="faq__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
          </button>
          <div class="faq__answer" aria-hidden="true">
            <div class="faq__answer-inner">
              <strong>Quarterly plans</strong> include a free TinyBand or TinyTag with basic GPS location tracking and basic trip history. <strong>Annual plans</strong> include your choice of any GPS device (TinyBand, TinyTag, or TinyPack) with full premium features: geofencing, SOS button, advanced alerts, and complete location history timeline.
            </div>
          </div>
        </div>

        <div class="faq__item" role="listitem">
          <button class="faq__question" aria-expanded="false">
            Are the drivers background-checked?
            <svg class="faq__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
          </button>
          <div class="faq__answer" aria-hidden="true">
            <div class="faq__answer-inner">
              Absolutely. Every TinyTrails driver undergoes a comprehensive 12-point verification process including criminal background check, driving record review, drug screening, child safety certification, vehicle inspection, and ongoing monitoring. We accept fewer than 15% of applicants to maintain the highest safety standards.
            </div>
          </div>
        </div>

        <div class="faq__item" role="listitem">
          <button class="faq__question" aria-expanded="false">
            Can I set custom pickup/drop-off locations?
            <svg class="faq__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
          </button>
          <div class="faq__answer" aria-hidden="true">
            <div class="faq__answer-inner">
              Yes! Parents can set any locations through our app &mdash; school, home, grandparents' house, soccer practice, tutoring centers, and more. Subscription plans (Monthly and above) support recurring schedules, while Per-Trip lets you choose different locations each time. Quarterly+ plans support multi-stop capability.
            </div>
          </div>
        </div>

        <div class="faq__item" role="listitem">
          <button class="faq__question" aria-expanded="false">
            What happens if I cancel my subscription?
            <svg class="faq__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
          </button>
          <div class="faq__answer" aria-hidden="true">
            <div class="faq__answer-inner">
              You can cancel anytime. Your service continues until the end of your current billing period. GPS devices provided free with plans should be returned within 14 days of cancellation. Per-Trip users have no commitment &mdash; simply book when you need a ride.
            </div>
          </div>
        </div>

        <div class="faq__item" role="listitem">
          <button class="faq__question" aria-expanded="false">
            How does the SOS emergency button work?
            <svg class="faq__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
          </button>
          <div class="faq__answer" aria-hidden="true">
            <div class="faq__answer-inner">
              Available on Annual plan devices, the SOS button sends an immediate alert to all registered parent/guardian contacts and our 24/7 safety coordination team. The alert includes the child's exact GPS location, and our team initiates a response protocol within 60 seconds. This feature works independently of any active ride &mdash; providing protection even outside of TinyTrails trips.
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========== CTA ========== -->
  <section class="cta" aria-labelledby="cta-title">
    <div class="container">
      <h2 id="cta-title">Give Your Child a Safer Ride</h2>
      <p>Join thousands of families who trust TinyTrails for GPS-tracked, reliable transportation. Start with a single ride or commit to a plan and get a free GPS device.</p>
      <div class="cta__actions">
        <a href="{{ route('frontend.pricing') }}" class="btn btn--white btn--lg">View Plans &amp; Pricing</a>
        <a href="{{ route('frontend.contact') }}" class="btn btn--outline-white btn--lg">Talk to Our Team</a>
      </div>
    </div>
  </section>
@endsection
