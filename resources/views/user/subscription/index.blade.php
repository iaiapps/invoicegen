@extends('layouts.app')

@section('title', 'Langganan')
@section('page-title', 'Kelola Langganan')
@section('page-description', 'Upgrade paket untuk meningkatkan limit invoice')

@section('content')
    <!-- Alert System -->
    @php
        $showAlert = false;
        $alertType = '';
        $alertMessage = '';

        if (!$currentPlan['is_free'] && $currentPlan['expires_at']) {
            $daysUntilExpiry = now()->diffInDays($currentPlan['expires_at'], false);
            $isExpired = now()->greaterThan($currentPlan['expires_at']);

            if ($isExpired) {
                // Grace period alert
                $gracePeriodDays = getSetting('grace_period_days', 7);
                $gracePeriodEnd = $currentPlan['expires_at']->copy()->addDays($gracePeriodDays);
                $daysLeftGrace = now()->diffInDays($gracePeriodEnd, false);

                if ($daysLeftGrace > 0) {
                    $showAlert = true;
                    $alertType = 'warning';
                    $alertMessage =
                        "⚠️ Subscription Anda telah berakhir! Anda masih memiliki <strong>{$daysLeftGrace} hari masa tenggang</strong> sebelum downgrade ke paket Free (" .
                        getSetting('free_invoice_limit', 20) .
                        ' invoice/bulan).';
                }
            } elseif ($daysUntilExpiry <= 3) {
                // 3 days before expiry - danger
                $showAlert = true;
                $alertType = 'danger';
                $alertMessage = "🚨 Subscription Anda akan berakhir dalam <strong>{$daysUntilExpiry} hari</strong>! Perpanjang sekarang untuk menghindari downgrade.";
            } elseif ($daysUntilExpiry <= 7) {
                // 7 days before expiry - warning
                $showAlert = true;
                $alertType = 'warning';
                $alertMessage = "⏰ Subscription Anda akan berakhir dalam <strong>{$daysUntilExpiry} hari</strong>. Jangan lupa untuk perpanjang.";
            }
        }

        // Check if over FREE tier limit during grace period
        if (!$currentPlan['is_free'] && $currentPlan['expires_at'] && now()->greaterThan($currentPlan['expires_at'])) {
            $invoiceCountThisMonth = Auth::user()->invoice_count_this_month;
            $freeLimit = getSetting('free_invoice_limit', 20);
            if ($invoiceCountThisMonth >= $freeLimit) {
                $showAlert = true;
                $alertType = 'danger';
                $alertMessage = "❌ Anda telah membuat <strong>{$invoiceCountThisMonth} invoice</strong> bulan ini. Limit Free adalah {$freeLimit} invoice. Upgrade untuk membuat invoice lebih banyak!";
            }
        }
    @endphp

    @if ($showAlert)
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="alert alert-{{ $alertType }}" style="margin: 0;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="flex: 1;">
                            {!! $alertMessage !!}
                        </div>
                        <a href="#plans" class="btn btn-sm btn-{{ $alertType === 'danger' ? 'danger' : 'warning' }}">
                            <i class="bi bi-arrow-up-circle"></i> Upgrade Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Current Plan -->
    <div class="row g-3 g-md-4 mb-4" id="plans">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div
                        style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h5 style="margin: 0 0 0.5rem 0;">Paket Saat Ini</h5>
                            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                <span style="font-size: 2rem; font-weight: 700; color: hsl(220 90% 56%);">
                                    {{ $currentPlan['name'] }}
                                </span>
                                @if ($currentPlan['is_active'])
                                    <span class="badge badge-success py-1">Aktif</span>
                                @else
                                    <span class="badge badge-danger py-1">Tidak Aktif</span>
                                @endif
                            </div>
                            <p style="margin: 0.5rem 0 0 0; color: hsl(215 16% 47%);">
                                <strong>{{ $currentPlan['invoice_limit'] }} invoice/bulan</strong> •
                                <strong>{{ getProductLimit(Auth::user()->subscription_plan) }} produk</strong>
                                @if (!$currentPlan['is_free'] && $currentPlan['expires_at'])
                                    <br>Berakhir: <strong>{{ $currentPlan['expires_at']->format('d M Y') }}</strong>
                                    {{-- @if ($daysRemaining !== null)
                                        ({{ $daysRemaining }} hari lagi)
                                    @endif --}}
                                @endif
                            </p>
                        </div>

                        <div>
                            <p style="margin: 0 0 0.5rem 0; font-size: 0.875rem; color: hsl(215 16% 47%);">
                                Invoice bulan ini
                            </p>
                            <div style="font-size: 1.5rem; font-weight: 600;">
                                {{ Auth::user()->invoice_count_this_month }} / {{ $currentPlan['invoice_limit'] }}
                                <span style="font-size: 0.875rem; color: hsl(215 16% 47%);">(Sisa:
                                    {{ getRemainingInvoices() }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Plans - Compact -->
    <div class="row g-3 mb-4">
        @foreach ($plans as $key => $plan)
            <div class="col-12 col-md-4">
                <div class="card" style="{{ $plan['is_current'] ? 'border: 2px solid hsl(220 90% 56%);' : '' }}">
                    <div class="card-body">
                        <div
                            style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem;">
                            <h5 style="margin: 0;">{{ $plan['name'] }}</h5>
                            @if (isset($plan['popular']))
                                <span class="badge badge-primary py-1">Popular</span>
                            @elseif ($plan['is_current'])
                                <span class="badge badge-success py-1">Aktif</span>
                            @endif
                        </div>

                        <div style="margin-bottom: 1rem;">
                            <div style="font-size: 1.75rem; font-weight: 700; color: hsl(220 90% 56%);">
                                {{ $plan['price_display'] }}
                            </div>
                            <div style="color: hsl(215 16% 47%); font-size: 0.875rem;">
                                {{ $plan['invoice_limit'] }} invoice/bulan<br>
                                {{ getProductLimit($key) }} produk
                            </div>
                        </div>

                        @if ($plan['is_current'])
                            <button class="btn btn-outline w-100" disabled>
                                <i class="me-2 bi bi-check-circle"></i> Paket Aktif
                            </button>
                        @elseif ($key === 'free')
                            <button class="btn btn-outline w-100" disabled>
                                Paket Gratis
                            </button>
                        @else
                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal"
                                data-bs-target="#upgradeModal" data-plan="{{ $key }}"
                                data-name="{{ $plan['name'] }}" data-price="{{ $plan['price'] }}"
                                data-limit="{{ $plan['invoice_limit'] }}"
                                data-price-display="{{ $plan['price_display'] }}">
                                <i class="me-2 bi bi-arrow-up-circle"></i> Upgrade
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Subscription History -->
    @if ($subscriptionHistory->count() > 0)
        <div class="row g-3 g-md-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Riwayat Subscription</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Paket</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Berlaku S/D</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subscriptionHistory as $sub)
                                        <tr>
                                            <td>{{ $sub->created_at->format('d M Y') }}</td>
                                            <td><strong>{{ ucfirst($sub->plan) }}</strong></td>
                                            <td>Rp {{ number_format($sub->amount, 0, ',', '.') }}</td>
                                            <td>
                                                {!! getSubscriptionStatusBadge($sub->payment_status) !!}
                                            </td>
                                            <td>
                                                @if ($sub->ends_at)
                                                    {{ $sub->ends_at->format('d M Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Upgrade Modal -->
    <div class="modal fade" id="upgradeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upgrade Subscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Plan Info -->
                    <div class="card mb-3" style="background: hsl(220 90% 96%);">
                        <div class="card-body">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h6 style="margin: 0; color: hsl(215 16% 47%);">Paket yang dipilih:</h6>
                                    <h4 style="margin: 0.25rem 0; color: hsl(220 90% 56%);" id="modalPlanName">-</h4>
                                    <p style="margin: 0; font-size: 0.875rem;">
                                        <span id="modalPlanLimit">0</span> invoice/bulan
                                    </p>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 0.875rem; color: hsl(215 16% 47%);">Total Bayar</div>
                                    <div style="font-size: 1.75rem; font-weight: 700; color: hsl(220 90% 56%);"
                                        id="modalPrice">
                                        Rp 0
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Tabs -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#transferTab"
                                type="button">
                                <i class="bi bi-bank"></i> Transfer Bank
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#qrisTab" type="button">
                                <i class="bi bi-qr-code"></i> QRIS
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Transfer Bank Tab -->
                        <div class="tab-pane fade show active" id="transferTab">
                            @foreach ($bankAccounts as $bank)
                                <div class="card mb-2">
                                    <div class="card-body" style="padding: 0.75rem;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <div style="font-weight: 600;">{{ $bank['bank'] }}</div>
                                                <div
                                                    style="font-size: 1.125rem; font-weight: 600; color: hsl(220 90% 56%);">
                                                    {{ $bank['account_number'] }}
                                                </div>
                                                <div style="font-size: 0.875rem; color: hsl(215 16% 47%);">
                                                    {{ $bank['account_name'] }}
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline"
                                                onclick="copyToClipboard('{{ $bank['account_number'] }}')">
                                                <i class="bi bi-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- QRIS Tab -->
                        <div class="tab-pane fade" id="qrisTab">
                            <div class="card">
                                <div class="card-body" style="text-align: center; padding: 2rem;">
                                    <div
                                        style="background: hsl(215 20% 95%); padding: 2rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                                        <i class="bi bi-qr-code" style="font-size: 4rem; color: hsl(215 16% 47%);"></i>
                                        <p style="margin: 1rem 0 0 0; color: hsl(215 16% 47%);">
                                            QR Code akan tersedia segera
                                        </p>
                                    </div>
                                    <small class="text-muted">Scan QRIS untuk pembayaran otomatis</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmation Form -->
                    <form action="{{ route('subscription.upgrade') }}" method="POST" id="upgradeForm">
                        @csrf
                        <input type="hidden" name="plan" id="selectedPlan">

                        <div class="mb-3 mt-3">
                            <label class="form-label">Catatan Pembayaran (Opsional)</label>
                            <textarea class="form-control" name="payment_notes" rows="2"
                                placeholder="Contoh: Transfer dari Bank BCA a.n. John Doe"></textarea>
                        </div>

                        <div class="alert alert-info" style="font-size: 0.875rem;">
                            <i class="bi bi-info-circle"></i>
                            Setelah konfirmasi, Anda akan diarahkan ke WhatsApp untuk konfirmasi pembayaran.
                            Subscription akan aktif dalam 1x24 jam setelah verifikasi.
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="me-2 bi bi-whatsapp"></i> Konfirmasi & Hubungi Admin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Handle upgrade modal
        const upgradeModal = document.getElementById('upgradeModal');
        if (upgradeModal) {
            upgradeModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const plan = button.getAttribute('data-plan');
                const name = button.getAttribute('data-name');
                const price = button.getAttribute('data-price');
                const limit = button.getAttribute('data-limit');
                const priceDisplay = button.getAttribute('data-price-display');

                document.getElementById('selectedPlan').value = plan;
                document.getElementById('modalPlanName').textContent = name;
                document.getElementById('modalPlanLimit').textContent = limit;
                document.getElementById('modalPrice').textContent = priceDisplay;
            });
        }

        // Handle form submission with WhatsApp redirect
        const upgradeForm = document.getElementById('upgradeForm');
        if (upgradeForm) {
            upgradeForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const plan = document.getElementById('selectedPlan').value;
                const planName = document.getElementById('modalPlanName').textContent;
                const price = document.getElementById('modalPrice').textContent;
                const notes = document.querySelector('[name="payment_notes"]').value;

                // WhatsApp message
                const userName = '{{ Auth::user()->name }}';
                const userEmail = '{{ Auth::user()->email }}';
                const whatsappNumber = '{{ getSetting('whatsapp_admin', '6281234567890') }}';

                let message = `Halo Admin,%0A%0A`;
                message += `Saya ${userName} (${userEmail}) ingin upgrade subscription:%0A%0A`;
                message += `📦 Paket: *${planName}*%0A`;
                message += `💰 Total: *${price}*%0A`;
                if (notes) {
                    message += `📝 Catatan: ${notes}%0A`;
                }
                message += `%0AMohon verifikasi pembayaran saya.%0ATerima kasih!`;

                const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${message}`;

                // Submit form first
                fetch(this.action, {
                        method: 'POST',
                        body: new FormData(this)
                    })
                    .then(response => {
                        if (response.redirected) {
                            // Open WhatsApp in new tab
                            window.open(whatsappUrl, '_blank');
                            // Redirect to subscription page
                            window.location.href = response.url;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Fallback: submit form normally
                        this.submit();
                    });
            });
        }

        // Copy to clipboard
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Nomor rekening berhasil disalin!');
            }, function(err) {
                console.error('Gagal menyalin:', err);
            });
        }
    </script>
@endpush
