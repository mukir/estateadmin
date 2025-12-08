<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $branding['platform_name'] ?? 'Estate Admin' }} | Estate SaaS</title>
    @if (!empty($branding['favicon_url']))
        <link rel="icon" type="image/png" href="{{ $branding['favicon_url'] }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f7f8fc;
            --bg-soft: #eef2ff;
            --card: #ffffff;
            --stroke: rgba(15, 23, 42, 0.08);
            --text: #0f172a;
            --muted: #5b6178;
            --accent: #f9b042;
            --accent-2: #5c7cfa;
            --accent-3: #0d9488;
            --radius: 20px;
            --shadow: 0 30px 70px rgba(15, 23, 42, 0.12);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Sora', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at 15% 20%, rgba(92, 124, 250, 0.14), transparent 45%),
                        radial-gradient(circle at 80% 0%, rgba(249, 176, 66, 0.18), transparent 45%),
                        var(--bg);
            color: var(--text);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        .noise {
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200' viewBox='0 0 200 200'%3E%3Crect width='200' height='200' fill='%23ffffff'/%3E%3Cpath d='M0 1h1v1H0z' fill='rgba(15,23,42,0.05)'/%3E%3C/svg%3E");
            opacity: 0.35;
        }
        header {
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid var(--stroke);
        }
        nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
            color: var(--text);
        }
        .brand-mark {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--accent), #ffe1a6);
            color: #1c1917;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 20px;
            box-shadow: 0 18px 50px rgba(249, 176, 66, 0.35);
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .nav-links a {
            text-decoration: none;
            font-size: 15px;
            color: var(--muted);
            padding: 6px 12px;
            border-radius: 999px;
            transition: color 0.2s ease, background 0.2s ease;
        }
        .nav-links a:hover {
            color: var(--text);
            background: rgba(92, 124, 250, 0.15);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 14px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
            font-size: 15px;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #ffd580);
            color: #1c1917;
            box-shadow: 0 18px 40px rgba(249, 176, 66, 0.35);
        }
        .btn-outline {
            border: 1px solid rgba(92, 124, 250, 0.3);
            color: var(--accent-2);
            background: rgba(92, 124, 250, 0.08);
        }
        .btn:hover { transform: translateY(-1px); }
        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 24px 100px;
        }
        .hero-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 36px;
            align-items: stretch;
        }
        .hero-copy h1 {
            font-size: clamp(40px, 5vw, 64px);
            line-height: 1.05;
            margin: 16px 0;
            color: var(--text);
        }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(92, 124, 250, 0.1);
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 14px;
            color: var(--accent-2);
        }
        .eyebrow span {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--accent-3);
            box-shadow: 0 0 0 10px rgba(13, 148, 136, 0.2);
        }
        .lede {
            font-size: 18px;
            line-height: 1.6;
            color: var(--muted);
            margin-bottom: 24px;
            max-width: 640px;
        }
        .hero-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        .hero-meta {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            font-size: 14px;
            color: var(--muted);
        }
        .tag {
            background: rgba(92, 124, 250, 0.08);
            border: 1px solid rgba(92, 124, 250, 0.25);
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 13px;
            color: var(--accent-2);
        }
        .hero-card {
            background: var(--card);
            border: 1px solid var(--stroke);
            border-radius: var(--radius);
            padding: 32px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }
        .hero-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 15%, rgba(92, 124, 250, 0.12), transparent 45%);
            pointer-events: none;
        }
        .hero-card h3 { margin: 0 0 8px; color: var(--text); }
        .hero-card form { position: relative; z-index: 1; }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 18px;
            margin-top: 30px;
        }
        .stat {
            padding: 18px;
            border-radius: 16px;
            background: linear-gradient(145deg, rgba(92, 124, 250, 0.08), rgba(249, 176, 66, 0.12));
            border: 1px solid var(--stroke);
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }
        .stat h4 { margin: 0; font-size: 24px; color: var(--text); }
        .stat p { margin: 6px 0 0; color: var(--muted); font-size: 14px; }
        .section { margin-top: 90px; }
        .section small {
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--muted);
            font-size: 13px;
        }
        .section h2 {
            margin: 12px 0;
            font-size: clamp(28px, 4vw, 40px);
            color: var(--text);
        }
        .section > p {
            max-width: 650px;
            color: var(--muted);
            line-height: 1.6;
        }
        .feature-grid {
            margin-top: 32px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        .feature-card {
            background: var(--card);
            border: 1px solid var(--stroke);
            border-radius: 18px;
            padding: 20px;
            min-height: 180px;
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.05);
        }
        .feature-card h4 { margin: 0 0 8px; color: var(--text); }
        .feature-card p { margin: 0; color: var(--muted); font-size: 14px; }
        .timeline {
            margin-top: 32px;
            display: grid;
            gap: 16px;
        }
        .timeline-item {
            display: flex;
            gap: 20px;
            background: var(--card);
            border-radius: 16px;
            padding: 18px 22px;
            border: 1px solid var(--stroke);
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.05);
        }
        .timeline-item strong { min-width: 140px; color: var(--text); }
        .timeline-item p { margin: 0; color: var(--muted); }
        .report-grid {
            margin-top: 32px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }
        .report-card {
            padding: 24px;
            border-radius: 18px;
            border: 1px solid var(--stroke);
            background: var(--card);
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.04);
        }
        .report-card ul {
            padding-left: 18px;
            margin: 8px 0 0;
            color: var(--muted);
        }
        .pricing-grid {
            margin-top: 32px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }
        .pricing-card {
            padding: 28px;
            border-radius: 22px;
            border: 1px solid var(--stroke);
            background: var(--card);
            position: relative;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
        }
        .pricing-card.highlight {
            border-color: rgba(249, 176, 66, 0.6);
            box-shadow: 0 30px 60px rgba(249, 176, 66, 0.25);
        }
        .pricing-card h3 { margin: 0 0 12px; color: var(--text); }
        .pricing-card .price { font-size: 32px; margin: 0 0 12px; color: var(--text); }
        .pricing-card ul {
            list-style: none;
            padding: 0;
            margin: 0 0 18px;
            color: var(--muted);
            line-height: 1.6;
        }
        .testimonial-grid {
            margin-top: 34px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .testimonial {
            padding: 22px;
            background: var(--card);
            border-radius: 18px;
            border: 1px solid var(--stroke);
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.05);
        }
        .testimonial p { margin: 0 0 12px; color: var(--muted); }
        .testimonial strong { font-size: 14px; color: var(--text); }
        form {
            display: grid;
            gap: 14px;
            margin-top: 18px;
        }
        label {
            font-size: 13px;
            color: var(--muted);
        }
        input, button { font-family: inherit; }
        input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, 0.6);
            background: #f9fafb;
            color: var(--text);
        }
        input::placeholder {
            color: rgba(15, 23, 42, 0.5);
        }
        input:focus {
            outline: 2px solid rgba(92, 124, 250, 0.35);
            outline-offset: 1px;
        }
        footer {
            margin-top: 90px;
            padding: 30px;
            text-align: center;
            color: var(--muted);
            font-size: 14px;
            border-top: 1px solid var(--stroke);
            background: var(--card);
        }
        @media (max-width: 720px) {
            nav {
                flex-wrap: wrap;
                justify-content: center;
            }
            header { position: static; }
            .timeline-item { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="noise"></div>
    <header>
        <nav>
            <div class="brand">
                @if (!empty($branding['logo_url']))
                    <img src="{{ $branding['logo_url'] }}" alt="{{ $branding['platform_name'] ?? 'Estate Admin' }}" style="height:64px;width:64px;object-fit:contain;">
                @else
                    <div class="brand-mark">EA</div>
                @endif
                <div>
                    <div>{{ $branding['platform_name'] ?? 'Estate Admin' }}</div>
                    <small style="color:var(--muted);">Enterprise estate OS</small>
                </div>
            </div>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#automation">Automation</a>
                <a href="#reports">Reports</a>
                <a href="#pricing">Pricing</a>
                <a href="{{ route('docs') }}">Docs</a>
                @if (Route::has('login'))
                    @auth
                        <a class="btn btn-outline" href="{{ url('/dashboard') }}">Dashboard</a>
                    @else
                        <a class="btn btn-outline" href="{{ route('login') }}">Log in</a>
                    @endauth
                @endif
                <a class="btn btn-primary" href="#trial">Start free trial</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero-grid">
            <div class="hero-copy">
                <div class="eyebrow"><span></span>Multi-business estate platform</div>
                <h1>Premium-grade property ops for every business you onboard.</h1>
                <p class="lede">
                    Launch a dedicated workspace for each estate company with airtight tenant isolation,
                    live billing, payments, and occupancy signals that your finance and operations teams trust.
                </p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="#trial">Start 14-day trial</a>
                    <a class="btn btn-outline" href="{{ route('docs') }}">View product docs</a>
                </div>
                <div class="hero-meta">
                    <span class="tag">Fine-grained roles per business</span>
                    <span class="tag">Scheduler-ready billing jobs</span>
                    <span class="tag">Single database, scoped queries</span>
                </div>
                <div class="stat-grid">
                    <div class="stat">
                        <h4>+21%</h4>
                        <p>Average boost in collections after automated reminders go live.</p>
                    </div>
                    <div class="stat">
                        <h4>5 minutes</h4>
                        <p>Create a business, import estates, and invite managers.</p>
                    </div>
                    <div class="stat">
                        <h4>Zero leaks</h4>
                        <p>Every query scoped via middleware and global scopes.</p>
                    </div>
                </div>
            </div>
            <div class="hero-card" id="trial">
                <h3>Launch a workspace in minutes</h3>
                <p style="color:var(--muted); margin:0;">Owner user + key roles auto-created. No card required.</p>
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
                    <button class="btn btn-primary" type="submit">Start free trial</button>
                </form>
                <div style="margin-top:16px; display:flex; flex-wrap:wrap; gap:10px; position:relative; z-index:1;">
                    <span class="tag">Owner + Admin + Manager + Accountant roles</span>
                    <span class="tag">Resident + house import templates</span>
                </div>
            </div>
        </section>

        <section class="section" id="features">
            <small>Platform</small>
            <h2>Everything operations needs, finished for enterprise property teams.</h2>
            <p>
                Estate Admin is designed for agencies, SACCOs, and developer groups running dozens of estates.
                Each workspace inherits consistent best practices so you can scale service without scaling headcount.
            </p>
            <div class="feature-grid">
                <div class="feature-card">
                    <h4>Estate hierarchy</h4>
                    <p>Import estates and houses with CSV templates or API hooks. Occupancy updates when residents move.</p>
                </div>
                <div class="feature-card">
                    <h4>Resident ledger</h4>
                    <p>Every resident maintains a running balance that is recalculated after each invoice and payment.</p>
                </div>
                <div class="feature-card">
                    <h4>Service charge library</h4>
                    <p>Build estate-specific charge sets that drive automated billing cycles and arrears calculations.</p>
                </div>
                <div class="feature-card">
                    <h4>Payments + MPesa ready</h4>
                    <p>Capture confirmations manually or wire up a webhook slot for mobile money receipts.</p>
                </div>
                <div class="feature-card">
                    <h4>Roles and approvals</h4>
                    <p>Owner, Admin, Manager, Accountant, and Viewer roles keep finance in control without slowing ops.</p>
                </div>
                <div class="feature-card">
                    <h4>Subdomain or prefix</h4>
                    <p>Serve each business via subdomain or <code>/b/{slug}</code>. Middleware locks the active workspace.</p>
                </div>
            </div>
        </section>

        <section class="section" id="automation">
            <small>Automation</small>
            <h2>The billing engine runs every day without spreadsheets.</h2>
            <p>
                Built-in scheduler commands keep invoices flowing, reminders nudging, and balances always current.
                Hook into the queue or run via cron with zero custom scripts.
            </p>
            <div class="timeline">
                <div class="timeline-item">
                    <strong>Monthly billing</strong>
                    <p>Command `billing:run` maps every occupied house to the right charges, builds invoices, and stamps due dates.</p>
                </div>
                <div class="timeline-item">
                    <strong>Smart totals</strong>
                    <p>Payments recalculate totals automatically and flip invoice status to draft, partial, or paid with no manual math.</p>
                </div>
                <div class="timeline-item">
                    <strong>Automated nudges</strong>
                    <p>`billing:reminders` tags overdue accounts, personalizes templates, and logs every chase.</p>
                </div>
                <div class="timeline-item">
                    <strong>Audit-friendly</strong>
                    <p>Every action is scoped by business, recorded with timestamps, and ready for board review.</p>
                </div>
            </div>
        </section>

        <section class="section" id="reports">
            <small>Insight</small>
            <h2>Reporting that board members, auditors, and lenders all trust.</h2>
            <p>
                Rich dashboards and exports keep leadership on the same page. Every KPI is filterable per business,
                per estate, and over any date range.
            </p>
            <div class="report-grid">
                <div class="report-card">
                    <h4>Arrears cockpit</h4>
                    <ul>
                        <li>Aging buckets (0-30, 31-60, 60+)</li>
                        <li>Trends by estate and resident cohorts</li>
                        <li>Export-ready CSV and PDF slots</li>
                    </ul>
                </div>
                <div class="report-card">
                    <h4>Collections intel</h4>
                    <ul>
                        <li>Compare MPesa, bank, and cash methods</li>
                        <li>Spot dips with rolling averages</li>
                        <li>Tag exceptions for follow-up</li>
                    </ul>
                </div>
                <div class="report-card">
                    <h4>Occupancy + health</h4>
                    <ul>
                        <li>Planned vs actual units per estate</li>
                        <li>Vacancy alerts when a resident exits</li>
                        <li>Resident statements with running balance</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="section" id="pricing">
            <small>Pricing</small>
            <h2>Simple plans. Upgrade a business the moment it is ready.</h2>
            <div class="pricing-grid">
                <div class="pricing-card">
                    <h3>Basic</h3>
                    <p class="price">Ksh 6k / business</p>
                    <ul>
                        <li>Estates, houses, residents</li>
                        <li>Manual invoicing + payments</li>
                        <li>Core dashboards and exports</li>
                    </ul>
                    <a class="btn btn-outline" href="#trial">Start trial</a>
                </div>
                <div class="pricing-card highlight">
                    <h3>Standard</h3>
                    <p class="price">Ksh 12k / business</p>
                    <ul>
                        <li>Automation commands enabled</li>
                        <li>Reminder workflows + templates</li>
                        <li>Role-based approvals</li>
                    </ul>
                    <a class="btn btn-primary" href="#trial">Talk to sales</a>
                </div>
                <div class="pricing-card">
                    <h3>Premium</h3>
                    <p class="price">Custom</p>
                    <ul>
                        <li>Webhooks & bespoke integrations</li>
                        <li>Priority support + success manager</li>
                        <li>Dedicated reporting workspace</li>
                    </ul>
                    <a class="btn btn-outline" href="{{ route('docs') }}">See specs</a>
                </div>
            </div>
        </section>

        <section class="section">
            <small>Proof</small>
            <h2>Teams already moving arrears down and occupancy up.</h2>
            <div class="testimonial-grid">
                <div class="testimonial">
                    <p>"We migrated three SACCOs in two weeks. Having every business in one dashboard with clean separation is a dream."</p>
                    <strong>Joan, Director at Umoja Property</strong>
                </div>
                <div class="testimonial">
                    <p>"Billing jobs plus reminders dropped our arrears by 24% without growing the finance team. Ops finally breathes."</p>
                    <strong>Daniel, COO at Lynk Estates</strong>
                </div>
                <div class="testimonial">
                    <p>"Role templates mean onboarding a new business owner is fast, and our auditors get the logs they need instantly."</p>
                    <strong>Ashley, Finance Lead at Terranova</strong>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="pricing-card" style="display:flex; flex-direction:column; align-items:flex-start; gap:16px;">
                <small>Next step</small>
                <h2 style="margin:0;">Ready to onboard your estate businesses?</h2>
                <p style="margin:0; color:var(--muted);">
                    Spin up a workspace, migrate data, and run billing the same day. Estate Admin ships with docs,
                    import templates, and cron-ready commands so you can focus on growth.
                </p>
                <div style="display:flex; gap:16px; flex-wrap:wrap;">
                    <a class="btn btn-primary" href="#trial">Start free trial</a>
                    <a class="btn btn-outline" href="{{ route('docs') }}">See implementation guide</a>
                </div>
            </div>
        </section>

        <footer>
            Built for estate managers, agencies, SACCOs, and property teams. Deploy, migrate, and run the scheduler.
        </footer>
    </main>
</body>
</html>
