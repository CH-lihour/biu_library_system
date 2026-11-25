<!-- Sidebar -->
<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="{{ route('dashboard') }}" class="logo">
                <img src="{{asset('assets/img/logos/header_logo.png')}}" alt="navbar brand" class="navbar-brand" height="45" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">

            @php
                // [title, icon, id, [[label, route], ...]] - a link shows only when the role opens it
                $menu = [
                    ['Book', 'icon-book-open', 'base', [
                        ['All Books', 'books.index'],
                        ['Book Copies', 'book-copies.index'],
                        ['Authors', 'authors.index'],
                        ['Publishers', 'publishers.index'],
                        ['Categories', 'categories.index'],
                    ]],
                    ['Members', 'fas fa-th-list', 'members', [
                        ['Members', 'members.index'],
                        ['Member Plans', 'member-plans.index'],
                    ]],
                    ['Book Transactions', 'fas fa-arrow-circle-right', 'bookTransactions', [
                        ['Borrow Books', 'borrows.index'],
                        ['Reservations', 'reservations.index'],
                    ]],
                    ['Administration', 'fas fa-user-shield', 'administration', [
                        ['Staff Accounts', 'users.index'],
                        ['Roles & Permissions', 'roles.index'],
                    ]],
                ];
            @endphp

            <ul class="nav nav-secondary" id="sidebarMenuAccordion">
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? '' : 'collapsed' }}" aria-expanded="{{ request()->routeIs('dashboard') ? 'true' : 'false' }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Menu</h4>
                </li>
                @foreach ($menu as [$title, $icon, $id, $links])
                    @php
                        $links = array_filter($links, fn ($link) => can_access($link[1]));
                        $patterns = array_map(fn ($link) => \Illuminate\Support\Str::before($link[1], '.') . '.*', $links);
                        $isActive = $links && request()->routeIs(...$patterns);
                    @endphp
                    @continue(! $links)
                    <li class="nav-item {{ $isActive ? 'active submenu' : '' }}">
                        <a data-bs-toggle="collapse" href="#{{ $id }}" class="{{ $isActive ? '' : 'collapsed' }}" aria-expanded="{{ $isActive ? 'true' : 'false' }}">
                            <i class="{{ $icon }}"></i>
                            <p>{{ $title }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ $isActive ? 'show' : '' }}" id="{{ $id }}" data-bs-parent="#sidebarMenuAccordion">
                            <ul class="nav nav-collapse">
                                @foreach ($links as [$label, $route])
                                    <li class="{{ request()->routeIs(\Illuminate\Support\Str::before($route, '.') . '.*') ? 'active' : '' }}">
                                        <a href="{{ route($route) }}">
                                            <span class="sub-item">{{ $label }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
