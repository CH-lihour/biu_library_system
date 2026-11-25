<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Library') - {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('assets/img/logos/biu_logo.svg') }}" type="image/x-icon" />
    {{-- Theme bootstrap. Must run before first paint to avoid a white flash. --}}
    <script>
        (function () {
            var theme = 'light';
            try {
                theme = localStorage.getItem('biu-theme')
                    || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            } catch (e) {
                /* storage unavailable — fall back to light */
            }
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({ google: { families: ["Public Sans:300,400,500,600,700"] } });
    </script>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dark-mode.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/portal.css') }}" />
</head>

<body class="portal">
    <header class="portal-nav">
        <div class="portal-inner">
            <a href="{{ route('portal.home') }}" class="portal-brand">
                <img src="{{ asset('assets/img/logos/biu_logo.png') }}" alt="" height="36">
                <span>{{ config('app.name') }}</span>
            </a>
            <nav class="portal-links" aria-label="Main">
                @foreach ([
                    ['portal.home', 'Home', ['portal.home']],
                    ['portal.catalog', 'Catalog', ['portal.catalog', 'portal.book']],
                    ['portal.loans', 'My Loans', ['portal.loans']],
                    ['portal.reservations', 'Reservations', ['portal.reservations']],
                    ['portal.profile', 'Profile', ['portal.profile']],
                ] as [$route, $label, $active])
                    <a href="{{ route($route) }}" @class(['active' => request()->routeIs(...$active)])
                        @if (request()->routeIs(...$active)) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>
            <div class="portal-user">
                @include('auth.partials.theme-toggle', ['floating' => false])
                @if (auth()->user()->isStaff())
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-primary">Admin panel</a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Sign out</button>
                </form>
            </div>
        </div>
    </header>

    <main class="portal-inner portal-main">
        @foreach (['success' => 'success', 'error' => 'danger'] as $key => $tone)
            @if (session($key))
                <div class="portal-alert portal-alert-{{ $tone }}" role="alert">{{ session($key) }}</div>
            @endif
        @endforeach

        @if ($reason = auth()->user()->member->blockedReason())
            <div class="portal-alert portal-alert-warning" role="status">
                <strong>{{ $reason }}</strong>
                You can browse the catalog, but you cannot reserve or borrow yet. Please visit the library desk with your ID.
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="portal-footer">
        <div class="portal-inner">{{ config('app.name') }}</div>
    </footer>

    <script src="{{ asset('assets/js/dark-mode.js') }}"></script>
</body>

</html>
