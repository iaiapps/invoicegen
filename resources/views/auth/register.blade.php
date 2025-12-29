@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <div class="auth-header">
        <div style="display: flex; justify-content: center; margin-bottom: 1rem;">
            <div
                style="width: 4rem; height: 4rem; background: hsl(var(--primary)); border-radius: 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                <i class="bi bi-receipt-cutoff"></i>
            </div>
        </div>
        <h1 class="auth-title">Buat Akun Baru</h1>
        <p class="auth-description">Daftar gratis dan mulai buat invoice profesional</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">
                        <i class="bi bi-person"></i> Nama Lengkap
                    </label>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                        name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                        placeholder="John Doe">
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope"></i> Email
                    </label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email"
                        placeholder="nama@example.com">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Shop Name -->
                <div class="mb-3">
                    <label for="shop_name" class="form-label">
                        <i class="bi bi-shop"></i> Nama Toko/Bisnis
                    </label>
                    <input id="shop_name" type="text" class="form-control @error('shop_name') is-invalid @enderror"
                        name="shop_name" value="{{ old('shop_name') }}" required placeholder="Toko Sejahtera">
                    <small class="form-text">Nama ini akan muncul di invoice Anda</small>
                    @error('shop_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Shop Phone -->
                <div class="mb-3">
                    <label for="shop_phone" class="form-label">
                        <i class="bi bi-telephone"></i> Nomor WhatsApp
                    </label>
                    <input id="shop_phone" type="text" class="form-control @error('shop_phone') is-invalid @enderror"
                        name="shop_phone" value="{{ old('shop_phone') }}" required placeholder="081234567890">
                    <small class="form-text">Format: 08xxxxxxxxxx (tanpa spasi atau tanda hubung)</small>
                    @error('shop_phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock"></i> Password
                    </label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="new-password" placeholder="••••••••">
                    <small class="form-text">Minimal 8 karakter</small>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="password-confirm" class="form-label">
                        <i class="bi bi-lock-fill"></i> Konfirmasi Password
                    </label>
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
                        autocomplete="new-password" placeholder="••••••••">
                </div>

                <!-- Terms -->
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms"
                            id="terms" required>
                        <label class="form-check-label" for="terms" style="font-size: 0.875rem;">
                            Saya menyetujui <a href="#" style="color: hsl(var(--primary));">Syarat & Ketentuan</a>
                        </label>
                        @error('terms')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 mb-3" style="justify-content: center;">
                    <i class="bi bi-person-plus"></i>
                    <span style="margin-left: 0.5rem;">Daftar Sekarang</span>
                </button>

                <!-- Info Box -->
                <div
                    style="background: hsl(var(--muted)); border: 1px solid hsl(var(--border)); border-radius: var(--radius); padding: 1rem; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                        <i class="bi bi-gift" style="font-size: 1.25rem; color: hsl(var(--primary));"></i>
                        <div style="font-size: 0.875rem;">
                            <strong style="display: block; margin-bottom: 0.25rem;">Gratis Selamanya!</strong>
                            <span style="color: hsl(var(--muted-foreground));">
                                Paket Free: 30 invoice/bulan. Tanpa kartu kredit. Upgrade kapan saja.
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Divider -->
                <div style="position: relative; text-align: center; margin: 1.5rem 0;">
                    <span
                        style="background: white; padding: 0 1rem; color: hsl(var(--muted-foreground)); font-size: 0.875rem; position: relative; z-index: 1;">
                        Atau
                    </span>
                    <div
                        style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: hsl(var(--border));">
                    </div>
                </div>

                <!-- Login Link -->
                <div style="text-align: center;">
                    <span style="font-size: 0.875rem; color: hsl(var(--muted-foreground));">
                        Sudah punya akun?
                    </span>
                    <a href="{{ route('login') }}"
                        style="font-size: 0.875rem; color: hsl(var(--primary)); text-decoration: none; font-weight: 500; margin-left: 0.25rem;">
                        Masuk
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div style="text-align: center; margin-top: 2rem; color: hsl(var(--muted-foreground)); font-size: 0.8125rem;">
        <p>© {{ date('Y') }} InvoiceGen. All rights reserved.</p>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto-format phone number
        document.getElementById('shop_phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('62')) {
                value = '0' + value.substring(2);
            }
            e.target.value = value;
        });
    </script>
@endpush
