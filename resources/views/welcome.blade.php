<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $branding['platform_name'] ?? 'Estate Admin' }} | Multi-Business SaaS</title>
    @if (!empty($branding['favicon_url']))
        <link rel="icon" type="image/png" href="{{ $branding['favicon_url'] }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #0c111d;
            --ink-soft: #1a2233;
            --muted: #d0d6e6;
            --accent: #ff7a59;
            --accent-2: #7dd7ff;
            --bg: #05080f;
            --card: rgba(12, 17, 29, 0.75);
            --border: rgba(255, 255, 255, 0.08);
            --radius: 18px;
            --shadow: 0 25px 80px rgba(0, 0, 0, 0.35);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Space Grotesk', 'Manrope', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 15% 20%, rgba(255, 122, 89, 0.14), transparent 26%),
                        radial-gradient(circle at 80% 10%, rgba(125, 215, 255, 0.18), transparent 30%),
                        radial-gradient(circle at 70% 75%, rgba(255, 122, 89, 0.09), transparent 30%),
                        var(--bg);
            color: #f5f7fb;
        }
        header {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(12px);
            background: linear-gradient(90deg, rgba(5, 8, 15, 0.82), rgba(5, 8, 15, 0.55));
            border-bottom: 1px solid var(--border);
        }
        nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .logo-mark {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            display: grid;
            place-items: center;
            color: #05080f;
            font-weight: 800;
            box-shadow: 0 12px 30px rgba(255, 122, 89, 0.35);
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }
        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: 15px;
            padding: 6px 10px;
            border-radius: 10px;
            transition: color 0.2s ease, background 0.2s ease;
        }
        .nav-links a:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.06);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 18px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            color: #05080f;
            background: linear-gradient(135deg, var(--accent), #ffab76);
            box-shadow: 0 14px 40px rgba(255, 122, 89, 0.25);
            border: none;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .btn.secondary {
            background: rgba(255, 255, 255, 0.07);
            color: #fff;
            box-shadow: none;
            border: 1px solid var(--border);
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 16px 45px rgba(255, 122, 89, 0.32); }
        .btn.secondary:hover { transform: translateY(-1px); }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 72px 24px 96px;
        }
        .hero {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 32px;
            align-items: center;
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            font-size: 14px;
            letter-spacing: 0.01em;
        }
        .eyebrow span {
            display: inline-flex;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-2);
            box-shadow: 0 0 0 6px rgba(125, 215, 255, 0.22);
        }
        h1 {
            font-size: clamp(36px, 4vw, 56px);
            line-height: 1.05;
            margin: 16px 0;
            color: #fff;
            letter-spacing: -0.02em;
        }
        .hero p.lede {
            font-size: 18px;
            line-height: 1.7;
            color: var(--muted);
            max-width: 640px;
            margin: 0 0 28px;
        }
        .hero-actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            align-items: center;
        }
        .pulse {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--muted);
        }
        .pulse::before {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 0 10px rgba(255, 122, 89, 0.18);
            animation: ping 2s infinite;
        }
        @keyframes ping {
            0% { transform: scale(0.9); opacity: 0.75; }
            70% { transform: scale(1.4); opacity: 0; }
            100% { opacity: 0; }
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 22px;
        }
        .trial-card {
            position: relative;
            overflow: hidden;
        }
        .trial-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 20%, rgba(125, 215, 255, 0.18), transparent 45%),
                        radial-gradient(circle at 80% 80%, rgba(255, 122, 89, 0.18), transparent 45%);
            pointer-events: none;
        }
        form {
            display: grid;
            gap: 14px;
            position: relative;
            z-index: 1;
        }
        label {
            font-size: 14px;
            color: var(--muted);
        }
        input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.04);
            color: #fff;
            font-size: 15px;
        }
        input:focus {
            outline: 2px solid rgba(255, 122, 89, 0.35);
            border-color: transparent;
        }
        .mini-badges {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .mini-badges span {
            font-size: 12px;
            padding: 8px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            border: 1px solid var(--border);
        }
        .grid-3 {
            margin-top: 72px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
        }
        .stat {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0.01));
            border-radius: var(--radius);
            padding: 18px 18px 20px;
            border: 1px solid var(--border);
        }
        .stat h3 { margin: 0 0 4px; font-size: 28px; color: #fff; }
        .stat p { margin: 0; color: var(--muted); font-size: 14px; }

        .section-title {
            font-size: 26px;
            margin: 0 0 12px;
            color: #fff;
            letter-spacing: -0.01em;
        }
        .section-sub {
            color: var(--muted);
            font-size: 16px;
            margin: 0 0 18px;
            max-width: 720px;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 18px;
            margin-top: 18px;
        }
        .feature {
            padding: 18px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.04);
        }
        .feature h4 {
            margin: 0 0 8px;
            color: #fff;
            font-size: 17px;
        }
        .feature p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
            font-size: 14px;
        }
        .timeline {
            margin-top: 32px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }
        .step {
            padding: 16px;
            border-radius: var(--radius);
            border: 1px dashed var(--border);
            background: rgba(255, 255, 255, 0.03);
        }
        .step strong { color: #fff; display: block; margin-bottom: 6px; }
        .cta-band {
            margin-top: 70px;
            padding: 26px 26px;
            border-radius: var(--radius);
            background: linear-gradient(135deg, rgba(255, 122, 89, 0.12), rgba(125, 215, 255, 0.12));
            border: 1px solid var(--border);
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 18px;
        }
        .cta-band p { margin: 0; color: var(--muted); }
        .report-pills {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .pill {
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            color: #fff;
            font-size: 14px;
        }
        footer {
            margin-top: 60px;
            padding: 24px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
            border-top: 1px solid var(--border);
            background: rgba(5, 8, 15, 0.6);
        }
        @media (max-width: 900px) {
            nav { padding: 14px 18px; }
            .cta-band { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                @if (!empty($branding['logo_url']))
                    <img src="{{ $branding['logo_url'] }}" alt="{{ $branding['platform_name'] ?? 'Estate Admin' }}" style="height:72px; width:72px; object-fit:contain;">
                @else
                    <div class="logo-mark">E</div>
                @endif
            </div>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#automation">Automations</a>
                <a href="#reports">Reports</a>
                <a href="{{ route('docs') }}">Docs</a>
                <a href="#pricing">Pricing</a>
                @if (Route::has('login'))
                    @auth
                        <a class="btn secondary" href="{{ url('/dashboard') }}">Dashboard</a>
                    @else
                        <a class="btn secondary" href="{{ route('login') }}">Log in</a>
                    @endauth
                @endif
            </div>
        </nav>
    </header>

    <main class="container">
        <section class="hero">
            <div>
                <div class="eyebrow"><span></span> Cloud-native • Multi-business • One database</div>
                <h1>Manage every estate, unit, and resident in one SaaS built for property teams.</h1>
                <p class="lede">
                    Estate Admin keeps each business securely isolated while sharing one powerful platform:
                    onboarding, estates, units, residents, billing, payments, and arrears in a single control tower.
                </p>
                <div class="hero-actions">
                    <a class="btn" href="#trial">Start free trial</a>
                    <div class="pulse">Live billing + arrears insights for every business</div>
                </div>
                <div class="mini-badges">
                    <span>Subdomain or /b/{slug}</span>
                    <span>Role-based access</span>
                    <span>Automated invoices</span>
                    <span>MPesa-ready (webhook slot)</span>
                </div>
                <div class="grid-3" style="margin-top:22px;">
                    <div class="stat">
                        <h3>+18%</h3>
                        <p>Average boost in collections after automated reminders.</p>
                    </div>
                    <div class="stat">
                        <h3>5 mins</h3>
                        <p>To onboard a business with owner, roles, and starter estates.</p>
                    </div>
                    <div class="stat">
                        <h3>One DB</h3>
                        <p>Multi-tenant with strict business scopes across every query.</p>
                    </div>
                </div>
            </div>
            <div id="trial" class="card trial-card">
                <h3 style="margin:0 0 8px; color:#fff;">Launch your business in minutes</h3>
                <p style="color:var(--muted); margin:0 0 14px;">14-day trial. No credit card. We create the owner and roles for you.</p>
                <form method="POST" action="{{ route('onboarding.start') }}">
                    @csrf
                    <div>
                        <label for="business_name">Business name</label>
                        <input id="business_name" name="business_name" type="text" placeholder="Zama Estate Managers" required>
                    </div>
                    <div>
                        <label for="admin_name">Admin name</label>
                        <input id="admin_name" name="admin_name" type="text" placeholder="Jane Doe" required>
                    </div>
                    <div>
                        <label for="email">Work email</label>
                        <input id="email" name="email" type="email" placeholder="jane@zama.africa" required>
                    </div>
                    <div>
                        <label for="phone">Phone</label>
                        <input id="phone" name="phone" type="text" placeholder="+254 7XX XXX XXX">
                    </div>
                    <div>
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" placeholder="Create a strong password" required>
                    </div>
                    <button class="btn" type="submit">Start free trial</button>
                </form>
                <div class="mini-badges" style="margin-top:14px;">
                    <span>Owner account auto-created</span>
                    <span>Admin, Manager, Accountant, Viewer roles</span>
                </div>
            </div>
        </section>

        <section id="features" style="margin-top: 90px;">
            <h2 class="section-title">Everything each business needs—kept apart by design.</h2>
            <p class="section-sub">Every table carries <code>business_id</code> with global scopes and middleware to prevent cross-tenant leaks.</p>
            <div class="feature-grid">
                <div class="feature">
                    <h4>Estates & units</h4>
                    <p>Plan, import, and manage estates with unique unit codes. Occupancy auto-updates when residents move in or out.</p>
                </div>
                <div class="feature">
                    <h4>Residents & balances</h4>
                    <p>Full profiles with linked estates/houses, statements, and live balances computed from invoices and payments.</p>
                </div>
                <div class="feature">
                    <h4>Service charges</h4>
                    <p>Per-estate charge library (service charge, garbage, security, water). Drives automated invoice creation.</p>
                </div>
                <div class="feature">
                    <h4>Invoices & payments</h4>
                    <p>Unique per period and house/resident. Payments recalc totals instantly; statuses shift to paid/partial automatically.</p>
                </div>
                <div class="feature">
                    <h4>Roles per business</h4>
                    <p>Admin, Manager, Accountant, Viewer with business-scoped pivot. Drop-in gates for stricter controls.</p>
                </div>
                <div class="feature">
                    <h4>Subdomain or prefix</h4>
                    <p>Resolve business by subdomain (migaa.app.com) or URL prefix (/b/{business_slug}). Middleware locks the context.</p>
                </div>
            </div>
        </section>

        <section id="automation" style="margin-top: 78px;">
            <h2 class="section-title">Automation that bills and nudges on autopilot.</h2>
            <p class="section-sub">Monthly billing and reminders are scheduled—no spreadsheets, no manual exports.</p>
            <div class="timeline">
                <div class="step">
                    <strong>1) Monthly billing</strong>
                    Creates invoices for every occupied house with active resident; pulls estate service charges and unit defaults.
                </div>
                <div class="step">
                    <strong>2) Smart totals</strong>
                    Totals, paid, and balance recalc on every payment save/delete; status flips to partial or paid automatically.
                </div>
                <div class="step">
                    <strong>3) Reminders</strong>
                    Daily pass marks overdue items and prepares SMS/email notifications with business templates.
                </div>
                <div class="step">
                    <strong>4) Cron-ready</strong>
                    `billing:run` monthly and `billing:reminders` daily are pre-wired in the scheduler.
                </div>
            </div>
        </section>

        <section id="reports" style="margin-top: 78px;">
            <h2 class="section-title">Reporting that board members love.</h2>
            <p class="section-sub">Arrears aging, collections by method/estate, occupancy, and resident statements—per business.</p>
            <div class="report-pills">
                <div class="pill">Arrears by estate with aging buckets</div>
                <div class="pill">Collections by method/date range</div>
                <div class="pill">Occupancy vs planned units</div>
                <div class="pill">Resident statement (running balance)</div>
                <div class="pill">Export-ready endpoints (PDF/CSV slot)</div>
                <div class="pill">Business-level dashboards & KPIs</div>
            </div>
        </section>

        <section id="pricing" class="cta-band">
            <div>
                <h3 style="margin:0 0 6px; color:#fff;">Built to scale across many businesses.</h3>
                <p>Choose a plan per business, or run multiple businesses under one super admin. Trials start in “basic” and can upgrade instantly.</p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <div class="pill" style="background:rgba(255,255,255,0.12);">Basic: core estates, billing, reports</div>
                <div class="pill" style="background:rgba(255,255,255,0.12);">Standard: reminders + import tools</div>
                <div class="pill" style="background:rgba(255,255,255,0.12);">Premium: automation + webhooks</div>
            </div>
        </section>

        <footer>
            Built for estate managers, agencies, SACCOs, and property teams. Ready to go live—just migrate and run the scheduler.
        </footer>
    </main>
</body>
</html>
