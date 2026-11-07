<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Young Fashion | Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --primary: #c9933b;
            --primary-light: #dfb064;
            --primary-dark: #a37225;
            --bg-dark: #090c10;
            --card-bg: rgba(18, 23, 33, 0.75);
            --card-border: rgba(201, 147, 59, 0.2);
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --prowave-cyan: #00d2ff;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Glow Backgrounds */
        .ambient-glow-1 {
            position: fixed;
            top: -20%;
            left: 50%;
            transform: translateX(-50%);
            width: 700px;
            height: 500px;
            background: radial-gradient(circle, rgba(201, 147, 59, 0.15) 0%, rgba(201, 147, 59, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        .ambient-glow-2 {
            position: fixed;
            bottom: -20%;
            left: 20%;
            width: 500px;
            height: 400px;
            background: radial-gradient(circle, rgba(0, 210, 255, 0.08) 0%, rgba(0, 210, 255, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* Subtle grid pattern overlay */
        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        /* Navbar Header */
        header {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.75rem 2.5rem;
            max-width: 1300px;
            width: 100%;
            margin: 0 auto;
        }

        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .brand-logo-img {
            height: 46px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.5));
        }

        .brand-title-badge {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .brand-title-badge span {
            color: var(--primary-light);
        }

        .nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.65rem 1.4rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s ease;
            backdrop-filter: blur(10px);
        }

        .nav-btn:hover {
            background: rgba(201, 147, 59, 0.15);
            border-color: var(--primary);
            color: var(--primary-light);
            transform: translateY(-1px);
        }

        /* Hero Content */
        main {
            position: relative;
            z-index: 10;
            max-width: 900px;
            width: 90%;
            margin: auto;
            text-align: center;
            padding: 3rem 1rem;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.4rem 1.1rem;
            background: rgba(201, 147, 59, 0.1);
            border: 1px solid rgba(201, 147, 59, 0.3);
            border-radius: 9999px;
            color: var(--primary-light);
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 2rem;
            box-shadow: 0 0 20px rgba(201, 147, 59, 0.15);
        }

        .hero-badge-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--primary-light);
            box-shadow: 0 0 10px var(--primary);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        .main-heading {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: clamp(2.5rem, 6vw, 4.2rem);
            font-weight: 700;
            line-height: 1.15;
            color: #ffffff;
            margin-bottom: 1.25rem;
            letter-spacing: -0.02em;
        }

        .main-heading .gradient-text {
            background: linear-gradient(135deg, #ffffff 30%, var(--primary-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sub-description {
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: var(--text-muted);
            max-width: 650px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
            font-weight: 400;
        }

        /* Glass Action Card */
        .action-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 2.25rem 2rem;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            max-width: 680px;
            margin: 0 auto;
        }

        .action-buttons-wrap {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 0.95rem 2.2rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: 0.02em;
            box-shadow: 0 10px 25px rgba(201, 147, 59, 0.3);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(201, 147, 59, 0.45);
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            color: #ffffff;
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 0.95rem 2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            color: #e2e8f0;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.25);
            color: #ffffff;
            transform: translateY(-2px);
        }

        /* Quick Feature Badges */
        .features-row {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 1.75rem;
            flex-wrap: wrap;
        }

        .feature-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .feature-item svg {
            color: var(--primary-light);
        }

        /* Footer */
        footer {
            position: relative;
            z-index: 10;
            padding: 2rem 1.5rem;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            background: rgba(9, 12, 16, 0.85);
            backdrop-filter: blur(8px);
        }

        .footer-brand {
            font-size: 0.95rem;
            color: #cbd5e1;
            font-weight: 500;
            margin-bottom: 0.35rem;
        }

        .prowave-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.03em;
        }

        .prowave-badge .highlight {
            background: linear-gradient(90deg, #00d2ff 0%, #3a7bd5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .footer-copy {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.35rem;
        }

        @media (max-width: 640px) {
            header {
                padding: 1.25rem 1.5rem;
            }
            .action-card {
                padding: 1.75rem 1.25rem;
            }
            .btn-primary, .btn-secondary {
                width: 100%;
            }
            .features-row {
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>
    <div class="grid-overlay"></div>

    <!-- Navigation Header -->
    <header>
        <a href="{{ url('/') }}" class="brand-logo-wrap">
            @if(file_exists(public_path('assets/images/young_fashion_logo.png')))
                <img src="{{ asset('assets/images/young_fashion_logo.png') }}" alt="Young Fashion" class="brand-logo-img">
            @else
                <div class="brand-title-badge">YOUNG <span>FASHION</span></div>
            @endif
        </a>

        @auth
            <a href="{{ route('home') }}" class="nav-btn">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
        @else
            <a href="{{ route('software') }}" class="nav-btn">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Software Login
            </a>
        @endauth
    </header>

    <!-- Main Hero -->
    <main>
        <div class="hero-badge">
            <span class="hero-badge-pulse"></span>
            Official Business Portal
        </div>

        <h1 class="main-heading">
            Welcome to <br>
            <span class="gradient-text">Young Fashion</span>
        </h1>

        <p class="sub-description">
            Retail Management, Inventory Control & Point of Sale System tailored for modern fashion enterprises.
        </p>

        <div class="action-card">
            <div class="action-buttons-wrap">
                @auth
                    <a href="{{ route('home') }}" class="btn-primary">
                        <span>Go to ERP Dashboard</span>
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('software') }}" class="btn-primary">
                        <span>Access ERP Portal</span>
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                @endauth
            </div>

            <div class="features-row">
                <div class="feature-item">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    POS & Sales
                </div>
                <div class="feature-item">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Real-time Inventory
                </div>
                <div class="feature-item">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Ledgers & Accounts
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-brand">
            Welcome to Young Fashion
        </div>
        <div class="prowave-badge">
            Powered by <span class="highlight">Prowave Technologies</span>
        </div>
        <div class="footer-copy">
            &copy; {{ date('Y') }} Young Fashion. All rights reserved.
        </div>
    </footer>
</body>
</html>
