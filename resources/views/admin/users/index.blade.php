@extends('layouts.app')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')
@section('page-description', 'Manajemen semua user yang terdaftar')

@section('content')
    <!-- Filter & Search -->
    <div class="card mb-3 mb-md-4">
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <input type="text" class="form-control" name="search" placeholder="Cari user..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-6 col-md-3">
                        <select class="form-select" name="plan">
                            <option value="">Semua Paket</option>
                            <option value="free" {{ request('plan') === 'free' ? 'selected' : '' }}>Free</option>
                            <option value="basic" {{ request('plan') === 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 class="card-title">Daftar User</h3>
                <p class="card-description">Total: {{ $users->total() }} user</p>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            @if ($users->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Shop</th>
                                <th>Plan</th>
                                <th>Invoices</th>
                                <th>Expires</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <div class="d-none d-md-flex"
                                                style="width: 2rem; height: 2rem; background: hsl(var(--primary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.875rem;">{{ $user->name }}
                                                </div>
                                                <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">
                                                    {{ $user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.875rem;">{{ $user->shop_name ?? '-' }}</div>
                                        <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">
                                            {{ $user->shop_phone ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ getSubscriptionBadgeClass($user->subscription_plan) }}">
                                            {{ ucfirst($user->subscription_plan) }}
                                        </span>
                                        <div
                                            style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-top: 0.25rem;">
                                            {{ $user->invoice_count_this_month }}/{{ $user->invoice_limit }}
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-weight: 600;">{{ $user->invoices_count ?? 0 }}</span>
                                    </td>
                                    <td style="font-size: 0.8125rem;">
                                        @if ($user->subscription_ends_at)
                                            {{ $user->subscription_ends_at->format('d M Y') }}
                                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">
                                                {{ $user->subscription_ends_at->diffForHumans() }}
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline"
                                                title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST"
                                                style="display: inline;">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-success' }}"
                                                    title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                    onclick="return confirm('Yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} user ini?')">
                                                    <i
                                                        class="bi {{ $user->is_active ? 'bi-x-circle' : 'bi-check-circle' }}"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div style="padding: 1rem 1.5rem; border-top: 1px solid hsl(var(--border));">
                    {{ $users->links() }}
                </div>
            @else
                <div style="padding: 3rem 1.5rem; text-align: center;">
                    <i class="bi bi-people" style="font-size: 3rem; color: hsl(var(--muted-foreground)); opacity: 0.3;"></i>
                    <p style="margin-top: 1rem; color: hsl(var(--muted-foreground));">
                        @if (request()->hasAny(['search', 'plan', 'status']))
                            Tidak ada user yang sesuai dengan filter
                        @else
                            Belum ada user terdaftar
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@endpush
