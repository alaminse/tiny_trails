@extends('frontend.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('frontend/css/pricing.css') }}">
@endsection

@section('content')
    <!-- ========== PRICING HERO ========== -->
    <section class="pricing-hero" aria-labelledby="pricing-hero-title">
        <div class="container text-center">
            <span class="section-label">Plans &amp; Pricing</span>
            <h1 id="pricing-hero-title" class="pricing-hero__title">Invest in Safety, Not Just Rides</h1>
            <p class="pricing-hero__subtitle">
                Every TinyTrails plan combines safe, reliable transportation with IoT-powered GPS security.
                Choose the commitment level that&rsquo;s right for your family &mdash; and unlock more protection as you go.
            </p>
            <div class="pricing-hero__value-note">
                <span aria-hidden="true">&#x1F6E1;</span>
                <span>Your subscription powers <strong>real-time GPS tracking</strong>, <strong>emergency response</strong>,
                    and <strong>peace of mind</strong> &mdash; not just transportation.</span>
            </div>
        </div>
    </section>

    <!-- ========== PLAN CARDS ========== -->
    <section class="pricing-plans" aria-labelledby="plans-title">
        <div class="container">
            <h2 id="plans-title" class="sr-only">Choose Your Plan</h2>

            <div class="pricing__grid">
                <!-- Per-Trip -->
                <div class="pricing-card animate-on-scroll" id="plan-pertrip">
                    <div class="pricing-card__header">
                        <div class="pricing-card__icon" aria-hidden="true">&#x1F697;</div>
                        <h3 class="pricing-card__name">Per-Trip</h3>
                        <p class="pricing-card__description">Maximum flexibility. Book when you need it.</p>
                        <div class="pricing-card__price">
                            <span class="pricing-card__currency">$</span>
                            <span class="pricing-card__amount">14</span>
                            <span class="pricing-card__period">/trip</span>
                        </div>
                        <p class="pricing-card__description" style="margin-top: var(--space-2); margin-bottom: 0;">No
                            commitment required</p>
                    </div>

                    <div class="pricing-card__features">
                        <div class="pricing-card__feature-group">
                            <p class="pricing-card__feature-group-title">Transportation</p>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Single pickup or drop-off
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Vetted, background-checked driver
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Any location to any location
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Real-time ride tracking in app
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross"
                                    aria-hidden="true">&#x2717;</span>
                                <span class="pricing-card__feature--disabled">No recurring schedule</span>
                            </div>
                        </div>

                        <div class="pricing-card__feature-group">
                            <p class="pricing-card__feature-group-title">IoT &amp; Safety</p>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                App-based ride tracking
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Pickup/drop-off notifications
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross"
                                    aria-hidden="true">&#x2717;</span>
                                <span class="pricing-card__feature--disabled">GPS hardware device</span>
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross"
                                    aria-hidden="true">&#x2717;</span>
                                <span class="pricing-card__feature--disabled">Geofencing</span>
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross"
                                    aria-hidden="true">&#x2717;</span>
                                <span class="pricing-card__feature--disabled">SOS button</span>
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross"
                                    aria-hidden="true">&#x2717;</span>
                                <span class="pricing-card__feature--disabled">Location history</span>
                            </div>
                        </div>
                    </div>

                    <div class="pricing-card__ideal">
                        <p><strong>Ideal for:</strong> Testing the service, occasional rides, or when your regular
                            arrangement falls through.</p>
                    </div>

                    <div class="pricing-card__cta">
                        <a href="{{ route('frontend.contact') }}" class="btn btn--outline btn--full btn--lg">Book a Ride</a>
                    </div>
                </div>

                <!-- Monthly -->
                <div class="pricing-card animate-on-scroll" data-delay="100" id="plan-monthly">
                    <div class="pricing-card__header">
                        <div class="pricing-card__icon" aria-hidden="true">&#x1F4C5;</div>
                        <h3 class="pricing-card__name">Monthly</h3>
                        <p class="pricing-card__description">Regular rides with basic tracking. Cancel anytime.</p>
                        <div class="pricing-card__price">
                            <span class="pricing-card__currency">$</span>
                            <span class="pricing-card__amount">89</span>
                            <span class="pricing-card__period">/month</span>
                        </div>
                        <p class="pricing-card__description" style="margin-top: var(--space-2); margin-bottom: 0;">Billed
                            monthly &bull; Cancel anytime</p>
                    </div>

                    <div class="pricing-card__features">
                        <div class="pricing-card__feature-group">
                            <p class="pricing-card__feature-group-title">Transportation</p>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Daily pickup &amp; drop-off (weekdays)
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Consistent assigned driver
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Recurring schedule support
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Schedule modification flexibility
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross"
                                    aria-hidden="true">&#x2717;</span>
                                <span class="pricing-card__feature--disabled">Multi-stop capability</span>
                            </div>
                        </div>

                        <div class="pricing-card__feature-group">
                            <p class="pricing-card__feature-group-title">IoT &amp; Safety</p>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                App-based GPS ride tracking
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Pickup/drop-off notifications
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Basic trip history (30 days)
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross"
                                    aria-hidden="true">&#x2717;</span>
                                <span class="pricing-card__feature--disabled">GPS hardware device</span>
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross"
                                    aria-hidden="true">&#x2717;</span>
                                <span class="pricing-card__feature--disabled">Geofencing</span>
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross"
                                    aria-hidden="true">&#x2717;</span>
                                <span class="pricing-card__feature--disabled">SOS button</span>
                            </div>
                        </div>
                    </div>

                    <div class="pricing-card__ideal">
                        <p><strong>Ideal for:</strong> Regular school commuters who want reliability but prefer
                            month-to-month flexibility.</p>
                    </div>

                    <div class="pricing-card__cta">
                        <a href="{{ route('frontend.contact') }}" class="btn btn--outline btn--full btn--lg">Start Monthly</a>
                    </div>
                </div>

                <!-- Quarterly — Featured -->
                <div class="pricing-card pricing-card--featured animate-on-scroll" data-delay="200" id="plan-quarterly">
                    <span class="pricing-card__popular">Best Value</span>
                    <div class="pricing-card__header">
                        <div class="pricing-card__icon" aria-hidden="true">&#x2B50;</div>
                        <h3 class="pricing-card__name">Quarterly</h3>
                        <p class="pricing-card__description">Commit for a term &amp; get free GPS hardware.</p>
                        <div class="pricing-card__price">
                            <span class="pricing-card__currency">$</span>
                            <span class="pricing-card__amount">69</span>
                            <span class="pricing-card__period">/month</span>
                        </div>
                        <span class="pricing-card__savings">Save 22% vs. monthly &mdash; billed $207/quarter</span>
                    </div>

                    <div class="pricing-card__iot-highlight">
                        <p>&#x1F381; <strong>Free GPS Device Included</strong><br>Choose TinyBand or TinyTag with basic
                            tracking</p>
                    </div>

                    <div class="pricing-card__features">
                        <div class="pricing-card__feature-group">
                            <p class="pricing-card__feature-group-title">Transportation</p>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Daily pickup &amp; drop-off (weekdays)
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Priority driver matching
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Multi-stop capability
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Schedule flexibility + same-day changes
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross"
                                    aria-hidden="true">&#x2717;</span>
                                <span class="pricing-card__feature--disabled">Weekend &amp; activity rides</span>
                            </div>
                        </div>

                        <div class="pricing-card__feature-group">
                            <p class="pricing-card__feature-group-title">IoT &amp; Safety</p>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                <strong>Free GPS tracking device</strong> (TinyBand or TinyTag)
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Basic GPS location tracking
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Basic trip history (90 days)
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Arrival &amp; departure notifications
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross"
                                    aria-hidden="true">&#x2717;</span>
                                <span class="pricing-card__feature--disabled">Geofencing &amp; safe zones</span>
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--cross"
                                    aria-hidden="true">&#x2717;</span>
                                <span class="pricing-card__feature--disabled">SOS emergency button</span>
                            </div>
                        </div>
                    </div>

                    <div class="pricing-card__ideal">
                        <p><strong>Ideal for:</strong> Families seeking regular rides with the added security of a physical
                            GPS tracking device for their child.</p>
                    </div>

                    <div class="pricing-card__cta">
                        <a href="{{ route('frontend.contact') }}" class="btn btn--primary btn--full btn--lg">Start Quarterly Plan</a>
                    </div>
                </div>

                <!-- Annual -->
                <div class="pricing-card animate-on-scroll" data-delay="300" id="plan-annual">
                    <div class="pricing-card__header">
                        <div class="pricing-card__icon" aria-hidden="true">&#x1F451;</div>
                        <h3 class="pricing-card__name">Annual</h3>
                        <p class="pricing-card__description">Maximum savings. Full premium IoT features.</p>
                        <div class="pricing-card__price">
                            <span class="pricing-card__currency">$</span>
                            <span class="pricing-card__amount">49</span>
                            <span class="pricing-card__period">/month</span>
                        </div>
                        <span class="pricing-card__savings">Save 45% vs. monthly &mdash; billed $588/year</span>
                    </div>

                    <div class="pricing-card__iot-highlight"
                        style="background: linear-gradient(135deg, #fff7ed, #fef3c7);">
                        <p>&#x1F451; <strong>Free Premium GPS Device + ALL Premium Features</strong><br>Choose any device:
                            TinyBand, TinyTag, or TinyPack</p>
                    </div>

                    <div class="pricing-card__features">
                        <div class="pricing-card__feature-group">
                            <p class="pricing-card__feature-group-title">Transportation</p>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                <strong>Unlimited rides</strong> (weekdays)
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Dedicated personal driver
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Multi-stop + extra destinations
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Weekend &amp; activity rides included
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Priority support &amp; scheduling
                            </div>
                        </div>

                        <div class="pricing-card__feature-group">
                            <p class="pricing-card__feature-group-title">IoT &amp; Safety &mdash; Premium</p>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                <strong>Free premium GPS device</strong> (any model)
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                <strong>Geofencing &amp; safe zone alerts</strong>
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                <strong>SOS emergency button</strong>
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                <strong>Advanced alerts &amp; smart notifications</strong>
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                <strong>Full location history timeline</strong>
                            </div>
                            <div class="pricing-card__feature">
                                <span class="pricing-card__feature-icon pricing-card__feature-icon--check"
                                    aria-hidden="true">&#x2713;</span>
                                Multi-child device support
                            </div>
                        </div>
                    </div>

                    <div class="pricing-card__ideal">
                        <p><strong>Ideal for:</strong> Families who want maximum protection with full premium IoT features
                            &mdash; geofencing, SOS, advanced alerts, and complete location history.</p>
                    </div>

                    <div class="pricing-card__cta">
                        <a href="{{ route('frontend.contact') }}" class="btn btn--accent btn--full btn--lg">Start Annual Plan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== IoT VALUE EXPLANATION ========== -->
    <section class="pricing-iot-explainer" aria-labelledby="iot-explainer-title">
        <div class="container">
            <div class="pricing-iot-explainer__inner">
                <div class="pricing-iot-explainer__content">
                    <span class="section-label">Why Your Subscription Matters</span>
                    <h2 id="iot-explainer-title">You&rsquo;re Not Just Paying for Rides.<br>You&rsquo;re Powering Safety
                        Technology.</h2>
                    <p>
                        Every TinyTrails subscription funds the IoT infrastructure that keeps your child safe: GPS satellite
                        connections,
                        cellular data for real-time tracking, cloud-based monitoring systems, and a 24/7 safety coordination
                        team.
                        The higher your commitment, the more advanced technology we can unlock for your family.
                    </p>
                </div>

                <div class="pricing-iot-explainer__tiers">
                    <div class="pricing-iot-tier">
                        <div class="pricing-iot-tier__header">
                            <span class="pricing-iot-tier__icon" aria-hidden="true">&#x1F4F1;</span>
                            <h3>Per-Trip &amp; Monthly</h3>
                        </div>
                        <p>App-based tracking during active rides. See your child&rsquo;s location in real-time while the
                            ride is in progress.</p>
                    </div>

                    <div class="pricing-iot-tier pricing-iot-tier--mid">
                        <div class="pricing-iot-tier__header">
                            <span class="pricing-iot-tier__icon" aria-hidden="true">&#x1F4CD;</span>
                            <h3>Quarterly</h3>
                        </div>
                        <p><strong>Free GPS device</strong> with basic location tracking &amp; trip history. Your child
                            carries a physical device that reports location even outside of rides.</p>
                    </div>

                    <div class="pricing-iot-tier pricing-iot-tier--top">
                        <div class="pricing-iot-tier__header">
                            <span class="pricing-iot-tier__icon" aria-hidden="true">&#x1F6E1;</span>
                            <h3>Annual &mdash; Premium</h3>
                        </div>
                        <p><strong>Full IoT suite:</strong> Geofencing, SOS emergency button, advanced alerts, location
                            history timeline, and multi-child support. Complete peace of mind, 24/7.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== COMPARISON TABLE ========== -->
    <section class="pricing-comparison" aria-labelledby="comparison-title">
        <div class="container text-center">
            <h2 id="comparison-title" class="section-title">Compare All Plans</h2>
            <p class="section-subtitle">A detailed breakdown of what&rsquo;s included in each plan.</p>

            <div class="pricing-comparison__table-wrap">
                <table class="pricing-comparison__table" role="table" aria-label="Plan comparison">
                    <thead>
                        <tr>
                            <th scope="col">Feature</th>
                            <th scope="col">Per-Trip</th>
                            <th scope="col">Monthly</th>
                            <th scope="col" class="pricing-comparison__highlight">Quarterly</th>
                            <th scope="col">Annual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="pricing-comparison__group-header">
                            <td colspan="5"><strong>Transportation</strong></td>
                        </tr>
                        <tr>
                            <td>Pickup &amp; Drop-off</td>
                            <td>Single ride</td>
                            <td>Daily (weekdays)</td>
                            <td class="pricing-comparison__highlight">Daily (weekdays)</td>
                            <td>Unlimited</td>
                        </tr>
                        <tr>
                            <td>Driver Assignment</td>
                            <td>Any available</td>
                            <td>Consistent</td>
                            <td class="pricing-comparison__highlight">Priority match</td>
                            <td>Dedicated personal</td>
                        </tr>
                        <tr>
                            <td>Recurring Schedule</td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-check" aria-label="Included">&#x2713;</span></td>
                            <td class="pricing-comparison__highlight"><span class="table-check"
                                    aria-label="Included">&#x2713;</span></td>
                            <td><span class="table-check" aria-label="Included">&#x2713;</span></td>
                        </tr>
                        <tr>
                            <td>Multi-Stop</td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td class="pricing-comparison__highlight"><span class="table-check"
                                    aria-label="Included">&#x2713;</span></td>
                            <td><span class="table-check" aria-label="Included">&#x2713;</span></td>
                        </tr>
                        <tr>
                            <td>Weekend &amp; Activity Rides</td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td class="pricing-comparison__highlight"><span class="table-cross"
                                    aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-check" aria-label="Included">&#x2713;</span></td>
                        </tr>

                        <tr class="pricing-comparison__group-header">
                            <td colspan="5"><strong>IoT &amp; Safety Technology</strong></td>
                        </tr>
                        <tr>
                            <td>App-Based Ride Tracking</td>
                            <td><span class="table-check" aria-label="Included">&#x2713;</span></td>
                            <td><span class="table-check" aria-label="Included">&#x2713;</span></td>
                            <td class="pricing-comparison__highlight"><span class="table-check"
                                    aria-label="Included">&#x2713;</span></td>
                            <td><span class="table-check" aria-label="Included">&#x2713;</span></td>
                        </tr>
                        <tr>
                            <td>GPS Hardware Device</td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td class="pricing-comparison__highlight"><strong>Free</strong> (Basic)</td>
                            <td><strong>Free</strong> (Premium)</td>
                        </tr>
                        <tr>
                            <td>Trip History</td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td>30 days</td>
                            <td class="pricing-comparison__highlight">90 days</td>
                            <td>Unlimited</td>
                        </tr>
                        <tr>
                            <td>Geofencing &amp; Safe Zones</td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td class="pricing-comparison__highlight"><span class="table-cross"
                                    aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-check" aria-label="Included">&#x2713;</span></td>
                        </tr>
                        <tr>
                            <td>SOS Emergency Button</td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td class="pricing-comparison__highlight"><span class="table-cross"
                                    aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-check" aria-label="Included">&#x2713;</span></td>
                        </tr>
                        <tr>
                            <td>Advanced Alerts</td>
                            <td>Basic</td>
                            <td>Basic</td>
                            <td class="pricing-comparison__highlight">Standard</td>
                            <td><strong>Advanced</strong></td>
                        </tr>
                        <tr>
                            <td>Location History Timeline</td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td class="pricing-comparison__highlight"><span class="table-cross"
                                    aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-check" aria-label="Included">&#x2713;</span></td>
                        </tr>
                        <tr>
                            <td>Multi-Child Device Support</td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td class="pricing-comparison__highlight"><span class="table-cross"
                                    aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-check" aria-label="Included">&#x2713;</span></td>
                        </tr>

                        <tr class="pricing-comparison__group-header">
                            <td colspan="5"><strong>Support</strong></td>
                        </tr>
                        <tr>
                            <td>Customer Support</td>
                            <td>Email</td>
                            <td>Email &amp; Chat</td>
                            <td class="pricing-comparison__highlight">Priority Chat</td>
                            <td>24/7 Phone + Chat</td>
                        </tr>
                        <tr>
                            <td>Safety Coordination Team</td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-cross" aria-label="Not included">&#x2717;</span></td>
                            <td class="pricing-comparison__highlight"><span class="table-cross"
                                    aria-label="Not included">&#x2717;</span></td>
                            <td><span class="table-check" aria-label="Included">&#x2713;</span> 24/7</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td><a href="{{ route('frontend.contact') }}" class="btn btn--outline btn--sm">Book Ride</a></td>
                            <td><a href="{{ route('frontend.contact') }}" class="btn btn--outline btn--sm">Start Monthly</a></td>
                            <td class="pricing-comparison__highlight"><a href="{{ route('frontend.contact') }}"
                                    class="btn btn--primary btn--sm">Start Quarterly</a></td>
                            <td><a href="{{ route('frontend.contact') }}" class="btn btn--accent btn--sm">Start Annual</a></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <!-- ========== FAQ ========== -->
    <section class="faq" id="faq" aria-labelledby="pricing-faq-title">
        <div class="container text-center">
            <span class="section-label">Pricing FAQ</span>
            <h2 id="pricing-faq-title" class="section-title">Have Questions About Our Plans?</h2>

            <div class="faq__list" role="list">
                <div class="faq__item" role="listitem">
                    <button class="faq__question" aria-expanded="false">
                        What&rsquo;s included in the &ldquo;free GPS device&rdquo;?
                        <svg class="faq__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="faq__answer" aria-hidden="true">
                        <div class="faq__answer-inner">
                            <strong>Quarterly plan:</strong> You receive a free TinyBand (wearable wristband) or TinyTag
                            (clip-on tracker) with basic GPS location tracking and basic trip history (90 days). The device
                            reports your child&rsquo;s location during and outside of rides.<br><br>
                            <strong>Annual plan:</strong> You receive your choice of any device (TinyBand, TinyTag, or
                            TinyPack backpack insert) with full premium features including geofencing, SOS emergency button,
                            advanced smart alerts, and unlimited location history timeline.
                        </div>
                    </div>
                </div>

                <div class="faq__item" role="listitem">
                    <button class="faq__question" aria-expanded="false">
                        Why is the subscription more than just rides?
                        <svg class="faq__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="faq__answer" aria-hidden="true">
                        <div class="faq__answer-inner">
                            Your subscription powers an entire safety ecosystem: GPS satellite connectivity, cellular data
                            transmission for real-time location updates, secure cloud infrastructure for data storage and
                            processing, our 24/7 safety coordination center, and ongoing device firmware updates.
                            You&rsquo;re investing in continuous, always-on protection &mdash; not just point-to-point
                            transport.
                        </div>
                    </div>
                </div>

                <div class="faq__item" role="listitem">
                    <button class="faq__question" aria-expanded="false">
                        Can I switch between plans?
                        <svg class="faq__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="faq__answer" aria-hidden="true">
                        <div class="faq__answer-inner">
                            Yes! You can upgrade at any time and we&rsquo;ll prorate the difference. Downgrades take effect
                            at the end of your current billing period. If you downgrade from a plan that included a free GPS
                            device, the device must be returned within 14 days or a device fee will apply.
                        </div>
                    </div>
                </div>

                <div class="faq__item" role="listitem">
                    <button class="faq__question" aria-expanded="false">
                        Is there a family discount for multiple children?
                        <svg class="faq__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="faq__answer" aria-hidden="true">
                        <div class="faq__answer-inner">
                            Yes. Adding a second child to the same plan receives a 20% discount, and each additional child
                            after that receives 25% off. Annual plan subscribers with multiple children also receive
                            additional GPS devices at no extra cost. Contact us for custom family pricing.
                        </div>
                    </div>
                </div>

                <div class="faq__item" role="listitem">
                    <button class="faq__question" aria-expanded="false">
                        What happens to the GPS device if I cancel?
                        <svg class="faq__chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <div class="faq__answer" aria-hidden="true">
                        <div class="faq__answer-inner">
                            GPS devices provided free with Quarterly and Annual plans should be returned within 14 days of
                            cancellation using a prepaid shipping label we&rsquo;ll provide. If the device is not returned,
                            a device fee ($45 for TinyBand/TinyTag, $65 for TinyPack) will be charged. If you&rsquo;ve
                            completed your full annual term, you may keep the device at no extra cost.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========== CTA ========== -->
    <section class="cta" aria-labelledby="pricing-cta-title">
        <div class="container">
            <h2 id="pricing-cta-title">Ready to Keep Your Child Safe?</h2>
            <p>Start with a single ride to experience the service, or choose a plan and receive a free GPS tracking device.
                Your child&rsquo;s safety is our mission.</p>
            <div class="cta__actions">
                <a href="{{ route('frontend.contact') }}" class="btn btn--white btn--lg">Get Started Today</a>
                <a href="{{ route('frontend.contact') }}" class="btn btn--outline-white btn--lg">Talk to Our Team</a>
            </div>
        </div>
    </section>
@endsection
