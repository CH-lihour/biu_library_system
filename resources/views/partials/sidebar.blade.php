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
                $isBookModule = request()->routeIs('books.*')
                                || request()->routeIs('authors.*')
                                || request()->routeIs('publishers.*')
                                || request()->routeIs('categories.*')
                                || request()->routeIs('book-copies.*');

                $isMemberModule = request()->routeIs('members.*')
                                || request()->routeIs('member-plans.*');

                $isBookTransactionModule = request()->routeIs('borrows.*');
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
                @auth
                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'librarian')
                        <li class="nav-item {{ $isBookModule ? 'active submenu' : '' }}">
                            <a data-bs-toggle="collapse" href="#base" class="{{ $isBookModule ? '' : 'collapsed' }}" aria-expanded="{{ $isBookModule ? 'true' : 'false' }}">
                                <i class="icon-book-open"></i>
                                <p>Book</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse {{ $isBookModule ? 'show' : '' }}" id="base" data-bs-parent="#sidebarMenuAccordion">
                                <ul class="nav nav-collapse">
                                    <li class="{{ request()->routeIs('books.*') ? 'active' : '' }}">
                                        <a href="{{ route('books.index') }}">
                                            <span class="sub-item">All Books</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('book-copies.*') ? 'active' : '' }}">
                                        <a href="{{ route("book-copies.index") }}">
                                            <span class="sub-item">Book Copies</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('authors.*') ? 'active' : '' }}">
                                        <a href="{{ route("authors.index") }}">
                                            <span class="sub-item">Authors</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('publishers.*') ? 'active' : '' }}">
                                        <a href="{{ route("publishers.index") }}">
                                            <span class="sub-item">Publishers</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                                        <a href="{{ route("categories.index") }}">
                                            <span class="sub-item">Categories</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item {{ $isMemberModule ? 'active submenu' : '' }}">
                            <a data-bs-toggle="collapse" href="#members" class="{{ $isMemberModule ? '' : 'collapsed' }}" aria-expanded="{{ $isMemberModule ? 'true' : 'false' }}">
                                <i class="fas fa-th-list"></i>
                                <p>Members</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse {{ $isMemberModule ? 'show' : '' }}" id="members" data-bs-parent="#sidebarMenuAccordion">
                                <ul class="nav nav-collapse">
                                    <li class="{{ request()->routeIs('members.*') ? 'active' : '' }}">
                                        <a href="{{ route('members.index') }}">
                                            <span class="sub-item">Members</span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('member-plans.*') ? 'active' : '' }}">
                                        <a href="{{ route('member-plans.index') }}">
                                            <span class="sub-item">Member Plans</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a data-bs-toggle="collapse" href="#bookTransactions" class="{{ $isBookTransactionModule ? '' : 'collapsed' }}" aria-expanded="{{ $isBookTransactionModule ? 'true' : 'false' }}">
                                <i class="fas fa-arrow-circle-right"></i>
                                <p>Book Transactions</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse {{ $isBookTransactionModule ? 'show' : '' }}" id="bookTransactions" data-bs-parent="#sidebarMenuAccordion">
                                <ul class="nav nav-collapse">
                                    <li class="{{ request()->routeIs('borrows.*') ? 'active' : '' }}">
                                        <a href="{{ route('borrows.index') }}">
                                            <span class="sub-item">Borrow Books</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif
                @endauth
            </ul>
        </div>
    </div>
</div>
