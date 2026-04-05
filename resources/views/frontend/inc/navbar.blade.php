<nav class="nav" role="navigation" aria-label="Main navigation">
    <div class="nav__inner">
      <a href="{{ route('frontend.home') }}" class="nav__logo" aria-label="TinyTrails Home">
        <img src="{{ asset('frontend/assets/logo.jpeg') }}" alt="TinyTrails logo" class="nav__logo-icon">
        <span>Tiny<strong>Trails</strong></span>
      </a>

      <button class="nav__toggle" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="nav-links">
        <span class="nav__toggle-icon" aria-hidden="true"></span>
      </button>

      <ul class="nav__links" id="nav-links" role="menubar">
        <li role="none"><a href="{{ route('frontend.home') }}" class="nav__link nav__link--active" role="menuitem">Home</a></li>
        <li role="none"><a href="{{ route('frontend.how_it_works') }}" class="nav__link" role="menuitem">How It Works</a></li>
        <li role="none"><a href="{{ route('frontend.pricing') }}" class="nav__link" role="menuitem">Plans &amp; Pricing</a></li>
        <li role="none"><a href="{{ route('frontend.safety') }}" class="nav__link" role="menuitem">Safety</a></li>
        <li role="none"><a href="{{ route('frontend.contact') }}" class="nav__link" role="menuitem">Contact</a></li>
      </ul>

      <div class="nav__actions">
        <a href="{{ route('login') }}" class="btn btn--outline btn--sm">Log In</a>
        <a href="{{ route('frontend.pricing') }}" class="btn btn--primary btn--sm">Get Started</a>
      </div>
    </div>
</nav>
