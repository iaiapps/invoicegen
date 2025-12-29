<div class="sidebar-footer">
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
            <div class="sidebar-user-email">{{ Auth::user()->email }}</div>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline w-100" style="justify-content: center;">
            <i class="bi bi-box-arrow-right"></i>
            <span class="sidebar-menu-item-text" style="margin-left: 0.5rem;">Logout</span>
        </button>
    </form>
</div>
