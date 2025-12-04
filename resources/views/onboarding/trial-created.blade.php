<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trial created | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #05080f;
            --card: rgba(255, 255, 255, 0.03);
            --border: rgba(255, 255, 255, 0.1);
            --muted: #c8d0e0;
            --accent: #ff7a59;
            --accent-2: #7dd7ff;
            --success: #34d399;
            --radius: 18px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Space Grotesk', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 12% 18%, rgba(255, 122, 89, 0.14), transparent 24%),
                        radial-gradient(circle at 78% 12%, rgba(125, 215, 255, 0.16), transparent 30%),
                        radial-gradient(circle at 70% 78%, rgba(255, 122, 89, 0.08), transparent 32%),
                        var(--bg);
            color: #f7f9fd;
            min-height: 100vh;
        }
        header {
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(10px);
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            letter-spacing: 0.01em;
            color: #fff;
            text-decoration: none;
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
            box-shadow: 0 12px 30px rgba(255, 122, 89, 0.3);
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 36px 20px 64px;
        }
        .hero {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 26px;
            align-items: start;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: 0 22px 60px rgba(0, 0, 0, 0.35);
        }
        h1 { margin: 0 0 10px; font-size: 28px; }
        p { margin: 0 0 12px; color: var(--muted); line-height: 1.6; }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid var(--border);
            color: #fff;
            background: rgba(255, 255, 255, 0.04);
            font-size: 13px;
        }
        .pill.success {
            background: rgba(52, 211, 153, 0.12);
            color: #bbf7d0;
            border-color: rgba(52, 211, 153, 0.3);
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 14px;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 0;
            border-bottom: 1px dashed var(--border);
        }
        .meta:last-child { border-bottom: none; }
        .meta small { color: var(--muted); display: block; margin-bottom: 4px; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 18px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            color: #05080f;
            background: linear-gradient(135deg, var(--accent), #ffab76);
            border: none;
            cursor: pointer;
            box-shadow: 0 16px 40px rgba(255, 122, 89, 0.25);
        }
        .stack { display: flex; flex-direction: column; gap: 12px; }
        .panel-title { margin: 0 0 6px; font-size: 16px; }
        .note { font-size: 13px; color: var(--muted); }
        .link {
            color: #fff;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        @media (max-width: 640px) {
            header { padding: 16px; }
            .container { padding: 26px 16px 48px; }
        }
    </style>
</head>
<body>
    <header>
        <a class="brand" href="{{ route('landing') }}">
            <span class="brand-mark">E</span>
            <span>{{ config('app.name') }}</span>
        </a>
        <div class="pill success">Trial activated</div>
    </header>

    <div class="container">
        <div class="hero">
            <div class="stack">
                <div class="card">
                    <h1>You're in. Trial created.</h1>
                    <p>{{ $message }}</p>
                    <div class="pill">Dashboard ready: {{ $business->name }}</div>
                    <a class="btn" href="{{ $dashboard_url }}">Go to dashboard</a>
                    <p class="note">Save this link for your team: <a class="link" href="{{ $dashboard_url }}">{{ $dashboard_url }}</a></p>
                </div>
                <div class="card">
                    <h3 class="panel-title">Next steps</h3>
                    <div class="stack">
                        <div class="pill">Invite teammates from Settings → Team</div>
                        <div class="pill">Add estates and units, then residents</div>
                        <div class="pill">Set service charges and run first invoices</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 class="panel-title">Business details</h3>
                <div class="meta">
                    <div><small>Name</small>{{ $business->name }}</div>
                    <div><small>Plan</small>{{ ucfirst($business->plan) }}</div>
                </div>
                <div class="meta">
                    <div><small>Slug</small>{{ $business->slug }}</div>
                    <div><small>Status</small>{{ ucfirst($business->status) }}</div>
                </div>
                <div class="meta">
                    <div><small>Trial ends</small>{{ optional($business->trial_ends_at)->format('M j, Y') }}</div>
                    <div><small>Contact</small>{{ $business->contact_email }}</div>
                </div>
                <div class="meta">
                    <div><small>Phone</small>{{ $business->contact_phone ?: 'Not provided' }}</div>
                    <div><small>Created</small>{{ optional($business->created_at)->format('M j, Y H:i') }}</div>
                </div>
                <h3 class="panel-title" style="margin-top:16px;">Owner</h3>
                <div class="grid">
                    <div class="card" style="background: rgba(255,255,255,0.02); border-style: dashed;">
                        <div class="meta" style="border-bottom:none; padding:0;">
                            <div><small>Name</small>{{ $owner->name }}</div>
                            <div><small>Email</small>{{ $owner->email }}</div>
                        </div>
                        <div class="note" style="margin-top:8px;">Owner account created with admin rights.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
