<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>404 | Page Not Found</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Arial, Helvetica, sans-serif;
            background: #f8fafc;
            color: #1f2937;
        }

        .card {
            width: min(92vw, 520px);
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
            padding: 28px;
            text-align: center;
        }

        h1 {
            margin: 0;
            font-size: 54px;
            line-height: 1;
            color: #0f172a;
        }

        h2 {
            margin: 8px 0 12px;
            font-size: 20px;
        }

        p {
            margin: 0 0 20px;
            color: #4b5563;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-secondary {
            background: #fff;
            color: #111827;
            border-color: #d1d5db;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>404</h1>
        <h2>Page not found</h2>
        <p>The page you are looking for does not exist or has been moved.</p>

        <div class="actions">
            @php
                $previousUrl = url()->previous();
                $isValidPrevious = $previousUrl && $previousUrl !== url('/') && $previousUrl !== route('login');
            @endphp
            @if ($isValidPrevious)
                <a class="btn btn-primary" href="{{ $previousUrl }}">Go Back</a>
            @endif
            <a class="btn btn-secondary" href="{{ route('dashboard') }}">Go to Dashboard</a>
        </div>
    </div>
</body>

</html>
