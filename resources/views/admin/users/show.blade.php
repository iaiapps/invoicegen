@extends('layouts.app')

@section('title', 'Detail User')
@section('page-title', $user->name)
@section('page-description', $user->email)

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Subscription Status & Grace Period Alert -->
    @php
        $isGracePeriod = false;
        $graceDaysLeft = 0;
        if (in_array($user->subscription_plan, ['basic', 'pro']) && $user->subscription_ends_at) {
            if (now()->greaterThan($user->subscription_ends_at)) {
                $gracePeriodEnd = $user->subscription_ends_at->copy()->addDays(7);
                if (now()->lessThanOrEqualTo($gracePeriodEnd)) {
                    $isGracePeriod = true;
                    $graceDaysLeft = now()->diffInDays($gracePeriodEnd, false);
                }
            }
        }
    @endphp

    @if ($isGracePeriod)
        <div class="alert alert-warning mb-3">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Grace Period:</strong> User dalam masa tenggang {{ $graceDaysLeft }} hari lagi sebelum downgrade ke
            Free.
        </div>
    @endif

    <div class="row g-3">
        <!-- User Info & Stats -->
        <div class="col-12 col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">User Info</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td style="width: 40%;">Name</td>
                            <td><strong>{{ $user->name }}</strong></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td>Shop</td>
                            <td>{{ $user->shop_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>{{ $user->shop_phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Joined</td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>

                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-{{ $user->is_active ? 'danger' : 'success' }} w-100"
                            onclick="return confirm('Yakin?')">
                            {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} User
                        </button>
                    </form>
                </div>
            </div>

            <!-- Stats -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistics</h3>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: hsl(220 90% 56%);">
                                {{ $stats['total_invoices'] }}</div>
                            <div style="font-size: 0.75rem; color: hsl(215 16% 47%);">Invoices</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: hsl(142 76% 36%);">
                                {{ $stats['paid_invoices'] }}</div>
                            <div style="font-size: 0.75rem; color: hsl(215 16% 47%);">Paid</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: hsl(261 51% 51%);">
                                {{ $stats['total_customers'] }}</div>
                            <div style="font-size: 0.75rem; color: hsl(215 16% 47%);">Customers</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: hsl(38 92% 50%);">
                                {{ $stats['total_products'] }}</div>
                            <div style="font-size: 0.75rem; color: hsl(215 16% 47%);">Products</div>
                        </div>
                    </div>
                    <div class="divider"></div>
                    <div style="text-align: center;">
                        <div style="font-size: 0.75rem; color: hsl(215 16% 47%); margin-bottom: 0.25rem;">Total Revenue
                        </div>
                        <div style="font-size: 1.25rem; font-weight: 700;">Rp
                            {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Management -->
        <div class="col-12 col-md-8">
            <!-- Current Subscription -->
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Current Subscription</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <div>
                            <span
                                class="badge badge-{{ $user->subscription_plan === 'pro' ? 'primary' : ($user->subscription_plan === 'basic' ? 'info' : 'secondary') }}"
                                style="font-size: 1.5rem; padding: 0.5rem 1rem;">
                                {{ ucfirst($user->subscription_plan) }}
                            </span>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.875rem; color: hsl(215 16% 47%);">Invoice Limit</div>
                            <div style="font-size: 1.5rem; font-weight: 700;">{{ $user->invoice_limit }}</div>
                        </div>
                    </div>
                    @if ($user->subscription_ends_at)
                        <div style="padding: 0.75rem; background: hsl(215 20% 95%); border-radius: 0.5rem;">
                            <div style="font-size: 0.875rem; color: hsl(215 16% 47%);">Expires At</div>
                            <div style="font-weight: 600;">{{ $user->subscription_ends_at->format('d M Y') }}</div>
                            <div style="font-size: 0.875rem; color: hsl(215 16% 47%);">
                                ({{ now()->diffInDays($user->subscription_ends_at, false) }} hari lagi)
                            </div>
                        </div>
                    @else
                        <div
                            style="padding: 0.75rem; background: hsl(215 20% 95%); border-radius: 0.5rem; text-align: center;">
                            <div style="color: hsl(215 16% 47%);">Free plan - No expiry</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Manual Actions -->
            <div class="row g-3 mb-3">
                <!-- Change Plan -->
                <div class="col-12 col-lg-6">
                    <div class="card" style="border: 2px solid hsl(220 90% 56%);">
                        <div class="card-header" style="background: hsl(220 90% 96%);">
                            <h5 style="margin: 0; color: hsl(220 90% 56%);">
                                <i class="bi bi-arrow-up-circle"></i> Change Plan
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.users.update-subscription', $user) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Plan <span class="text-danger">*</span></label>
                                    <select class="form-control" name="plan" required onchange="toggleDuration(this)">
                                        <option value="free" {{ $user->subscription_plan === 'free' ? 'selected' : '' }}>
                                            Free (30 invoice)</option>
                                        <option value="basic"
                                            {{ $user->subscription_plan === 'basic' ? 'selected' : '' }}>Basic (60 invoice)
                                        </option>
                                        <option value="pro" {{ $user->subscription_plan === 'pro' ? 'selected' : '' }}>
                                            Pro (120 invoice)</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="duration_field">
                                    <label class="form-label">Duration (Months)</label>
                                    <input type="number" class="form-control" name="duration_months" min="1"
                                        max="12" value="1">
                                    <small class="form-text">Kosongkan untuk Free plan</small>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle"></i> Update Plan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Extend Subscription -->
                <div class="col-12 col-lg-6">
                    <div class="card" style="border: 2px solid hsl(142 76% 36%);">
                        <div class="card-header" style="background: hsl(142 76% 96%);">
                            <h5 style="margin: 0; color: hsl(142 76% 36%);">
                                <i class="bi bi-calendar-plus"></i> Extend Subscription
                            </h5>
                        </div>
                        <div class="card-body">
                            @if (in_array($user->subscription_plan, ['basic', 'pro']))
                                <form action="{{ route('admin.users.extend-subscription', $user) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Add Months <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="extend_months" min="1"
                                            max="12" value="1" required>
                                        <small class="form-text">Perpanjang subscription existing</small>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-plus-circle"></i> Extend
                                    </button>
                                </form>
                            @else
                                <div class="p-3 bg-warning rounded opacity-50" style="margin: 0; text-align: center;">
                                    <small>Hanya untuk Basic/Pro plan</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Invoices -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Invoices</h3>
                </div>
                <div class="card-body">
                    @if ($recentInvoices->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>No Invoice</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentInvoices as $invoice)
                                        <tr>
                                            <td><code>{{ $invoice->invoice_number }}</code></td>
                                            <td>{{ $invoice->customer->name }}</td>
                                            <td><strong>Rp {{ number_format($invoice->total, 0, ',', '.') }}</strong></td>
                                            <td>
                                                @if ($invoice->status === 'paid')
                                                    <span class="badge badge-success">Paid</span>
                                                @elseif ($invoice->status === 'cancelled')
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @else
                                                    <span class="badge badge-warning">Unpaid</span>
                                                @endif
                                            </td>
                                            <td>{{ $invoice->invoice_date->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="text-align: center; padding: 2rem; color: hsl(215 16% 47%);">
                            Belum ada invoice
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleDuration(select) {
            const durationField = document.getElementById('duration_field');
            const durationInput = document.querySelector('[name="duration_months"]');

            if (select.value === 'free') {
                durationInput.value = '';
                durationInput.required = false;
                durationField.style.display = 'none';
            } else {
                durationInput.required = true;
                durationField.style.display = 'block';
                if (!durationInput.value) {
                    durationInput.value = 1;
                }
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const planSelect = document.querySelector('[name="plan"]');
            if (planSelect) {
                toggleDuration(planSelect);
            }
        });
    </script>
@endpush
