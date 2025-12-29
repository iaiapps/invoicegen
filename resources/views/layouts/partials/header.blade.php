<header class="main-header">
    <div class="main-header-left">
        <!-- Hamburger Button -->
        <button class="hamburger-btn" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
            <i class="bi bi-list"></i>
        </button>

        <div class="d-none d-md-block">
            <h1 style="font-size: 1.5rem; font-weight: 600; margin: 0;">
                @yield('page-title', 'Dashboard')
            </h1>
            <p style="font-size: 0.875rem; color: hsl(var(--muted-foreground)); margin: 0;">
                @yield('page-description', 'Selamat datang kembali!')
            </p>
        </div>
    </div>

    <div class="main-header-right">
        <!-- Subscription Badge (Hidden on mobile & admin) -->
        @if (!Auth::user()->hasRole('admin'))
            <div class="badge badge-primary d-md-flex p-2 px-3">
                <a href="{{ route('subscription.index') }}" class="text-white link-underline link-underline-opacity-0">
                    <i class="bi bi-star-fill" style="margin-right: 0.25rem;"></i>
                    {{ ucfirst(Auth::user()->subscription_plan) }}
                </a>
            </div>
        @endif

        <!-- Notification Icon -->
        {{-- <button class="btn btn-ghost" style="padding: 0.5rem; position: relative;" title="Notifications">
            <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
            <span
                style="position: absolute; top: 0.25rem; right: 0.25rem; width: 0.5rem; height: 0.5rem; background: hsl(var(--destructive)); border-radius: 50%;"></span>
        </button> --}}

        <!-- User Profile (Mobile) -->
        <div class="dropdown d-md-none">
            <button class="btn btn-ghost" style="padding: 0.5rem;" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                <div
                    style="width: 2rem; height: 2rem; background: hsl(var(--primary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <div class="dropdown-item-text">
                        <div style="font-weight: 600;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">
                            {{ Auth::user()->email }}</div>
                    </div>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-person"></i> Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Page Title (Mobile Only) -->
<div class="d-md-none"
    style="padding: 1rem; background: hsl(var(--card)); border-bottom: 1px solid hsl(var(--border));">
    <h1 style="font-size: 1.25rem; font-weight: 600; margin: 0;">
        @yield('page-title', 'Dashboard')
    </h1>
    <p style="font-size: 0.875rem; color: hsl(var(--muted-foreground)); margin: 0;">
        @yield('page-description', 'Selamat datang kembali!')
    </p>
</div>
