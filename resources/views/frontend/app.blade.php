<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- Security Headers -->
  <meta http-equiv="Content-Security-Policy"
        content="default-src 'self';
                 script-src 'self';
                 style-src 'self' https://fonts.googleapis.com;
                 font-src 'self' https://fonts.gstatic.com;
                 img-src 'self' data:;
                 connect-src 'self';
                 frame-ancestors 'none';
                 base-uri 'self';
                 form-action 'self';">
  <meta http-equiv="X-Content-Type-Options" content="nosniff">
  <meta name="referrer" content="strict-origin-when-cross-origin">

  <!-- SEO -->
  <title>TinyTrails — Safe Kids Drop-off &amp; Pickup Service with GPS Tracking</title>
  <meta name="description" content="TinyTrails provides safe, reliable kids drop-off and pickup services to school and activities. GPS tracking devices, real-time monitoring, vetted drivers, and flexible plans for peace of mind.">
  <meta name="keywords" content="kids transportation, school pickup, drop-off service, GPS tracking, child safety, school rides">
  <link rel="canonical" href="https://tinytrails.com">

  <!-- Open Graph -->
  <meta property="og:title" content="TinyTrails — Safe Kids Drop-off &amp; Pickup Service">
  <meta property="og:description" content="GPS-tracked, safe rides for your kids. Real-time monitoring, vetted drivers, flexible plans.">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://tinytrails.com">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}">
  <link rel="stylesheet" href="{{ asset('frontend/css/responsive.css') }}">
  @yield('css')
</head>
<body>
  <!-- ========== NAVIGATION ========== -->
  @include('frontend.inc.navbar')

  @yield('content')
  <!-- ========== FOOTER ========== -->
  @include('frontend.inc.footer')
  <!-- Scripts -->
  <script src="{{ asset('frontend/js/main.js') }}"></script>
  @stack('script')
</body>
</html>
