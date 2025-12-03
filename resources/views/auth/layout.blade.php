<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Estate Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #05080f;
            --card: rgba(255, 255, 255, 0.02);
            --border: rgba(255, 255, 255, 0.08);
            --muted: #d0d6e6;
            --accent: #ff7a59;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Space Grotesk', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 15% 20%, rgba(255, 122, 89, 0.14), transparent 26%),
                        radial-gradient(circle at 80% 10%, rgba(125, 215, 255, 0.18), transparent 30%),
                        radial-gradient(circle at 70% 75%, rgba(255, 122, 89, 0.09), transparent 30%),
                        var(--bg);
            color: #f5f7fb;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 16px;
        }
        .card {
            width: min(460px, 100%);
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 26px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.35);
        }
        h1 { margin: 0 0 10px; font-size: 26px; }
        p { margin: 0 0 14px; color: var(--muted); line-height: 1.6; }
        label { display: block; font-size: 14px; color: var(--muted); margin-bottom: 6px; }
        input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.04);
            color: #fff;
            font-size: 15px;
            margin-bottom: 10px;
        }
        input:focus { outline: 2px solid rgba(255, 122, 89, 0.35); border-color: transparent; }
        .btn {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, var(--accent), #ffab76);
            color: #05080f;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 14px 40px rgba(255, 122, 89, 0.25);
            transition: transform 0.2s ease;
        }
        .btn:hover { transform: translateY(-1px); }
        .muted-link { color: var(--muted); text-decoration: none; }
        .muted-link:hover { color: #fff; text-decoration: underline; }
        .error { color: #ffb8a7; font-size: 13px; margin-top: -6px; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="card">
        @if ($errors->any())
            <div style="margin-bottom: 12px; padding: 10px 12px; border-radius: 12px; border: 1px solid rgba(255,122,89,0.35); background: rgba(255,122,89,0.08); color: #fff;">
                <strong style="display:block; margin-bottom: 6px;">Please fix the highlighted fields.</strong>
                <ul style="margin:0; padding-left: 18px; color: #ffd9ce; font-size: 13px; line-height: 1.5;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
</body>
</html>
