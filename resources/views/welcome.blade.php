<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TaskFlow</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --orange: #F55D00;
            --orange-light: #FF7A2B;
            --orange-pale: #FFF3EC;
            --white: #FFFFFF;
            --ink: #1A1108;
            --ink-60: #6B5C50;
            --ink-20: #E8E0DA;
            --surface: #FAFAF8;
        }

        body {
            font-family: 'Instrument Sans', sans-serif;
            background: var(--surface);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* NAV */
        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            background: white;
            border-bottom: 1px solid var(--ink-20);
        }

        .logo {
            display: flex; align-items: center; gap: 0.5rem;
            font-weight: 700; font-size: 1.1rem;
            color: var(--ink); text-decoration: none;
        }

        .logo-mark {
            width: 28px; height: 28px;
            background: var(--orange);
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
        }

        .logo-mark svg { width: 15px; height: 15px; }

        .nav-links { display: flex; align-items: center; gap: 0.5rem; }

        .btn-login {
            padding: 0.45rem 1rem;
            font-size: 0.875rem; font-weight: 500;
            color: var(--ink);
            background: transparent;
            border: 1px solid var(--ink-20);
            border-radius: 6px; cursor: pointer;
            text-decoration: none;
            transition: border-color 0.15s;
        }
        .btn-login:hover { border-color: var(--orange); color: var(--orange); }

        .btn-register {
            padding: 0.45rem 1rem;
            font-size: 0.875rem; font-weight: 600;
            color: white;
            background: var(--orange);
            border: 1px solid var(--orange);
            border-radius: 6px; cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
        }
        .btn-register:hover { background: var(--orange-light); }

        /* HERO */
        .hero {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4rem 2rem;
            text-align: center;
        }

        .hero-inner { max-width: 560px; }

        .hero-badge {
            display: inline-block;
            padding: 0.3rem 0.85rem;
            background: var(--orange-pale);
            color: var(--orange);
            font-size: 0.75rem; font-weight: 600;
            border-radius: 100px;
            margin-bottom: 1.5rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .hero-h1 {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 700;
            line-height: 1.15;
            color: var(--ink);
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .hero-h1 span { color: var(--orange); }

        .hero-sub {
            font-size: 1rem;
            color: var(--ink-60);
            line-height: 1.65;
            margin-bottom: 2rem;
        }

        .hero-cta {
            display: flex; align-items: center; justify-content: center;
            gap: 0.75rem; flex-wrap: wrap;
        }

        .cta-primary {
            padding: 0.75rem 1.75rem;
            font-size: 0.95rem; font-weight: 600;
            color: white; background: var(--orange);
            border: none; border-radius: 8px;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(245,93,0,0.25);
            transition: background 0.15s, transform 0.15s;
        }
        .cta-primary:hover { background: var(--orange-light); transform: translateY(-1px); }

        .cta-secondary {
            padding: 0.75rem 1.5rem;
            font-size: 0.95rem; font-weight: 500;
            color: var(--ink);
            border: 1px solid var(--ink-20);
            border-radius: 8px; text-decoration: none;
            transition: border-color 0.15s;
        }
        .cta-secondary:hover { border-color: var(--orange); color: var(--orange); }

        /* FEATURES */
        .features {
            background: white;
            border-top: 1px solid var(--ink-20);
            padding: 3.5rem 2rem;
        }

        .features-inner { max-width: 900px; margin: 0 auto; }

        .features-label {
            text-align: center;
            font-size: 0.75rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em;
            color: var(--orange); margin-bottom: 0.5rem;
        }

        .features-title {
            text-align: center;
            font-size: 1.5rem; font-weight: 700;
            color: var(--ink); margin-bottom: 2.5rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
        }

        .feature-card {
            padding: 1.5rem;
            border: 1px solid var(--ink-20);
            border-radius: 10px;
            background: var(--surface);
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .feature-card:hover {
            border-color: var(--orange);
            box-shadow: 0 4px 16px rgba(245,93,0,0.08);
        }

        .feature-icon {
            font-size: 1.3rem; margin-bottom: 0.75rem;
        }

        .feature-h3 {
            font-size: 0.9rem; font-weight: 600; color: var(--ink);
            margin-bottom: 0.35rem;
        }

        .feature-p { font-size: 0.825rem; color: var(--ink-60); line-height: 1.55; }

        /* FOOTER */
        footer {
            padding: 1.25rem 2rem;
            text-align: center;
            font-size: 0.8rem; color: var(--ink-60);
            border-top: 1px solid var(--ink-20);
        }

        @media (max-width: 600px) {
            .features-grid { grid-template-columns: 1fr; }
            .hero { padding: 3rem 1.25rem; }
            .features { padding: 2.5rem 1.25rem; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <a href="/" class="logo">
        <div class="logo-mark">
            <svg viewBox="0 0 18 18" fill="none">
                <path d="M3 9L7 13L15 5" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        TaskFlow
    </a>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-badge">Task Management & Collaboration</div>
        <h1 class="hero-h1">Manage tasks.<br><span>Ship together.</span></h1>
        <p class="hero-sub">
            TaskFlow keeps your team's work organized, assigned, and on track — from first idea to final slay.
        </p>
        <div class="hero-cta">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="cta-primary">Sign Up</a>
            @endif
            @if (Route::has('login'))
                <a href="{{ route('login') }}" class="cta-secondary">Log in</a>
            @endif
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="features">
    <div class="features-inner">
        <div class="features-label">What you get</div>
        <h2 class="features-title">Everything your team needs</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3 class="feature-h3">Team collaboration</h3>
                <p class="feature-p">Assign tasks, leave comments, and keep everyone on the same page.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3 class="feature-h3">Progress tracking</h3>
                <p class="feature-p">See where each project stands at a glance. No spreadsheets needed.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔔</div>
                <h3 class="feature-h3">Deadline alerts</h3>
                <p class="feature-p">Get notified before things fall behind — not after.</p>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    © {{ date('Y') }} TaskFlow. All rights reserved.
</footer>

</body>
</html>