<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRIMKOPKAR PRIMA - Sistem Arisan Digital PT. PRIMATEXCO INDONESIA</title>
    <meta name="description" content="Sistem Arisan Digital PRIMKOPKAR PRIMA PT. PRIMATEXCO INDONESIA - Platform arisan modern, transparan, dan terpercaya untuk keluarga besar Primatexco.">
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --secondary: #1e293b;
            --accent: #f59e0b;
            --light-gray: #f8fafc;
            --border-color: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-primary: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            background-color: #ffffff;
            line-height: 1.6;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .container {
            padding-left: 15px;
            padding-right: 15px;
        }

        /* Navbar Styles */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            padding: 1rem 0;
        }

        .navbar.scrolled {
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.12);
            padding: 0.5rem 0;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            font-weight: 800;
            color: var(--primary) !important;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .navbar-brand:hover {
            transform: scale(1.02);
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .brand-subtitle {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .brand-title {
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .navbar-brand img {
            width: 45px;
            height: 45px;
            object-fit: contain;
            margin-right: 1rem;
            border-radius: 12px;
            background: white;
            padding: 4px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
            transition: all 0.3s ease;
        }

        .navbar.scrolled .navbar-brand img {
            width: 38px;
            height: 38px;
        }

        .nav-link {
            font-weight: 600;
            color: var(--text-dark) !important;
            margin: 0 0.4rem;
            padding: 0.5rem 0.8rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            font-size: 0.95rem;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 40%;
        }

        .nav-link:hover {
            color: var(--primary) !important;
            background-color: rgba(37, 99, 235, 0.05);
        }

        .btn-login {
            background: var(--gradient-primary);
            color: white !important;
            border: none;
            padding: 0.7rem 1.75rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            margin-left: 0.75rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(37, 99, 235, 0.4);
        }

        /* Hero Section */
        .hero-section {
            margin-top: 80px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background-image: url('{{ asset('storage/img/background.webp') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
            overflow: hidden;
            padding: 4rem 0;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            color: white;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.8s ease-out;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 2rem;
            font-weight: 400;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        .hero-description {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 3rem;
            line-height: 1.8;
            animation: fadeInUp 0.8s ease-out 0.4s both;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease-out 0.6s both;
        }

        .btn-hero {
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-hero-primary {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-hero-primary:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            color: var(--primary);
        }

        .btn-hero-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-hero-outline:hover {
            background: white;
            color: var(--primary) !important;
            border-color: white;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .hero-image {
            position: relative;
            animation: fadeInRight 0.8s ease-out 0.4s both;
        }

        .hero-image img {
            width: 100%;
            max-width: 600px;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.3));
            animation: float 6s ease-in-out infinite;
        }

        /* About Section */
        .about-section {
            padding: 6rem 0;
            background: white;
            position: relative;
        }

        .section-title {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 1rem;
            text-align: center;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--gradient-primary);
            margin: 1rem auto;
            border-radius: 2px;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: var(--text-muted);
            text-align: center;
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .about-content {
            display: flex;
            align-items: center;
            gap: 4rem;
        }

        .about-text {
            flex: 1;
        }

        .about-text p {
            font-size: 1.1rem;
            line-height: 1.9;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .about-image {
            flex: 1;
            position: relative;
        }

        .about-image img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .about-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .stat-card {
            text-align: center;
            padding: 2rem;
            background: var(--light-gray);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-number {
            transform: scale(1.1);
        }

        .stat-label {
            font-size: 1rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Features Section */
        .features-section {
            padding: 6rem 0;
            background: var(--light-gray);
        }

        .features-list {
            margin-top: 4rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 4rem;
            margin-bottom: 6rem;
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .feature-item.active {
            opacity: 1;
            transform: translateY(0);
        }

        .feature-item:last-child {
            margin-bottom: 0;
        }

        /* Alternating layout - odd items: text left, image right */
        .feature-item:nth-child(odd) {
            flex-direction: row;
        }

        /* Even items: image left, text right */
        .feature-item:nth-child(even) {
            flex-direction: row-reverse;
        }

        .feature-content {
            flex: 1;
        }

        .feature-image {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .feature-image img {
            width: 100%;
            max-width: 400px;
            height: auto;
            filter: drop-shadow(0 10px 30px rgba(0, 0, 0, 0.1));
            transition: transform 0.3s ease;
        }

        .feature-image img:hover {
            transform: scale(1.05);
        }

        .feature-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .feature-description {
            font-size: 1.1rem;
            color: var(--text-muted);
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        .feature-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .feature-link:hover {
            gap: 0.75rem;
            color: var(--primary-dark);
        }

        /* CTA Section */
        .cta-section {
            padding: 6rem 0;
            background: var(--gradient-primary);
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 30% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 70% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        }

        .cta-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .cta-title {
            font-size: 3rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
        }

        .cta-description {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-cta {
            padding: 1.2rem 3rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-cta-white {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-cta-white:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            color: var(--primary);
        }

        .btn-cta-outline {
            background: transparent;
            color: white;
            border: 3px solid white;
        }

        .btn-cta-outline:hover {
            background: white;
            color: var(--primary) !important;
            border-color: white;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Footer */
        .footer {
            background: var(--secondary);
            color: white;
            padding: 4rem 0 2rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .footer-brand img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            margin-right: 1rem;
            border-radius: 10px;
            background: white;
            padding: 5px;
        }

        .footer-brand-text h3 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }

        .footer-brand-text p {
            font-size: 0.9rem;
            opacity: 0.8;
            margin: 0;
        }

        .footer-description {
            font-size: 1rem;
            opacity: 0.8;
            line-height: 1.7;
            margin-top: 1rem;
        }

        .footer-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .footer-links a:hover {
            color: white;
            transform: translateX(5px);
        }

        .footer-links a i {
            margin-right: 0.5rem;
            color: var(--primary-light);
        }

        .footer-contact p {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
            opacity: 0.8;
        }

        .footer-contact i {
            margin-right: 0.75rem;
            color: var(--primary-light);
            margin-top: 0.25rem;
            font-size: 1.1rem;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 2rem;
            text-align: center;
        }

        .footer-bottom p {
            margin: 0;
            opacity: 0.7;
            font-size: 0.95rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-link {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--primary);
            transform: translateY(-5px);
            color: white;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        /* Responsive Styles */
        
        /* Large Desktop (1200px and up) */
        @media (min-width: 1200px) {
            .container {
                max-width: 1140px;
            }
        }

        /* Tablet and below (992px and below) */
        @media (max-width: 992px) {
            .hero-section {
                padding: 3rem 0;
                min-height: auto;
            }

            .hero-title {
                font-size: 2.5rem;
                line-height: 1.3;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }

            .hero-description {
                font-size: 1rem;
            }

            .about-content {
                flex-direction: column;
                gap: 2rem;
            }

            .about-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }

            .section-title {
                font-size: 2.2rem;
            }

            .section-subtitle {
                font-size: 1.1rem;
            }

            .cta-title {
                font-size: 2.2rem;
            }

            .cta-description {
                font-size: 1.1rem;
            }

            .feature-item {
                gap: 2rem;
                margin-bottom: 4rem;
            }

            .feature-title {
                font-size: 1.7rem;
            }

            .feature-description {
                font-size: 1rem;
            }

            .feature-image img {
                max-width: 350px;
            }

            .footer-content {
                grid-template-columns: repeat(2, 1fr);
                gap: 2rem;
            }
        }

        /* Mobile and Small Tablet (768px and below) */
        @media (max-width: 768px) {
            .navbar {
                padding: 0.5rem 0;
            }

            .navbar-brand {
                font-size: 1rem;
            }

            .navbar-brand img {
                width: 35px;
                height: 35px;
                margin-right: 0.5rem;
            }

            .hero-section {
                margin-top: 60px;
                padding: 4rem 0 3rem;
                text-align: center;
                background-attachment: scroll !important; /* Fix mobile background issue */
                background-position: center top;
            }

            .hero-content {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .hero-title {
                font-size: 2rem;
                margin-bottom: 1rem;
                line-height: 1.3;
            }

            .hero-subtitle {
                font-size: 1.1rem;
                margin-bottom: 1.5rem;
            }

            .hero-description {
                font-size: 0.95rem;
                margin-bottom: 2rem;
                padding: 0 1rem;
            }

            .hero-buttons {
                flex-direction: column;
                gap: 1rem;
                width: 100%;
                max-width: 320px;
                margin: 0 auto;
            }

            .btn-hero {
                width: 100%;
                justify-content: center;
                padding: 0.875rem 2rem;
                font-size: 1rem;
            }

            .hero-image {
                margin-top: 3rem;
                display: flex;
                justify-content: center;
            }

            .hero-image img {
                max-width: 220px;
                width: 100%;
            }

            /* Sections Spacing */
            .about-section,
            .features-section,
            .cta-section {
                padding: 4rem 0;
            }

            .section-title {
                font-size: 1.75rem;
                margin-bottom: 1rem;
            }

            .section-subtitle {
                font-size: 1rem;
                margin-bottom: 2.5rem;
            }

            .about-text p {
                font-size: 1rem;
                margin-bottom: 1.25rem;
                text-align: center;
            }

            .about-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
                margin-top: 2rem;
            }

            .stat-card {
                padding: 1.5rem;
            }

            .stat-number {
                font-size: 2rem;
            }

            .stat-label {
                font-size: 0.9rem;
            }

            .cta-title {
                font-size: 1.75rem;
                margin-bottom: 1rem;
            }

            .cta-description {
                font-size: 1rem;
                margin-bottom: 2rem;
                padding: 0 1rem;
            }

            .btn-cta {
                padding: 1rem 2rem;
                font-size: 1rem;
                width: 100%;
                justify-content: center;
            }

            .cta-buttons {
                flex-direction: column;
                gap: 1rem;
                max-width: 320px;
                margin: 0 auto;
            }

            /* Features Items */
            .feature-item,
            .feature-item:nth-child(odd),
            .feature-item:nth-child(even) {
                flex-direction: column !important;
                gap: 2rem;
                margin-bottom: 4rem;
                text-align: center;
            }

            .feature-title {
                font-size: 1.5rem;
                text-align: center;
            }

            .feature-description {
                font-size: 1rem;
                text-align: center;
            }

            .feature-image img {
                max-width: 280px;
                margin: 0 auto;
            }

            /* Navbar Mobile Menu */
            .navbar-collapse {
                background: white;
                padding: 1.5rem;
                border-radius: 15px;
                margin-top: 1rem;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
                border: 1px solid var(--border-color);
            }

            .nav-link {
                margin: 0.5rem 0;
                text-align: center;
                padding: 0.75rem !important;
                background: var(--light-gray);
            }
            
            .nav-link:hover, .nav-link:focus {
                background: rgba(37, 99, 235, 0.1);
            }

            .btn-login {
                width: 100%;
                text-align: center;
                margin: 1rem 0 0 0;
                justify-content: center;
            }

            .footer {
                padding: 3rem 0 1.5rem;
                text-align: center;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }

            .footer-brand {
                justify-content: center;
                flex-direction: column;
            }
            
            .footer-brand img {
                margin-right: 0;
                margin-bottom: 1rem;
            }

            .footer-links {
                text-align: center;
            }

            .footer-links a {
                justify-content: center;
            }

            .footer-contact p {
                justify-content: center;
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
            }
            
            .footer-contact i {
                margin-right: 0;
                margin-bottom: 0.25rem;
            }

            .social-links {
                justify-content: center;
            }
        }

        /* Small Mobile (480px and below) */
        @media (max-width: 480px) {
            .navbar-brand {
                font-size: 0.9rem;
            }

            .navbar-brand span {
                display: none;
            }

            .navbar-brand::after {
                content: 'PRIMA';
                font-size: 1.2rem;
                font-weight: 800;
            }

            .hero-title {
                font-size: 1.6rem;
            }

            .hero-subtitle {
                font-size: 0.95rem;
            }

            .hero-description {
                font-size: 0.9rem;
            }

            .btn-hero {
                padding: 0.75rem 1.5rem;
                font-size: 0.95rem;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .section-subtitle {
                font-size: 0.95rem;
            }

            .about-section,
            .features-section,
            .cta-section {
                padding: 3rem 0;
            }

            .about-stats {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .stat-card {
                padding: 1.25rem;
            }

            .stat-number {
                font-size: 1.75rem;
            }

            .stat-label {
                font-size: 0.85rem;
            }

            .feature-title {
                font-size: 1.25rem;
            }

            .feature-description {
                font-size: 0.9rem;
            }

            .feature-image img {
                max-width: 220px;
            }

            .cta-title {
                font-size: 1.5rem;
            }

            .cta-description {
                font-size: 0.95rem;
            }

            .btn-cta {
                padding: 0.875rem 1.75rem;
                font-size: 0.95rem;
            }

            .footer-brand img {
                width: 40px;
                height: 40px;
            }

            .footer-title {
                font-size: 1.1rem;
            }

            .footer-bottom p {
                font-size: 0.85rem;
            }
        }

        /* Extra Small Mobile (360px and below) */
        @media (max-width: 360px) {
            .hero-title {
                font-size: 1.4rem;
            }

            .hero-subtitle {
                font-size: 0.875rem;
            }

            .section-title {
                font-size: 1.3rem;
            }

            .btn-hero,
            .btn-cta {
                font-size: 0.875rem;
                padding: 0.7rem 1.25rem;
            }

            .feature-image img {
                max-width: 180px;
            }

            .stat-number {
                font-size: 1.5rem;
            }
        }

        /* Scroll reveal animation */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <img src="{{ asset('storage/img/logo.webp') }}" alt="PRIMA Logo">
                <div class="brand-text d-none d-lg-flex">
                    <span class="brand-subtitle">PRIMKOPKAR PRIMA</span>
                    <span class="brand-title">ARISAN SEPEDA MOTOR</span>
                </div>
                <!-- Small desktop / Tablet version -->
                <div class="brand-text d-lg-none d-sm-flex">
                    <span class="brand-title">PRIMKOPKAR PRIMA</span>
                </div>
                <!-- Mobile version -->
                <span class="brand-title d-sm-none">PRIMA</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#keunggulan">Keunggulan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#bergabung">Bergabung</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register.index') }}">Daftar</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-login" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Area Peserta
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="hero-title" data-aos="fade-up" data-aos-duration="1000">Arisan Digital Terpercaya untuk Keluarga Besar <br></h1>
                     <h2 style="color: #fff;">PRIMKOPKAR "PRIMA" PT. PRIMATEXCO INDONESIA</h2>
                    <p class="hero-subtitle" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">Sistem arisan modern, transparan, dan mudah digunakan</p>
                    <p class="hero-description" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                        Bergabunglah dengan ribuan anggota PRIMKOPKAR PRIMA yang telah merasakan kemudahan 
                        sistem arisan digital kami. Kelola arisan Anda dengan mudah, aman, dan transparan.
                    </p>
                    <div class="hero-buttons" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="600">
                        <a href="{{ route('register.index') }}" class="btn btn-hero btn-hero-outline">
                            <i class="fas fa-user-plus"></i> Daftar Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-hero btn-hero-outline">
                            <i class="fas fa-sign-in-alt"></i> Area Peserta
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 hero-image">
                    <img src="{{ asset('storage/img/logo.webp') }}" alt="PRIMA Illustration" style="border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="400">
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="tentang" class="about-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up" data-aos-duration="1000">Tentang Kami</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">Mitra terpercaya untuk kebutuhan arisan Anda</p>
            
            <div class="about-content">
                <div class="about-text" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
                    <p>
                        <strong>PRIMKOPKAR PRIMA</strong> adalah sistem arisan digital terdepan yang dikembangkan 
                        khusus untuk keluarga besar PT. PRIMATEXCO INDONESIA. Kami hadir untuk memberikan 
                        solusi arisan yang modern, transparan, dan mudah diakses oleh seluruh anggota.
                    </p>
                    <p>
                        Dengan pengalaman bertahun-tahun dalam mengelola koperasi dan sistem arisan, kami 
                        memahami kebutuhan Anda akan sistem yang dapat diandalkan, aman, dan memberikan 
                        nilai tambah bagi seluruh anggota koperasi.
                    </p>
                    <p>
                        Platform kami dirancang dengan teknologi terkini untuk memastikan setiap transaksi 
                        tercatat dengan baik, transparan, dan dapat diakses kapan saja oleh para peserta.
                    </p>
                </div>
                <div class="about-image" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="400">
                    <img src="{{ asset('storage/img/logo.webp') }}" alt="About PRIMA">
                </div>
            </div>

            <div class="about-stats">
                <div class="stat-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">
                    <div class="stat-number" data-target="500" data-suffix="+">0</div>
                    <div class="stat-label">Anggota Aktif</div>
                </div>
                <div class="stat-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <div class="stat-number" data-target="20" data-suffix="+">0</div>
                    <div class="stat-label">Grup Arisan</div>
                </div>
                <div class="stat-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                    <div class="stat-number" data-target="99.9" data-suffix="%" data-decimal="1">0</div>
                    <div class="stat-label">Kepuasan Peserta</div>
                </div>
                <div class="stat-card" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                    <div class="stat-number" data-target="24" data-suffix="/7">0</div>
                    <div class="stat-label">Akses Online</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="keunggulan" class="features-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up" data-aos-duration="1000">Keunggulan Kami</h2>
            <p class="section-subtitle" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="100">Mengapa memilih arisan sepeda motor primkopkar "prima"?</p>

            <div class="features-list">
                <!-- Feature 1: Keamanan Terjamin (Odd - Text Left, Image Right) -->
                <div class="feature-item" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
                    <div class="feature-content">
                        <h3 class="feature-title">Keamanan Terjamin</h3>
                        <p class="feature-description">
                            Sistem keamanan berlapis dengan enkripsi data tingkat tinggi untuk melindungi 
                            informasi pribadi dan transaksi Anda. Data Anda tersimpan dengan aman dan terlindungi 
                            dari akses yang tidak sah.
                        </p>
                    </div>
                    <div class="feature-image">
                        <img src="{{ asset('storage/img/keamanan.webp') }}" alt="Keamanan Terjamin" loading="lazy" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="400">
                    </div>
                </div>

                <!-- Feature 2: Transparan & Akurat (Even - Image Left, Text Right) -->
                <div class="feature-item" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                    <div class="feature-content">
                        <h3 class="feature-title">Transparan & Akurat</h3>
                        <p class="feature-description">
                            Lihat riwayat transaksi, status undian, dan perkembangan arisan Anda secara 
                            real-time dengan laporan yang detail. Semua informasi keuangan dapat diakses 
                            dengan mudah dan transparan.
                        </p>
                    </div>
                    <div class="feature-image">
                        <img src="{{ asset('storage/img/transparan.webp') }}" alt="Transparan & Akurat" loading="lazy" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="400">
                    </div>
                </div>

                <!-- Feature 3: Akses Mudah (Odd - Text Left, Image Right) -->
                <div class="feature-item" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400">
                    <div class="feature-content">
                        <h3 class="feature-title">Akses Mudah</h3>
                        <p class="feature-description">
                            Akses sistem dari mana saja dan kapan saja melalui perangkat mobile atau desktop 
                            dengan tampilan yang responsif. Kelola arisan Anda dengan mudah hanya dengan 
                            beberapa klik.
                        </p>
                    </div>
                    <div class="feature-image">
                         <img src="{{ asset('storage/img/kemudahan.webp') }}" alt="Akses Mudah" loading="lazy" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="400">
                    </div>
                </div>

                <!-- Feature 4: Manajemen Profesional (Even - Image Left, Text Right) -->
                <div class="feature-item" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="500">
                    <div class="feature-content">
                        <h3 class="feature-title">Manajemen Profesional</h3>
                        <p class="feature-description">
                            Sistem pengelolaan yang profesional dengan admin berpengalaman untuk memastikan 
                            kelancaran operasional arisan. Tim kami selalu siap membantu mengelola arisan 
                            dengan efisien.
                        </p>
                    </div>
                    <div class="feature-image">
                         <img src="{{ asset('storage/img/profesional.webp') }}" alt="Manajemen Profesional" loading="lazy" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="400">
                    </div>
                </div>

                <!-- Feature 6: Dukungan 24/7 (Even - Image Left, Text Right)
                <div class="feature-item reveal">
                    <div class="feature-content">
                        <h3 class="feature-title">Dukungan 24/7</h3>
                        <p class="feature-description">
                            Tim support kami siap membantu Anda kapan saja untuk menyelesaikan pertanyaan 
                            atau masalah yang Anda hadapi. Kepuasan anggota adalah prioritas utama kami.
                        </p>
                    </div>
                    <div class="feature-image">
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==" alt="Dukungan 24/7" loading="lazy">
                    </div>
                </div> -->
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="bergabung" class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title" data-aos="zoom-in" data-aos-duration="1000">Bergabung Bersama Kami</h2>
                <p class="cta-description" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="200">
                    Jadilah bagian dari keluarga besar PRIMKOPKAR "PRIMA" dan nikmati kemudahan arisan digital 
                    yang transparan dan terpercaya. Daftar sekarang dan mulai perjalanan arisan Anda!
                </p>
                <div class="cta-buttons" data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="400">
            <a href="{{ route('register.index') }}" class="btn btn-cta btn-cta-outline">
                <i class="fas fa-user-plus"></i> Daftar Sekarang
            </a>
             <a href="{{ route('login') }}" class="btn btn-cta btn-cta-outline">
                <i class="fas fa-sign-in-alt"></i> Login Peserta
            </a>
        </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div>
                    <div class="footer-brand">
                        <div class="footer-brand-text">
                            <h3>PRIMKOPKAR "PRIMA"</h3><br>
                            <h3>PT. PRIMATEXCO INDONESIA</h3>
                        </div>
                    </div>
                    <p class="footer-description">
                        Sistem arisan sepeda motor terpercaya untuk keluarga besar Primatexco. 
                        Transparan, aman, dan mudah digunakan.
                    </p>
                </div>

                <div>
                    <h4 class="footer-title">Menu Cepat</h4>
                    <ul class="footer-links">
                        <li><a href="#tentang"><i class="fas fa-chevron-right"></i> Tentang Kami</a></li>
                        <li><a href="#keunggulan"><i class="fas fa-chevron-right"></i> Keunggulan</a></li>
                        <li><a href="#bergabung"><i class="fas fa-chevron-right"></i> Bergabung</a></li>
                        <li><a href="{{ route('login') }}"><i class="fas fa-chevron-right"></i> Login Peserta</a></li>
                        
                    </ul>
                </div>

                <div>
                    <h4 class="footer-title">Hubungi Kami</h4>
                    <div class="footer-contact">
                        <p>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>PRIMKOPKAR PRIMA<br>Jl. Urip Sumoharjo, Kebrok, Sambong, Kec. Batang, Kabupaten Batang, Jawa Tengah</span>
                        </p>
                        <p>
                            <i class="fas fa-clock"></i>
                            <span>Senin - Jumat: 08:00 - 15:00</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2026 PRIMKOPKAR PRIMA - PT. PRIMATEXCO INDONESIA. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            easing: 'ease-out-cubic',
            once: true,
            offset: 100,
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Counter animation function with easing
        function animateCounter(element) {
            const target = parseFloat(element.getAttribute('data-target'));
            const suffix = element.getAttribute('data-suffix') || '';
            const decimals = parseInt(element.getAttribute('data-decimal')) || 0;
            const duration = 2000; // Animation duration in milliseconds
            const startTime = performance.now();
            
            // Easing function (ease-out-cubic)
            const easeOutCubic = (t) => {
                return 1 - Math.pow(1 - t, 3);
            };
            
            function updateCounter(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                
                // Apply easing to progress
                const easedProgress = easeOutCubic(progress);
                const current = easedProgress * target;
                
                // Format number with decimals if needed
                const displayValue = decimals > 0 
                    ? current.toFixed(decimals) 
                    : Math.floor(current);
                
                element.textContent = displayValue + suffix;
                
                // Continue animation if not complete
                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                }
            }
            
            requestAnimationFrame(updateCounter);
        }

        // Use Intersection Observer to trigger counter animation when stat cards become visible
        const observerOptions = {
            threshold: 0.5, // Trigger when 50% of element is visible
            rootMargin: '0px'
        };

        const statObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumber = entry.target.querySelector('.stat-number');
                    if (statNumber && !statNumber.classList.contains('counted')) {
                        // Small delay to sync with AOS animation
                        setTimeout(() => {
                            animateCounter(statNumber);
                            statNumber.classList.add('counted');
                        }, 300);
                    }
                }
            });
        }, observerOptions);

        // Observe all stat cards
        document.addEventListener('DOMContentLoaded', () => {
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                statObserver.observe(card);
            });
        });

        // Close mobile menu when clicking a link
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    bootstrap.Collapse.getInstance(navbarCollapse).hide();
                }
            });
        });
    </script>
</body>
</html>
