<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiny trails - Keep Your Little Ones Safe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #FF6B9D;
            --secondary: #4ECDC4;
            --accent: #FFE66D;
            --purple: #9B59B6;
            --blue: #3498DB;
            --dark: #2C3E50;
            --neon-pink: #FF10F0;
            --neon-blue: #00F0FF;
            --neon-green: #39FF14;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
            background: #0a0a0a;
        }

        /* Animated Gaming Background */
        .bg-gaming {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: white;
            border-radius: 50%;
            animation: float-particle 15s infinite;
            opacity: 0.6;
        }

        @keyframes float-particle {
            0%, 100% { transform: translateY(0) translateX(0); }
            25% { transform: translateY(-100px) translateX(50px); }
            50% { transform: translateY(-50px) translateX(-50px); }
            75% { transform: translateY(-150px) translateX(100px); }
        }

        .grid-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: grid-scroll 20s linear infinite;
        }

        @keyframes grid-scroll {
            0% { transform: translateY(0); }
            100% { transform: translateY(50px); }
        }

        /* Navigation */
        .navbar {
            background: rgba(10, 10, 10, 0.95) !important;
            backdrop-filter: blur(20px);
            box-shadow: 0 4px 30px rgba(255, 16, 240, 0.3);
            border-bottom: 2px solid var(--neon-pink);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
            color: white !important;
            text-shadow: 0 0 20px var(--neon-pink), 0 0 40px var(--neon-blue);
            display: flex;
            align-items: center;
        }

        .nav-link {
            color: white !important;
            font-weight: 600;
            margin: 0 0.5rem;
            position: relative;
            transition: all 0.3s;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--neon-pink), var(--neon-blue));
            transition: width 0.3s;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link:hover {
            color: var(--neon-pink) !important;
            text-shadow: 0 0 10px var(--neon-pink);
            transform: translateY(-2px);
        }

        /* Logo SVG */
        .logo-svg {
            width: 60px;
            height: 60px;
            margin-right: 15px;
            filter: drop-shadow(0 0 10px var(--neon-pink));
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            padding: 120px 0 50px;
        }

        .hero-content {
            background: rgba(10, 10, 10, 0.85);
            border-radius: 30px;
            padding: 3rem;
            box-shadow: 
                0 0 60px rgba(255, 16, 240, 0.4),
                0 20px 60px rgba(0, 0, 0, 0.6),
                inset 0 0 60px rgba(255, 16, 240, 0.1);
            border: 2px solid rgba(255, 16, 240, 0.3);
            animation: glow-pulse 3s ease-in-out infinite;
        }

        @keyframes glow-pulse {
            0%, 100% { box-shadow: 0 0 60px rgba(255, 16, 240, 0.4), 0 20px 60px rgba(0, 0, 0, 0.6); }
            50% { box-shadow: 0 0 80px rgba(255, 16, 240, 0.6), 0 20px 80px rgba(0, 0, 0, 0.8); }
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: bold;
            color: white;
            text-shadow: 
                0 0 20px var(--neon-pink),
                0 0 40px var(--neon-blue),
                0 0 60px var(--neon-pink);
            margin-bottom: 1.5rem;
            animation: text-glow 2s ease-in-out infinite;
        }

        @keyframes text-glow {
            0%, 100% { text-shadow: 0 0 20px var(--neon-pink), 0 0 40px var(--neon-blue); }
            50% { text-shadow: 0 0 30px var(--neon-pink), 0 0 60px var(--neon-blue), 0 0 80px var(--neon-pink); }
        }

        .hero p {
            font-size: 1.3rem;
            color: #ddd;
            margin-bottom: 2rem;
        }

        /* Device Cards - Gaming Style */
        .device-card {
            background: linear-gradient(145deg, rgba(20, 20, 20, 0.9), rgba(40, 40, 40, 0.9));
            border-radius: 20px;
            padding: 2rem;
            margin: 1rem 0;
            box-shadow: 
                0 0 30px rgba(255, 16, 240, 0.3),
                inset 0 0 30px rgba(255, 16, 240, 0.05);
            border: 2px solid rgba(255, 16, 240, 0.3);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .device-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 16, 240, 0.3), transparent);
            transition: left 0.6s;
        }

        .device-card:hover::before {
            left: 100%;
        }

        .device-card:hover {
            transform: translateY(-15px) scale(1.05);
            box-shadow: 
                0 0 60px rgba(255, 16, 240, 0.6),
                0 30px 60px rgba(0, 0, 0, 0.8),
                inset 0 0 40px rgba(255, 16, 240, 0.1);
            border-color: var(--neon-pink);
        }

        .device-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            filter: drop-shadow(0 0 20px var(--neon-blue));
            animation: float-icon 3s ease-in-out infinite;
        }

        @keyframes float-icon {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .device-card h4 {
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .device-card p {
            color: #bbb;
        }

        .badge-gaming {
            background: linear-gradient(135deg, var(--neon-pink), var(--purple));
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 0 20px rgba(255, 16, 240, 0.5);
        }

        /* Buttons - Gaming Style */
        .btn-gaming-primary {
            background: linear-gradient(135deg, var(--neon-pink), var(--purple));
            border: 2px solid var(--neon-pink);
            padding: 1rem 3rem;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 
                0 0 30px rgba(255, 16, 240, 0.6),
                0 10px 30px rgba(0, 0, 0, 0.5);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-gaming-primary::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-gaming-primary:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-gaming-primary:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 0 50px rgba(255, 16, 240, 0.8),
                0 15px 50px rgba(0, 0, 0, 0.7);
        }

        .btn-gaming-secondary {
            background: linear-gradient(135deg, var(--neon-blue), var(--blue));
            border: 2px solid var(--neon-blue);
            padding: 1rem 3rem;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 
                0 0 30px rgba(0, 240, 255, 0.6),
                0 10px 30px rgba(0, 0, 0, 0.5);
            transition: all 0.3s;
        }

        .btn-gaming-secondary:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 0 50px rgba(0, 240, 255, 0.8),
                0 15px 50px rgba(0, 0, 0, 0.7);
        }

        /* Features Section */
        .features-section {
            padding: 5rem 0;
            background: rgba(10, 10, 10, 0.8);
            position: relative;
        }

        .section-title {
            font-size: 3.5rem;
            font-weight: bold;
            color: white;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 3rem;
            text-shadow: 
                0 0 20px var(--neon-pink),
                0 0 40px var(--neon-blue);
        }

        .feature-box {
            text-align: center;
            padding: 2.5rem;
            background: rgba(20, 20, 20, 0.8);
            border-radius: 20px;
            border: 2px solid rgba(255, 16, 240, 0.2);
            transition: all 0.4s;
            height: 100%;
        }

        .feature-box:hover {
            transform: translateY(-10px);
            border-color: var(--neon-pink);
            box-shadow: 0 0 40px rgba(255, 16, 240, 0.5);
        }

        .feature-icon-gaming {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3rem;
            color: white;
            position: relative;
            animation: rotate-border 4s linear infinite;
        }

        .feature-icon-gaming::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--neon-pink), var(--neon-blue), var(--neon-green), var(--neon-pink));
            z-index: -1;
            animation: rotate-border 3s linear infinite;
        }

        @keyframes rotate-border {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .feature-icon-gaming.pink { background: linear-gradient(135deg, var(--neon-pink), var(--purple)); }
        .feature-icon-gaming.blue { background: linear-gradient(135deg, var(--neon-blue), var(--blue)); }
        .feature-icon-gaming.green { background: linear-gradient(135deg, var(--neon-green), #00cc00); }

        .feature-box h3 {
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1rem;
        }

        .feature-box p {
            color: #bbb;
        }

        /* About Section */
        .about-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
            position: relative;
        }

        .emotional-card {
            background: rgba(20, 20, 20, 0.9);
            border-radius: 20px;
            padding: 2rem;
            margin: 1.5rem 0;
            border-left: 5px solid var(--neon-pink);
            box-shadow: 0 0 30px rgba(255, 16, 240, 0.3);
            transition: all 0.3s;
        }

        .emotional-card:hover {
            transform: translateX(10px);
            box-shadow: 0 0 50px rgba(255, 16, 240, 0.5);
        }

        .emotional-card h4 {
            color: var(--neon-pink);
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .emotional-card p {
            color: #ddd;
            font-style: italic;
        }

        /* Privacy Policy Section */
        .privacy-section {
            padding: 5rem 0;
            background: rgba(10, 10, 10, 0.9);
        }

        .policy-box {
            background: rgba(30, 30, 30, 0.9);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 16, 240, 0.2);
        }

        .policy-box h3 {
            color: var(--neon-blue);
            font-weight: bold;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }

        .policy-box p, .policy-box ul {
            color: #ccc;
            line-height: 1.8;
        }

        .policy-box ul {
            list-style: none;
            padding-left: 0;
        }

        .policy-box ul li::before {
            content: '▶ ';
            color: var(--neon-pink);
            margin-right: 10px;
        }

        /* Blog Cards */
        .blog-card {
            background: rgba(20, 20, 20, 0.9);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(255, 16, 240, 0.2);
            transition: all 0.3s;
            margin-bottom: 2rem;
            border: 2px solid rgba(255, 16, 240, 0.2);
        }

        .blog-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 0 50px rgba(255, 16, 240, 0.5);
            border-color: var(--neon-pink);
        }

        .blog-image {
            height: 250px;
            background: linear-gradient(135deg, var(--neon-pink), var(--neon-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            position: relative;
            overflow: hidden;
        }

        .blog-image::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: blog-shine 3s infinite;
        }

        @keyframes blog-shine {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .blog-content {
            padding: 2rem;
        }

        .blog-content h4 {
            color: white;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .blog-content .date {
            color: var(--neon-blue);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .blog-content p {
            color: #bbb;
            margin-bottom: 1.5rem;
        }

        /* Contact Form */
        .contact-section {
            padding: 5rem 0;
            background: rgba(10, 10, 10, 0.9);
        }

        .form-control-gaming {
            background: rgba(30, 30, 30, 0.8);
            border: 2px solid rgba(255, 16, 240, 0.3);
            border-radius: 15px;
            padding: 1rem;
            color: white;
            transition: all 0.3s;
        }

        .form-control-gaming:focus {
            background: rgba(40, 40, 40, 0.9);
            border-color: var(--neon-pink);
            box-shadow: 0 0 20px rgba(255, 16, 240, 0.4);
            color: white;
        }

        .form-control-gaming::placeholder {
            color: #888;
        }

        .form-label-gaming {
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        /* Math Captcha */
        .captcha-box {
            background: rgba(30, 30, 30, 0.9);
            border: 2px solid var(--neon-blue);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
        }

        .captcha-question {
            color: var(--neon-blue);
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        /* Footer */
        footer {
            background: rgba(10, 10, 10, 0.95);
            color: white;
            padding: 3rem 0 1rem;
            border-top: 2px solid var(--neon-pink);
        }

        .footer-section h5 {
            color: var(--neon-pink);
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }

        .footer-links a {
            color: #bbb;
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }

        .footer-links a:hover {
            color: var(--neon-pink);
            transform: translateX(5px);
        }

        .social-icons a {
            display: inline-block;
            width: 50px;
            height: 50px;
            line-height: 50px;
            text-align: center;
            background: linear-gradient(135deg, var(--neon-pink), var(--neon-blue));
            border-radius: 50%;
            color: white;
            margin: 0 0.5rem;
            transition: all 0.3s;
            font-size: 1.3rem;
            box-shadow: 0 0 20px rgba(255, 16, 240, 0.5);
        }

        .social-icons a:hover {
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 0 30px rgba(255, 16, 240, 0.8);
        }

        /* Page Content Container */
        .page-content {
            min-height: 80vh;
            color: white;
        }

        /* FAQ Accordion */
        .accordion-gaming .accordion-item {
            background: rgba(20, 20, 20, 0.9);
            border: 2px solid rgba(255, 16, 240, 0.2);
            margin-bottom: 1rem;
            border-radius: 15px;
            overflow: hidden;
        }

        .accordion-gaming .accordion-button {
            background: rgba(30, 30, 30, 0.9);
            color: white;
            font-weight: bold;
            border: none;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .accordion-gaming .accordion-button:not(.collapsed) {
            background: linear-gradient(135deg, var(--neon-pink), var(--purple));
            color: white;
            box-shadow: 0 0 20px rgba(255, 16, 240, 0.5);
        }

        .accordion-gaming .accordion-body {
            background: rgba(20, 20, 20, 0.9);
            color: #ddd;
        }

        /* Testimonial Card */
        .testimonial-card {
            background: rgba(30, 30, 30, 0.9);
            border-left: 5px solid var(--neon-blue);
            border-radius: 15px;
            padding: 2rem;
            margin: 1rem 0;
            box-shadow: 0 0 30px rgba(0, 240, 255, 0.2);
        }

        .testimonial-card p {
            color: #ddd;
            font-style: italic;
            margin-bottom: 1rem;
        }

        .testimonial-card strong {
            color: var(--neon-blue);
        }

        /* Hide all pages except home by default */
        .page {
            display: none;
        }

        .page.active {
            display: block;
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Loading Animation */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(10, 10, 10, 0.95);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loader {
            width: 80px;
            height: 80px;
            border: 5px solid rgba(255, 16, 240, 0.3);
            border-top: 5px solid var(--neon-pink);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loader"></div>
    </div>

    <!-- Animated Gaming Background -->
    <div class="bg-gaming">
        <div class="grid-overlay"></div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#" onclick="showPage('home')">
                <svg class="logo-svg" viewBox="0 0 100 100">
                    <defs>
                        <linearGradient id="logoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#FF10F0;stop-opacity:1" />
                            <stop offset="50%" style="stop-color:#00F0FF;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#39FF14;stop-opacity:1" />
                        </linearGradient>
                        <filter id="glow">
                            <feGaussianBlur stdDeviation="4" result="coloredBlur"/>
                            <feMerge>
                                <feMergeNode in="coloredBlur"/>
                                <feMergeNode in="SourceGraphic"/>
                            </feMerge>
                        </filter>
                    </defs>
                    <circle cx="50" cy="50" r="45" fill="url(#logoGrad)" filter="url(#glow)"/>
                    <circle cx="50" cy="50" r="35" fill="none" stroke="white" stroke-width="3"/>
                    <path d="M 50 25 L 50 50 L 70 50" stroke="white" stroke-width="5" fill="none" stroke-linecap="round"/>
                    <circle cx="50" cy="50" r="6" fill="white"/>
                    <circle cx="35" cy="35" r="4" fill="white" opacity="0.6"/>
                    <circle cx="65" cy="35" r="4" fill="white" opacity="0.6"/>
                    <circle cx="35" cy="65" r="4" fill="white" opacity="0.6"/>
                </svg>
                Tiny trails
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="background: var(--neon-pink); border: none;">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#" onclick="showPage('home')">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="showPage('about')">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="showPage('how-it-works')">How It Works</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="showPage('faq')">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="showPage('privacy')">Privacy Policy</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="showPage('blog')">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" onclick="showPage('contact')">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HOME PAGE -->
    <div id="home" class="page active">
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="hero-content">
                            <h1>🎮 Keep Your Little Gamers Safe! 🌟</h1>
                            <p>Next-gen tracking technology for the digital age. Real-time location, geofencing, and peace of mind in one powerful device.</p>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="device-card">
                                        <div class="device-icon">📡</div>
                                        <h4>BLE IoT Device</h4>
                                        <p>Standard Bluetooth Low Energy tracking for everyday safety</p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span class="badge-gaming">Default Option</span>
                                        </div>
                                        <div class="mt-3">
                                            <small style="color: #aaa;">💰 Purchase Outright or Lease Monthly</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="device-card">
                                        <div class="device-icon">📱</div>
                                        <h4>4G/LTE Device</h4>
                                        <p>Premium cellular tracking with unlimited range</p>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span class="badge-gaming">Extra Cost</span>
                                        </div>
                                        <div class="mt-3">
                                            <small style="color: #aaa;">💰 Purchase Outright or Lease Monthly</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 text-center">
                                <button class="btn-gaming-primary me-3" onclick="showPage('contact')">Get Started Now</button>
                                <button class="btn-gaming-secondary" onclick="showPage('how-it-works')">Learn More</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 text-center">
                        <svg width="500" height="500" viewBox="0 0 500 500">
                            <defs>
                                <linearGradient id="heroGrad">
                                    <stop offset="0%" style="stop-color:#FF10F0"/>
                                    <stop offset="50%" style="stop-color:#00F0FF"/>
                                    <stop offset="100%" style="stop-color:#39FF14"/>
                                </linearGradient>
                                <filter id="heroGlow">
                                    <feGaussianBlur stdDeviation="8" result="coloredBlur"/>
                                    <feMerge>
                                        <feMergeNode in="coloredBlur"/>
                                        <feMergeNode in="SourceGraphic"/>
                                    </feMerge>
                                </filter>
                            </defs>
                            <circle cx="250" cy="250" r="200" fill="url(#heroGrad)" opacity="0.1" filter="url(#heroGlow)">
                                <animate attributeName="r" values="200;230;200" dur="3s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="250" cy="250" r="150" fill="url(#heroGrad)" opacity="0.2" filter="url(#heroGlow)">
                                <animate attributeName="r" values="150;180;150" dur="2.5s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="250" cy="250" r="100" fill="url(#heroGrad)" opacity="0.4" filter="url(#heroGlow)">
                                <animate attributeName="r" values="100;120;100" dur="2s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="250" cy="250" r="80" fill="url(#heroGrad)" filter="url(#heroGlow)"/>
                            <text x="250" y="280" text-anchor="middle" font-size="80" fill="white">👶</text>
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <h2 class="section-title">⚡ Power Features ⚡</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="feature-box">
                            <div class="feature-icon-gaming pink">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h3>Military-Grade Security</h3>
                            <p>Bank-level encryption protects your family's data. Your privacy is our highest priority.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-box">
                            <div class="feature-icon-gaming blue">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <h3>Real-Time Tracking</h3>
                            <p>Instant location updates and smart alerts. Know where your kids are, always.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="feature-box">
                            <div class="feature-icon-gaming green">
                                <i class="fas fa-battery-full"></i>
                            </div>
                            <h3>Extended Battery</h3>
                            <p>Up to 7 days on a single charge. Worry-free protection that lasts.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- ABOUT US PAGE -->
    <div id="about" class="page">
        <section class="about-section page-content">
            <div class="container" style="padding-top: 120px;">
                <h2 class="section-title">About Tiny trails</h2>
                <div class="row align-items-center mb-5">
                    <div class="col-lg-6">
                        <div class="hero-content">
                            <h3 style="color: var(--neon-pink); text-transform: uppercase; margin-bottom: 2rem;">We're Parents Too</h3>
                            <p style="color: #ddd; font-size: 1.1rem; line-height: 1.8;">
                                We understand the worry that comes with letting your children explore the world. That's why we created Tiny trails - not just as a product, but as a promise to keep families connected and protected.
                            </p>
                            <p style="color: #ddd; font-size: 1.1rem; line-height: 1.8;">
                                Our mission is simple: give parents peace of mind through cutting-edge technology. Every device is designed with safety, reliability, and ease of use at its core.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6 text-center">
                        <svg width="400" height="400" viewBox="0 0 400 400">
                            <circle cx="200" cy="200" r="150" fill="url(#heroGrad)" opacity="0.2" filter="url(#heroGlow)"/>
                            <text x="200" y="230" text-anchor="middle" font-size="120">👨‍👩‍👧‍👦</text>
                        </svg>
                    </div>
                </div>

                <h3 class="text-center mb-5" style="color: var(--neon-blue); text-transform: uppercase; font-size: 2.5rem;">Real Stories, Real Impact</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="emotional-card">
                            <h4>🎯 Lost & Found in Minutes</h4>
                            <p>"My 6-year-old wandered off at the mall. I was terrified. But with Tiny trails, I found him at the arcade in under 2 minutes. This device literally saved my sanity that day."</p>
                            <strong style="color: var(--neon-pink);">- Sarah M., Mother of 2</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="emotional-card">
                            <h4>💙 Independence for Special Needs</h4>
                            <p>"My daughter with autism can now walk to school independently. I have peace of mind knowing exactly where she is, and she feels like a 'big kid'. This changed our lives."</p>
                            <strong style="color: var(--neon-pink);">- David L., Father</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="emotional-card">
                            <h4>🚸 School Pickup Made Easy</h4>
                            <p>"Co-parenting is tough. Now both parents get alerts when our son arrives at school and when he's picked up. No more arguments or confusion about who has him."</p>
                            <strong style="color: var(--neon-pink);">- Jennifer K., Co-Parent</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="emotional-card">
                            <h4>🏃‍♂️ Active Kids, Relaxed Parents</h4>
                            <p>"My son plays in the neighborhood with friends. I set up geofences for safe zones. If he goes beyond them, I get instant alerts. Perfect balance of freedom and safety."</p>
                            <strong style="color: var(--neon-pink);">- Michael R., Father of 3</strong>
                        </div>
                    </div>
                </div>

                <div class="hero-content mt-5">
                    <h3 style="color: var(--neon-green); text-transform: uppercase; margin-bottom: 2rem;">🚗 Verified Driver Profiles</h3>
                    <p style="color: #ddd; font-size: 1.1rem; line-height: 1.8;">
                        Safety isn't just about technology - it's about trust. That's why every driver associated with our service undergoes comprehensive background verification:
                    </p>
                    <ul style="color: #ddd; font-size: 1.1rem; line-height: 2; list-style: none; padding-left: 0;">
                        <li>✅ <strong style="color: var(--neon-pink);">Police Background Checks</strong> - Full criminal history screening</li>
                        <li>✅ <strong style="color: var(--neon-blue);">Working With Children (WWC) Verification</strong> - Mandatory clearance</li>
                        <li>✅ <strong style="color: var(--neon-green);">Identity Verification</strong> - Multi-factor authentication</li>
                        <li>✅ <strong style="color: var(--accent);">Continuous Monitoring</strong> - Ongoing compliance checks</li>
                    </ul>
                    <p style="color: #ddd; font-size: 1.1rem; line-height: 1.8; margin-top: 2rem;">
                        <em>Your child's safety is never compromised. Every touchpoint is verified, monitored, and secure.</em>
                    </p>
                </div>
            </div>
        </section>
    </div>

    <!-- HOW IT WORKS PAGE -->
    <div id="how-it-works" class="page">
        <section class="features-section page-content">
            <div class="container" style="padding-top: 120px;">
                <h2 class="section-title">⚙️ How It Works ⚙️</h2>
                
                <div class="row mb-5">
                    <div class="col-md-3 mb-4">
                        <div class="feature-box">
                            <div class="feature-icon-gaming pink">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3>1. Choose</h3>
                            <p>Select between BLE or 4G/LTE tracking. Purchase outright or lease monthly - whatever works for your family.</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="feature-box">
                            <div class="feature-icon-gaming blue">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h3>2. Download</h3>
                            <p>Get our free app on iOS or Android. Setup takes less than 5 minutes.</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="feature-box">
                            <div class="feature-icon-gaming green">
                                <i class="fas fa-link"></i>
                            </div>
                            <h3>3. Connect</h3>
                            <p>Pair the device via Bluetooth or cellular. Simple, fast, secure.</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="feature-box">
                            <div class="feature-icon-gaming pink">
                                <i class="fas fa-heart"></i>
                            </div>
                            <h3>4. Relax</h3>
                            <p>Track, monitor, and receive instant alerts. Peace of mind, guaranteed.</p>
                        </div>
                    </div>
                </div>

                <div class="hero-content">
                    <h3 style="color: var(--neon-pink); text-transform: uppercase; margin-bottom: 2rem; text-align: center;">🎮 Advanced Features 🎮</h3>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div style="padding: 1.5rem; background: rgba(255, 16, 240, 0.1); border-radius: 15px; border: 2px solid rgba(255, 16, 240, 0.3);">
                                <h4 style="color: var(--neon-pink);">📍 Geofencing</h4>
                                <p style="color: #ddd;">Create custom safe zones (home, school, park). Get instant alerts when your child enters or exits these areas.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div style="padding: 1.5rem; background: rgba(0, 240, 255, 0.1); border-radius: 15px; border: 2px solid rgba(0, 240, 255, 0.3);">
                                <h4 style="color: var(--neon-blue);">📊 Location History</h4>
                                <p style="color: #ddd;">View complete movement history. Know where they've been and identify patterns.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div style="padding: 1.5rem; background: rgba(57, 255, 20, 0.1); border-radius: 15px; border: 2px solid rgba(57, 255, 20, 0.3);">
                                <h4 style="color: var(--neon-green);">🔔 Smart Alerts</h4>
                                <p style="color: #ddd;">Customizable notifications for arrivals, departures, low battery, and SOS button presses.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div style="padding: 1.5rem; background: rgba(255, 230, 109, 0.1); border-radius: 15px; border: 2px solid rgba(255, 230, 109, 0.3);">
                                <h4 style="color: var(--accent);">👨‍👩‍👧 Family Sharing</h4>
                                <p style="color: #ddd;">Multiple family members can track the same device. Perfect for co-parenting and extended family.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-6">
                        <div class="testimonial-card">
                            <p>"Setup was incredibly easy. My 70-year-old mother-in-law figured it out in minutes. The app is intuitive and the tracking is accurate to within meters."</p>
                            <strong>- Tom H., Tech Professional</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="testimonial-card">
                            <p>"The geofencing feature is a game-changer. I get a notification the moment my kids arrive home from school. No more wondering if they made it safely."</p>
                            <strong>- Lisa M., Working Mom</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- FAQ PAGE -->
    <div id="faq" class="page">
        <section class="page-content" style="padding: 150px 0 100px; background: rgba(10, 10, 10, 0.9);">
            <div class="container">
                <h2 class="section-title">❓ Frequently Asked Questions ❓</h2>
                <div class="accordion accordion-gaming" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                What's the difference between BLE and 4G/LTE devices?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <strong>BLE (Bluetooth Low Energy)</strong> devices work within approximately 100-meter range and are perfect for home tracking, nearby parks, and indoor locations. They're energy-efficient and cost-effective.<br><br>
                                <strong>4G/LTE devices</strong> use cellular networks for unlimited range tracking anywhere with cell coverage. Perfect for older kids who travel independently, school commutes, or when you need tracking beyond Bluetooth range.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Can I lease instead of buying outright?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Absolutely! We offer flexible monthly leasing options with no long-term commitment. This is perfect if you want to try the service before committing to a purchase, or if you prefer spreading the cost over time. Leasing includes full warranty coverage and free device replacement if needed.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Is my child's location data secure?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, absolutely. We use military-grade AES-256 encryption for all data transmission and storage. Your family's privacy is our top priority. We never sell, share, or monetize your location data. All data is stored on secure servers with multiple redundancy and backup systems. You maintain complete control and can delete all data at any time.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                What about driver verification and background checks?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                All drivers associated with our service undergo rigorous verification including:<br>
                                • Full police background checks<br>
                                • Working With Children (WWC) clearance<br>
                                • Identity verification<br>
                                • Regular compliance monitoring<br><br>
                                Safety is non-negotiable. We maintain the highest standards in the industry.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                How accurate is the tracking?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                BLE devices are accurate to within 10-30 meters depending on environment. 4G/LTE devices with GPS are accurate to within 3-10 meters in open areas. Indoor accuracy depends on signal strength but is generally excellent for locating within a building.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                What's the battery life?
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                BLE devices last 5-7 days on a single charge with normal use. 4G/LTE devices last 2-4 days depending on tracking frequency. Both devices have low-battery alerts and fast charging (full charge in under 2 hours). We also offer extended battery accessories for longer trips.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                                Do you have a mobile app?
                            </button>
                        </h2>
                        <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes! Our free app is available on iOS (iPhone/iPad) and Android devices. The app features real-time tracking, geofence management, location history, multiple device support, family sharing, and customizable alerts. It works seamlessly across all your devices.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                                What if the device is lost or damaged?
                            </button>
                        </h2>
                        <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                All devices come with a 1-year warranty covering manufacturing defects. Accidental damage protection is available for a small additional fee. If you're leasing, device replacement is included. We also offer a "Find My Device" feature that helps locate lost trackers using the last known location.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- PRIVACY POLICY PAGE -->
    <div id="privacy" class="page">
        <section class="privacy-section page-content">
            <div class="container" style="padding-top: 120px;">
                <h2 class="section-title">🔒 Privacy Policy & Legal 🔒</h2>
                <p class="text-center" style="color: #aaa; font-size: 0.9rem; margin-bottom: 3rem;">Last Updated: October 14, 2025</p>

                <div class="policy-box">
                    <h3>1. Introduction</h3>
                    <p>At Tiny trails, we take your privacy seriously. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our tracking devices and services.</p>
                </div>

                <div class="policy-box">
                    <h3>2. Information We Collect</h3>
                    <p>We collect the following types of information:</p>
                    <ul>
                        <li><strong>Location Data:</strong> Real-time and historical GPS coordinates from tracking devices</li>
                        <li><strong>Account Information:</strong> Name, email address, phone number, and password</li>
                        <li><strong>Device Information:</strong> Device IDs, firmware versions, battery status</li>
                        <li><strong>Usage Data:</strong> App interactions, feature usage, and preferences</li>
                        <li><strong>Payment Information:</strong> Processed securely through third-party payment processors</li>
                    </ul>
                </div>

                <div class="policy-box">
                    <h3>3. How We Use Your Information</h3>
                    <p>Your information is used exclusively for:</p>
                    <ul>
                        <li>Providing real-time location tracking services</li>
                        <li>Sending alerts and notifications as configured</li>
                        <li>Customer support and technical assistance</li>
                        <li>Improving our products and services</li>
                        <li>Complying with legal obligations</li>
                    </ul>
                    <p><strong>We NEVER sell, rent, or share your personal data with third parties for marketing purposes.</strong></p>
                </div>

                <div class="policy-box">
                    <h3>4. Data Security</h3>
                    <p>We implement industry-leading security measures:</p>
                    <ul>
                        <li><strong>AES-256 Encryption:</strong> Military-grade encryption for all data transmission</li>
                        <li><strong>Secure Servers:</strong> Data stored on encrypted, redundant servers</li>
                        <li><strong>Access Controls:</strong> Multi-factor authentication and role-based access</li>
                        <li><strong>Regular Audits:</strong> Third-party security assessments and penetration testing</li>
                        <li><strong>GDPR Compliant:</strong> Full compliance with international privacy regulations</li>
                    </ul>
                </div>

                <div class="policy-box">
                    <h3>5. Your Rights</h3>
                    <p>You have complete control over your data:</p>
                    <ul>
                        <li><strong>Access:</strong> Request a copy of all data we hold about you</li>
                        <li><strong>Correction:</strong> Update or correct any inaccurate information</li>
                        <li><strong>Deletion:</strong> Request permanent deletion of your data</li>
                        <li><strong>Portability:</strong> Export your data in a machine-readable format</li>
                        <li><strong>Opt-Out:</strong> Unsubscribe from non-essential communications</li>
                    </ul>
                </div>

                <div class="policy-box">
                    <h3>6. Data Retention</h3>
                    <p>We retain your data only as long as necessary:</p>
                    <ul>
                        <li>Active accounts: Data retained for the duration of service</li>
                        <li>Location history: 90 days rolling retention (configurable)</li>
                        <li>Deleted accounts: All personal data purged within 30 days</li>
                        <li>Legal requirements: Some data may be retained longer for compliance</li>
                    </ul>
                </div>

                <div class="policy-box">
                    <h3>7. Children's Privacy</h3>
                    <p>Our service is designed for parents/guardians to track their children. We do not knowingly collect personal information directly from children under 13. Parents maintain full control and responsibility for all tracking activities.</p>
                </div>

                <div class="policy-box" style="background: rgba(255, 16, 240, 0.1); border: 2px solid var(--neon-pink);">
                    <h3>⚖️ Legal Disclaimers & Liability</h3>
                    <h4 style="color: var(--neon-pink); margin-top: 1.5rem;">Service Limitations</h4>
                    <ul>
                        <li><strong>No Guarantee of Prevention:</strong> Tiny trails is a tracking tool, not a prevention device. We cannot guarantee the prevention of harm, loss, or injury.</li>
                        <li><strong>Technology Limitations:</strong> GPS and cellular services may be unavailable or inaccurate in certain conditions (indoors, remote areas, signal interference).</li>
                        <li><strong>Battery Dependency:</strong> Tracking requires adequate device battery. We are not liable for tracking failures due to depleted batteries.</li>
                        <li><strong>Network Dependency:</strong> Service requires active cellular or Bluetooth connectivity. We are not responsible for network outages or coverage limitations.</li>
                    </ul>

                    <h4 style="color: var(--neon-pink); margin-top: 1.5rem;">Liability Limitations</h4>
                    <ul>
                        <li><strong>AS-IS Service:</strong> Service provided "as is" without warranties of any kind, express or implied.</li>
                        <li><strong>Limited Liability:</strong> Our liability is limited to the amount paid for the service in the preceding 12 months.</li>
                        <li><strong>No Consequential Damages:</strong> We are not liable for indirect, incidental, special, or consequential damages.</li>
                        <li><strong>Parental Responsibility:</strong> Parents/guardians remain solely responsible for their children's safety and wellbeing.</li>
                    </ul>

                    <h4 style="color: var(--neon-pink); margin-top: 1.5rem;">User Responsibilities</h4>
                    <ul>
                        <li>Ensure device is properly charged and maintained</li>
                        <li>Use service in compliance with local laws and regulations</li>
                        <li>Obtain appropriate consent when tracking individuals</li>
                        <li>Keep account credentials secure and confidential</li>
                        <li>Report any service issues or security concerns immediately</li>
                    </ul>

                    <h4 style="color: var(--neon-pink); margin-top: 1.5rem;">Legal Compliance</h4>
                    <p>Users must comply with all applicable laws regarding tracking and monitoring. It is your responsibility to ensure lawful use of this service in your jurisdiction. Unauthorized tracking may violate privacy laws.</p>
                </div>

                <div class="policy-box">
                    <h3>8. Third-Party Services</h3>
                    <p>We use trusted third-party services for:</p>
                    <ul>
                        <li><strong>Payment Processing:</strong> Stripe, PayPal (PCI DSS compliant)</li>
                        <li><strong>Cloud Infrastructure:</strong> AWS, Google Cloud (ISO 27001 certified)</li>
                        <li><strong>Analytics:</strong> Anonymized usage data only</li>
                        <li><strong>Customer Support:</strong> Zendesk (encrypted communications)</li>
                    </ul>
                    <p>All third parties are contractually bound to protect your data and use it only for specified purposes.</p>
                </div>

                <div class="policy-box">
                    <h3>9. International Data Transfers</h3>
                    <p>Your data may be transferred to and processed in countries outside your residence. We ensure adequate protection through:</p>
                    <ul>
                        <li>Standard Contractual Clauses (SCCs)</li>
                        <li>Privacy Shield frameworks where applicable</li>
                        <li>Data localization options for enterprise customers</li>
                    </ul>
                </div>

                <div class="policy-box">
                    <h3>10. Cookies and Tracking Technologies</h3>
                    <p>We use essential cookies for authentication and functionality. You can disable non-essential cookies in your browser settings without affecting core service functionality.</p>
                </div>

                <div class="policy-box">
                    <h3>11. Updates to This Policy</h3>
                    <p>We may update this policy periodically. Material changes will be communicated via email and in-app notifications. Continued use after changes constitutes acceptance.</p>
                </div>

                <div class="policy-box" style="background: rgba(0, 240, 255, 0.1); border: 2px solid var(--neon-blue);">
                    <h3>12. Contact Us</h3>
                    <p>For privacy concerns, data requests, or questions:</p>
                    <ul>
                        <li><strong>Email:</strong> privacy@kidsafetracker.com</li>
                        <li><strong>Data Protection Officer:</strong> dpo@kidsafetracker.com</li>
                        <li><strong>Mail:</strong> Tiny trails Privacy Team, 123 Safety Street, Tech City, TC 12345</li>
                        <li><strong>Response Time:</strong> We respond to all inquiries within 48 hours</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>

    <!-- BLOG PAGE -->
    <div id="blog" class="page">
        <section class="features-section page-content">
            <div class="container" style="padding-top: 120px;">
                <h2 class="section-title">📰 Latest News & Updates 📰</h2>
                <div class="row">
                    <div class="col-md-4">
                        <div class="blog-card">
                            <div class="blog-image">🎯</div>
                            <div class="blog-content">
                                <h4>5 Essential Safety Tips Every Parent Should Know</h4>
                                <p class="date">October 10, 2025</p>
                                <p>Teaching kids about safety while using technology as a backup layer of protection. Learn how to empower your children with knowledge...</p>
                                <button class="btn-gaming-primary btn-sm" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">Read More</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="blog-card">
                            <div class="blog-image">🚀</div>
                            <div class="blog-content">
                                <h4>New Feature Alert: Advanced Geofencing</h4>
                                <p class="date">October 5, 2025</p>
                                <p>Create unlimited custom safe zones with our new geofencing system. Set up schools, parks, friends' houses, and get instant notifications...</p>
                                <button class="btn-gaming-primary btn-sm" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">Read More</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="blog-card">
                            <div class="blog-image">🌍</div>
                            <div class="blog-content">
                                <h4>Tiny trails Goes Global</h4>
                                <p class="date">September 28, 2025</p>
                                <p>We're expanding to 15 new countries! Bringing peace of mind to families worldwide with localized support and multilingual apps...</p>
                                <button class="btn-gaming-primary btn-sm" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">Read More</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="blog-card">
                            <div class="blog-image">💡</div>
                            <div class="blog-content">
                                <h4>How GPS Tracking Works: A Parent's Guide</h4>
                                <p class="date">September 20, 2025</p>
                                <p>Ever wondered how GPS tracking actually works? We break down the technology in simple terms that anyone can understand...</p>
                                <button class="btn-gaming-primary btn-sm" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">Read More</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="blog-card">
                            <div class="blog-image">🎉</div>
                            <div class="blog-content">
                                <h4>Celebrating 100,000 Families Protected</h4>
                                <p class="date">September 15, 2025</p>
                                <p>A huge milestone! We've now helped over 100,000 families stay connected and safe. Thank you for trusting us with what matters most...</p>
                                <button class="btn-gaming-primary btn-sm" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">Read More</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="blog-card">
                            <div class="blog-image">🔒</div>
                            <div class="blog-content">
                                <h4>Privacy First: Our Commitment to Your Data</h4>
                                <p class="date">September 8, 2025</p>
                                <p>Learn about our enhanced security measures, encryption standards, and why we'll never sell your data. Privacy isn't just a feature...</p>
                                <button class="btn-gaming-primary btn-sm" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">Read More</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="blog-card">
                            <div class="blog-image">👨‍👩‍👧</div>
                            <div class="blog-content">
                                <h4>Co-Parenting Made Easier with Shared Tracking</h4>
                                <p class="date">August 30, 2025</p>
                                <p>How our family sharing feature is helping divorced and separated parents coordinate better and keep everyone informed...</p>
                                <button class="btn-gaming-primary btn-sm" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">Read More</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="blog-card">
                            <div class="blog-image">🏃</div>
                            <div class="blog-content">
                                <h4>Finding Balance: Independence vs. Safety</h4>
                                <p class="date">August 22, 2025</p>
                                <p>Expert insights on giving kids age-appropriate freedom while maintaining safety. How tracking technology enables healthy independence...</p>
                                <button class="btn-gaming-primary btn-sm" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">Read More</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="blog-card">
                            <div class="blog-image">🎓</div>
                            <div class="blog-content">
                                <h4>Back to School Safety Checklist</h4>
                                <p class="date">August 15, 2025</p>
                                <p>Essential tips for the new school year. From setting up school zones to emergency protocols, make this year the safest yet...</p>
                                <button class="btn-gaming-primary btn-sm" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">Read More</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- CONTACT PAGE -->
    <div id="contact" class="page">
        <section class="contact-section page-content">
            <div class="container" style="padding-top: 120px;">
                <h2 class="section-title">💬 Get In Touch 💬</h2>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="hero-content">
                            <form id="contactForm" onsubmit="return validateCaptcha(event)">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label-gaming">Full Name *</label>
                                        <input type="text" class="form-control-gaming" placeholder="John Doe" required>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label-gaming">Email Address *</label>
                                        <input type="email" class="form-control-gaming" placeholder="john@example.com" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label-gaming">Phone Number</label>
                                        <input type="tel" class="form-control-gaming" placeholder="+1 (555) 123-4567">
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label-gaming">Inquiry Type *</label>
                                        <select class="form-control-gaming" required>
                                            <option value="">Select...</option>
                                            <option>Product Information</option>
                                            <option>Technical Support</option>
                                            <option>Billing Question</option>
                                            <option>Partnership Inquiry</option>
                                            <option>Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-gaming">Subject *</label>
                                    <input type="text" class="form-control-gaming" placeholder="Brief description of your inquiry" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-gaming">Message *</label>
                                    <textarea class="form-control-gaming" rows="6" placeholder="Tell us how we can help you..." required></textarea>
                                </div>
                                
                                <!-- Math Captcha -->
                                <div class="mb-4">
                                    <label class="form-label-gaming">Security Check *</label>
                                    <div class="captcha-box">
                                        <div class="captcha-question" id="captchaQuestion"></div>
                                        <input type="number" class="form-control-gaming" id="captchaAnswer" placeholder="Enter the answer" required style="max-width: 200px; margin: 0 auto;">
                                        <button type="button" class="btn btn-sm mt-2" onclick="generateCaptcha()" style="background: rgba(255, 16, 240, 0.2); color: white; border: 1px solid var(--neon-pink);">
                                            <i class="fas fa-sync-alt"></i> New Question
                                        </button>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn-gaming-primary">
                                        <i class="fas fa-paper-plane"></i> Send Message
                                    </button>
                                </div>
                            </form>

                            <div id="successMessage" style="display: none; margin-top: 2rem; padding: 1.5rem; background: rgba(57, 255, 20, 0.2); border: 2px solid var(--neon-green); border-radius: 15px; text-align: center;">
                                <h4 style="color: var(--neon-green); margin-bottom: 1rem;">✅ Message Sent Successfully!</h4>
                                <p style="color: #ddd;">Thank you for contacting us. We'll get back to you within 24 hours.</p>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="row mt-5">
                            <div class="col-md-4 text-center mb-4">
                                <div style="background: rgba(255, 16, 240, 0.1); padding: 2rem; border-radius: 15px; border: 2px solid rgba(255, 16, 240, 0.3); height: 100%;">
                                    <div style="font-size: 3rem; color: var(--neon-pink); margin-bottom: 1rem;">📧</div>
                                    <h5 style="color: white; text-transform: uppercase; margin-bottom: 1rem;">Email Us</h5>
                                    <p style="color: #aaa;">support@kidsafetracker.com</p>
                                </div>
                            </div>
                            <div class="col-md-4 text-center mb-4">
                                <div style="background: rgba(0, 240, 255, 0.1); padding: 2rem; border-radius: 15px; border: 2px solid rgba(0, 240, 255, 0.3); height: 100%;">
                                    <div style="font-size: 3rem; color: var(--neon-blue); margin-bottom: 1rem;">📞</div>
                                    <h5 style="color: white; text-transform: uppercase; margin-bottom: 1rem;">Call Us</h5>
                                    <p style="color: #aaa;">1-800-KIDSAFE<br>(1-800-543-7233)</p>
                                </div>
                            </div>
                            <div class="col-md-4 text-center mb-4">
                                <div style="background: rgba(57, 255, 20, 0.1); padding: 2rem; border-radius: 15px; border: 2px solid rgba(57, 255, 20, 0.3); height: 100%;">
                                    <div style="font-size: 3rem; color: var(--neon-green); margin-bottom: 1rem;">💬</div>
                                    <h5 style="color: white; text-transform: uppercase; margin-bottom: 1rem;">Live Chat</h5>
                                    <p style="color: #aaa;">Available 24/7<br>Instant Support</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="footer-section">
                        <h5>About Tiny trails</h5>
                        <p style="color: #bbb;">Next-generation tracking technology keeping families safe and connected. Trusted by over 100,000 families worldwide.</p>
                        <div class="social-icons mt-4">
                            <a href="https://facebook.com" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com" target="_blank" title="Twitter"><i class="fab fa-twitter"></i></a>
                            <a href="https://instagram.com" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="https://linkedin.com" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <a href="https://youtube.com" target="_blank" title="YouTube"><i class="fab fa-youtube"></i></a>
                            <a href="https://tiktok.com" target="_blank" title="TikTok"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <div class="footer-section">
                        <h5>Quick Links</h5>
                        <div class="footer-links">
                            <a href="#" onclick="showPage('home')">Home</a>
                            <a href="#" onclick="showPage('about')">About Us</a>
                            <a href="#" onclick="showPage('how-it-works')">How It Works</a>
                            <a href="#" onclick="showPage('faq')">FAQ</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <div class="footer-section">
                        <h5>Support</h5>
                        <div class="footer-links">
                            <a href="#" onclick="showPage('contact')">Contact Us</a>
                            <a href="#" onclick="showPage('privacy')">Privacy Policy</a>
                            <a href="#">Terms of Service</a>
                            <a href="#">Help Center</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="footer-section">
                        <h5>Newsletter</h5>
                        <p style="color: #bbb; font-size: 0.9rem;">Stay updated with the latest features and safety tips!</p>
                        <div class="input-group">
                            <input type="email" class="form-control-gaming" placeholder="Your email" style="border-radius: 25px 0 0 25px;">
                            <button class="btn-gaming-primary" style="border-radius: 0 25px 25px 0; padding: 0.5rem 1.5rem;">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            <hr style="border-color: rgba(255, 16, 240, 0.3); margin: 2rem 0;">
            <div class="text-center" style="color: #888;">
                <p>&copy; 2025 Tiny trails. All Rights Reserved. | Made with ❤️ for Families Everywhere</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Page Navigation with Loading Animation
        function showPage(pageId) {
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.add('active');
            
            setTimeout(() => {
                const pages = document.querySelectorAll('.page');
                pages.forEach(page => page.classList.remove('active'));
                
                const targetPage = document.getElementById(pageId);
                if (targetPage) {
                    targetPage.classList.add('active');
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
                
                loadingOverlay.classList.remove('active');
                
                // Close mobile menu if open
                const navbarCollapse = document.getElementById('navbarNav');
                if (navbarCollapse.classList.contains('show')) {
                    navbarCollapse.classList.remove('show');
                }
            }, 500);
        }

        // Math Captcha
        let captchaAnswer = 0;

        function generateCaptcha() {
            const num1 = Math.floor(Math.random() * 20) + 1;
            const num2 = Math.floor(Math.random() * 20) + 1;
            const operations = ['+', '-', '×'];
            const operation = operations[Math.floor(Math.random() * operations.length)];
            
            let question = `${num1} ${operation} ${num2} = ?`;
            
            switch(operation) {
                case '+':
                    captchaAnswer = num1 + num2;
                    break;
                case '-':
                    captchaAnswer = num1 - num2;
                    break;
                case '×':
                    captchaAnswer = num1 * num2;
                    break;
            }
            
            document.getElementById('captchaQuestion').textContent = question;
            document.getElementById('captchaAnswer').value = '';
        }

        function validateCaptcha(event) {
            event.preventDefault();
            const userAnswer = parseInt(document.getElementById('captchaAnswer').value);
            
            if (userAnswer === captchaAnswer) {
                document.getElementById('contactForm').style.display = 'none';
                document.getElementById('successMessage').style.display = 'block';
                
                setTimeout(() => {
                    document.getElementById('contactForm').reset();
                    document.getElementById('contactForm').style.display = 'block';
                    document.getElementById('successMessage').style.display = 'none';
                    generateCaptcha();
                }, 5000);
                
                return false;
            } else {
                alert('❌ Incorrect answer! Please try again.');
                generateCaptcha();
                return false;
            }
        }

        // Generate captcha on page load
        generateCaptcha();

        // Create floating particles
        function createParticles() {
            const bgGaming = document.querySelector('.bg-gaming');
            for (let i = 0; i < 50; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                bgGaming.appendChild(particle);
            }
        }

        createParticles();

        // Smooth scroll for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href.length > 1) {
                    e.preventDefault();
                    const element = document.querySelector(href);
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            });
        });
    </script>
</body>
</html>