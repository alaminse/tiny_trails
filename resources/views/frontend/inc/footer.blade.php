<footer class="footer" role="contentinfo">
    <div class="container">
      <div class="footer__grid">
        <div>
          <a href="{{ route('frontend.home') }}" class="nav__logo" style="margin-bottom: var(--space-4); display: inline-flex;">
            <img src="{{ asset('frontend/assets/logo.jpeg') }}" alt="TinyTrails logo" class="nav__logo-icon">
            <span style="color: white;">Tiny<strong>Trails</strong></span>
          </a>
          <p class="footer__brand-description">
            TinyTrails provides GPS-tracked, safe drop-off and pickup services for children. Combining modern transportation with IoT security technology for complete peace of mind.
          </p>
          <div class="footer__social">
            <a href="#" class="footer__social-link" aria-label="Facebook">f</a>
            <a href="#" class="footer__social-link" aria-label="Twitter">t</a>
            <a href="#" class="footer__social-link" aria-label="Instagram">i</a>
            <a href="#" class="footer__social-link" aria-label="LinkedIn">in</a>
          </div>
        </div>

        <div>
          <h3 class="footer__heading">Service</h3>
          <a href="{{ route('frontend.how_it_works') }}" class="footer__link">How It Works</a>
          <a href="{{ route('frontend.pricing') }}" class="footer__link">Plans &amp; Pricing</a>
          <a href="{{ route('frontend.safety') }}" class="footer__link">Safety</a>
          <a href="#devices" class="footer__link">GPS Devices</a>
          <a href="#" class="footer__link">Service Areas</a>
        </div>

        <div>
          <h3 class="footer__heading">Company</h3>
          <a href="#" class="footer__link">About Us</a>
          <a href="#" class="footer__link">Careers</a>
          <a href="#" class="footer__link">Blog</a>
          <a href="{{ route('frontend.contact') }}" class="footer__link">Contact</a>
          <a href="#" class="footer__link">Press</a>
        </div>

        <div>
          <h3 class="footer__heading">Support</h3>
          <a href="{{ route('frontend.contact') }}" class="footer__link">Help Center</a>
          <a href="#faq" class="footer__link">FAQ</a>
          <a href="#" class="footer__link">Driver Application</a>
          <a href="#" class="footer__link">Partner Schools</a>
          <a href="#" class="footer__link">Download App</a>
        </div>
      </div>

      <div class="footer__bottom">
        <p class="footer__copyright">&copy; 2026 TinyTrails. All rights reserved.</p>
        <div class="footer__legal">
          <a href="#" class="footer__legal-link">Privacy Policy</a>
          <a href="#" class="footer__legal-link">Terms of Service</a>
          <a href="#" class="footer__legal-link">Cookie Policy</a>
          <a href="#" class="footer__legal-link">Accessibility</a>
        </div>
      </div>
    </div>
</footer>
