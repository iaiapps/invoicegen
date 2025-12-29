@extends('layouts.app')

@section('title', 'Detail Subscription')
@section('page-title', 'Detail Subscription #' . $subscription->id)
@section('page-description', ucfirst($subscription->plan) . ' - ' . $subscription->user->name)

@section('content')
    <div class="mb-3 mb-md-4">
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Status Badge -->
    <div class="mb-3">
        {!! getSubscriptionStatusBadge($subscription->payment_status) !!}
    </div>

    <!-- Approve/Reject Actions (for pending only) -->
    @if ($subscription->payment_status === 'pending')
        <div class="row g-3 mb-4">
            <!-- Approve Form -->
            <div class="col-12 col-md-6">
                <div class="card" style="border: 2px solid hsl(142 76% 36%);">
                    <div class="card-header" style="background: hsl(142 76% 96%);">
                        <h5 style="margin: 0; color: hsl(142 76% 36%);">
                            <i class="bi bi-check-circle"></i> Approve Subscription
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.subscriptions.approve', $subscription) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Durasi (Bulan) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="duration_months" min="1" max="12" value="1" required>
                                <small class="form-text">Berapa bulan subscription ini akan aktif</small>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Approve & Aktifkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Reject Form -->
            <div class="col-12 col-md-6">
                <div class="card" style="border: 2px solid hsl(0 84% 60%);">
                    <div class="card-header" style="background: hsl(0 84% 96%);">
                        <h5 style="margin: 0; color: hsl(0 84% 60%);">
                            <i class="bi bi-x-circle"></i> Reject Subscription
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.subscriptions.reject', $subscription) }}" method="POST" onsubmit="return confirm('Yakin tolak subscription ini?')">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Alasan Penolakan</label>
                                <textarea class="form-control" name="reject_reason" rows="2" placeholder="Opsional"></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-3">
        <!-- Subscription Details -->
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Subscription</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <td style="width: 40%;">Plan</td>
                            <td><strong class="badge badge-{{ $subscription->plan === 'pro' ? 'primary' : 'info' }}">{{ ucfirst($subscription->plan) }}</strong></td>
                        </tr>
                        <tr>
                            <td>Amount</td>
                            <td><strong>Rp {{ number_format($subscription->amount, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td>Invoice Limit</td>
                            <td>{{ $subscription->invoice_limit }} /bulan</td>
                        </tr>
                        <tr>
                            <td>Payment Method</td>
                            <td>{{ ucfirst($subscription->payment_method ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td>Payment Reference</td>
                            <td><code>{{ $subscription->payment_reference ?? '-' }}</code></td>
                        </tr>
                        <tr>
                            <td>Starts At</td>
                            <td>{{ $subscription->starts_at ? $subscription->starts_at->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Ends At</td>
                            <td>{{ $subscription->ends_at ? $subscription->ends_at->format('d M Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Created At</td>
                            <td>{{ $subscription->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- User Info -->
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Information</h3>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <td style="width: 40%;">Name</td>
                            <td><strong>{{ $subscription->user->name }}</strong></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>{{ $subscription->user->email }}</td>
                        </tr>
                        <tr>
                            <td>Shop Name</td>
                            <td>{{ $subscription->user->shop_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Phone</td>
                            <td>{{ $subscription->user->shop_phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Current Plan</td>
                            <td><span class="badge badge-secondary">{{ ucfirst($subscription->user->subscription_plan) }}</span></td>
                        </tr>
                    </table>
                    <a href="{{ route('admin.users.show', $subscription->user) }}" class="btn btn-outline w-100 mt-2">
                        <i class="bi bi-person"></i> Detail User
                    </a>
                </div>
            </div>

            <!-- Payment Notes -->
            @if ($subscription->payment_response)
                <div class="card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Catatan Pembayaran</h3>
                    </div>
                    <div class="card-body">
                        <p style="margin: 0; white-space: pre-wrap;">{{ $subscription->payment_response }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
