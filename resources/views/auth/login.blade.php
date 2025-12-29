@extends('layouts.guest')

@section('title', 'Login')

@section('content')
    <div class="auth-header">
        <div style="display: flex; justify-content: center; margin-bottom: 1rem;">
            <div
                style="width: 4rem; height: 4rem; background: hsl(var(--primary)); border-radius: 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                <i class="bi bi-receipt-cutoff"></i>
            </div>
        </div>
        <h1 class="auth-title">Selamat Datang Kembali</h1>
        <p class="auth-description">Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger" role="alert"
                    style="border-radius: var(--radius); border: 1px solid hsl(var(--destructive) / 0.2); background: hsl(var(--destructive) / 0.1); color: hsl(var(--destructive)); margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: start; gap: 0.75rem;">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 0.25rem;">Login gagal!</strong>
                            @foreach ($errors->all() as $error)
                                <div style="font-size: 0.875rem;">{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope"></i> Email
                    </label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="nama@example.com">
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label for="password" class="form-label" style="margin: 0;">
                            <i class="bi bi-lock"></i> Password
                        </label>
                        {{-- @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                style="font-size: 0.875rem; color: hsl(var(--primary)); text-decoration: none;">
                                Lupa password?
                            </a>
                        @endif --}}
                    </div>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password" placeholder="••••••••">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember" style="font-size: 0.875rem;">
                            Ingat saya
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 mb-3" style="justify-content: center;">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span style="margin-left: 0.5rem;">Masuk</span>
                </button>

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

                <!-- Register Link -->
                <div style="text-align: center;">
                    <span style="font-size: 0.875rem; color: hsl(var(--muted-foreground));">
                        Belum punya akun?
                    </span>
                    <a href="{{ route('register') }}"
                        style="font-size: 0.875rem; color: hsl(var(--primary)); text-decoration: none; font-weight: 500; margin-left: 0.25rem;">
                        Daftar Gratis
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
