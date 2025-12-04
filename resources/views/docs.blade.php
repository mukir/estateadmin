<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documentation | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #05080f;
            --card: rgba(255, 255, 255, 0.03);
            --card-strong: rgba(255, 255, 255, 0.06);
            --border: rgba(255, 255, 255, 0.12);
            --muted: #c8d0e0;
            --accent: #ff7a59;
            --accent-2: #7dd7ff;
            --radius: 18px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Space Grotesk', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 14% 18%, rgba(255, 122, 89, 0.14), transparent 26%),
                        radial-gradient(circle at 86% 10%, rgba(125, 215, 255, 0.16), transparent 32%),
                        radial-gradient(circle at 74% 76%, rgba(255, 122, 89, 0.1), transparent 30%),
                        var(--bg);
            color: #f7f9fd;
            min-height: 100vh;
        }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            letter-spacing: 0.01em;
        }
        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            display: grid;
            place-items: center;
            color: #05080f;
            font-weight: 800;
            box-shadow: 0 12px 30px rgba(255, 122, 89, 0.28);
        }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.04);
            color: #fff;
            font-size: 13px;
        }
        .pill.accent {
            border-color: rgba(255, 122, 89, 0.45);
            background: rgba(255, 122, 89, 0.12);
            color: #ffd9ce;
        }
        .container {
            max-width: 1180px;
            margin: 0 auto;
            padding: 32px 20px 72px;
        }
        .hero {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 22px;
            align-items: start;
            margin-bottom: 24px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px;
            box-shadow: 0 22px 60px rgba(0, 0, 0, 0.35);
        }
        .card strong { color: #fff; }
        h1 { margin: 0 0 10px; font-size: clamp(30px, 4vw, 48px); letter-spacing: -0.02em; }
        h2 { margin: 0 0 12px; font-size: 24px; }
        h3 { margin: 0 0 8px; font-size: 18px; }
        p { margin: 0 0 12px; color: var(--muted); line-height: 1.6; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            color: #05080f;
            background: linear-gradient(135deg, var(--accent), #ffab76);
            border: none;
            cursor: pointer;
            box-shadow: 0 16px 40px rgba(255, 122, 89, 0.26);
        }
        .btn.secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            border: 1px solid var(--border);
        }
        .toc {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
        }
        .toc a {
            display: block;
            text-decoration: none;
            color: #fff;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.03);
            transition: transform 0.15s ease, border 0.15s ease;
            font-weight: 600;
        }
        .toc a:hover {
            transform: translateY(-1px);
            border-color: rgba(255, 122, 89, 0.35);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 14px;
        }
        .panel {
            margin-top: 26px;
        }
        ul {
            margin: 0 0 12px 0;
            padding-left: 18px;
            color: var(--muted);
            line-height: 1.6;
        }
        code, pre {
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", monospace;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: #e4e9f5;
        }
        code { padding: 2px 6px; }
        pre { padding: 10px 12px; overflow-x: auto; }
        .badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .note { font-size: 13px; color: var(--muted); }
        @media (max-width: 640px) {
            header { padding: 14px 16px; }
            .container { padding: 26px 16px 56px; }
        }
    </style>
</head>
<body>
    <header>
        <a class="brand" href="{{ route('landing') }}">
            <span class="brand-mark">E</span>
            <span>{{ config('app.name') }}</span>
        </a>
        <div class="badge-row">
            <span class="pill">Documentation</span>
            <a class="btn secondary" href="{{ route('landing') }}#trial">Start a trial</a>
            @if (Route::has('login'))
                @auth
                    <a class="btn" href="{{ url('/dashboard') }}">Go to dashboard</a>
                @else
                    <a class="btn" href="{{ route('login') }}">Log in</a>
                @endauth
            @endif
        </div>
    </header>

    <div class="container">
        <section class="hero">
            <div class="card">
                <p class="pill accent" style="margin:0 0 10px;">Customer-facing guide</p>
                <h1>{{ config('app.name') }} implementation overview</h1>
                <p>Everything a client needs to set up, operate, and scale on {{ config('app.name') }}. Use the quick links to jump to the section you need.</p>
                <div class="badge-row" style="margin-top:10px;">
                    <span class="pill">Multi-business, single database</span>
                    <span class="pill">Role-based access</span>
                    <span class="pill">Automated billing</span>
                </div>
            </div>
            <div class="card">
                <h3>Quick map</h3>
                <div class="toc">
                    <a href="#onboarding">Onboarding</a>
                    <a href="#access">Access</a>
                    <a href="#data-model">Data model</a>
                    <a href="#imports">Imports</a>
                    <a href="#billing">Billing</a>
                    <a href="#payments">Payments</a>
                    <a href="#reports">Reports</a>
                    <a href="#operations">Operations</a>
                </div>
            </div>
            <div class="card" style="background: var(--card-strong);">
                <h3>Sample login (demo)</h3>
                <p class="note">Use this on the staging/demo environment only.</p>
                <div class="badge-row" style="margin-top:8px;">
                    <span class="pill">Email: demo@estateadmin.test</span>
                    <span class="pill">Password: Password123!</span>
                    <span class="pill">Migaa admin: migaaadmin@estateadmin.test / Password123!</span>
                </div>
                <p class="note" style="margin-top:8px;">After login, pick a business or create a trial from the landing page.</p>
            </div>
        </section>

        <section id="onboarding" class="panel card">
            <h2>Onboarding flow</h2>
            <p>Use the public trial form on the landing page or call the endpoint below to spin up a new business and owner.</p>
            <pre>POST /start-trial
body:
- business_name (string, required)
- admin_name (string, required)
- email (string, required)
- phone (string)
- password (string, min 8)</pre>
            <p>Response includes the created business, owner, and <code>dashboard_url</code>. Non-JSON requests render a success screen for the client.</p>
            <ul>
                <li>Status: <code>trial</code> with 14-day end date, plan <code>basic</code>.</li>
                <li>Owner is created with Admin role; Manager, Accountant, and Viewer roles are seeded.</li>
                <li>After signup, clients land on <code>/b/{business}/dashboard</code>.</li>
            </ul>
        </section>

        <section id="access" class="panel card">
            <h2>Access and isolation</h2>
            <div class="grid">
                <div class="card" style="background: var(--card-strong);">
                    <h3>Multi-business scoping</h3>
                    <ul>
                        <li>Every request inside the app prefix uses <code>/b/{business:slug}</code> and the <code>business</code> middleware to load context.</li>
                        <li>Global scopes enforce <code>business_id</code> on queries to prevent cross-tenant access.</li>
                    </ul>
                </div>
                <div class="card" style="background: var(--card-strong);">
                    <h3>Roles and MFA</h3>
                    <ul>
                        <li>Roles: Admin, Manager, Accountant, Viewer. Attach users per business.</li>
                        <li>MFA middleware is available and can be required for sensitive routes.</li>
                        <li>Session-based auth; logins land on the correct business dashboard.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section id="data-model" class="panel card">
            <h2>Core data model</h2>
            <div class="grid">
                <div class="card">
                    <h3>Estates and units</h3>
                    <ul>
                        <li>Estates carry <code>name</code>, <code>code</code>, <code>type</code>, <code>address</code>, planned vs occupied counters.</li>
                        <li>Houses belong to an estate with <code>house_code</code>, block, type, and default service charge.</li>
                    </ul>
                </div>
                <div class="card">
                    <h3>Residents and balances</h3>
                    <ul>
                        <li>Residents connect to estate and house, hold contact details, type, and status.</li>
                        <li>Invoices and payments link to resident and house to keep statements aligned.</li>
                    </ul>
                </div>
                <div class="card">
                    <h3>Charges and invoices</h3>
                    <ul>
                        <li>Service charges can be global per estate or default per house.</li>
                        <li>Recurring run creates invoices per occupied unit with active resident.</li>
                        <li>Invoices store items, payments, status (draft/partial/paid), and balances auto-recalc.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section id="imports" class="panel card">
            <h2>Imports and templates</h2>
            <p>CSV importers speed up onboarding. Download templates then upload filled files.</p>
            <div class="grid">
                <div class="card">
                    <h3>Templates</h3>
                    <p><code>GET /b/{business}/imports/template/{type}</code> where type is <code>estates</code>, <code>houses</code>, or <code>residents</code>.</p>
                </div>
                <div class="card">
                    <h3>Upload</h3>
                    <ul>
                        <li>Estates: <code>name, code, type, address, planned_units</code>.</li>
                        <li>Houses: <code>estate_code, house_code, block, house_type, default_service_charge, is_occupied</code>.</li>
                        <li>Residents: <code>estate_code, house_code, full_name, email, phone, resident_type, status</code>.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section id="billing" class="panel card">
            <h2>Billing automation</h2>
            <div class="grid">
                <div class="card">
                    <h3>Recurring run</h3>
                    <ul>
                        <li>Route: <code>POST /b/{business}/app/invoices/run-recurring</code> (authorized users).</li>
                        <li>Creates invoices per occupied house with active resident using service charges and defaults.</li>
                    </ul>
                </div>
                <div class="card">
                    <h3>Manual invoices</h3>
                    <p>Use the UI under Invoices to create ad-hoc invoices, attach items, and deliver PDF or email.</p>
                </div>
                <div class="card">
                    <h3>Reminders</h3>
                    <p>Daily reminders can be scheduled via queue/scheduler to nudge overdue balances; statuses refresh on every payment.</p>
                </div>
            </div>
        </section>

        <section id="payments" class="panel card">
            <h2>Payments and statements</h2>
            <ul>
                <li>Route prefix: <code>/b/{business}/payments</code> for listing and posting payments.</li>
                <li>Invoices recalc totals and status after each payment add or delete.</li>
                <li>Resident statements available at <code>/b/{business}/app/residents/{resident}/statement</code> and as PDFs.</li>
                <li>MPesa/webhook slot available; plug in your collector to post payments into the same endpoint.</li>
            </ul>
        </section>

        <section id="reports" class="panel card">
            <h2>Reports and dashboards</h2>
            <div class="grid">
                <div class="card">
                    <h3>Dashboards</h3>
                    <p>Business dashboard surfaces occupancy, estates count, billing status, and quick actions.</p>
                </div>
                <div class="card">
                    <h3>Reports</h3>
                    <ul>
                        <li>Arrears by estate/aging: <code>/b/{business}/reports/arrears</code>.</li>
                        <li>Collections by method/date: <code>/b/{business}/reports/collections</code>.</li>
                        <li>Occupancy vs planned units: <code>/b/{business}/reports/occupancy</code>.</li>
                        <li>Exports: <code>/b/{business}/app/reports/export/{type}</code> (CSV).</li>
                    </ul>
                </div>
            </div>
        </section>

        <section id="operations" class="panel card">
            <h2>Operations runbook</h2>
            <div class="grid">
                <div class="card">
                    <h3>Background workers</h3>
                    <ul>
                        <li>Scheduler: <code>php artisan schedule:work</code> (or cron every minute).</li>
                        <li>Queue: <code>php artisan queue:listen --tries=1</code> for jobs and reminders.</li>
                    </ul>
                </div>
                <div class="card">
                    <h3>Environment</h3>
                    <ul>
                        <li>Mail defaults to log driver; swap to SMTP in <code>.env</code>.</li>
                        <li>Cache/session use database drivers by default for consistency.</li>
                        <li>Set <code>APP_URL</code> to your public domain for correct links in emails and PDFs.</li>
                    </ul>
                </div>
                <div class="card">
                    <h3>Support</h3>
                    <p>If anything looks off, re-run the trial flow and confirm you land on the dashboard URL provided. Use the in-app help or share the <code>dashboard_url</code> with clients.</p>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
