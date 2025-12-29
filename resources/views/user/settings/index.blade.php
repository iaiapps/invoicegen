@extends('layouts.app')

@section('title', 'Pengaturan Toko')
@section('page-title', 'Pengaturan Toko')
@section('page-description', 'Kelola informasi toko dan akun Anda')

@section('content')
    <div class="row g-3 g-md-4">
        <!-- Informasi Toko -->
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Toko</h3>
                    <p class="card-description">Data toko akan muncul di invoice Anda</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Nama Pemilik -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nama Pemilik <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Nama Toko -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="shop_name" class="form-label">Nama Toko</label>
                                    <input type="text" class="form-control @error('shop_name') is-invalid @enderror"
                                        id="shop_name" name="shop_name" value="{{ old('shop_name', $user->shop_name) }}"
                                        placeholder="Contoh: Toko Sejahtera">
                                    @error('shop_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- No. Telepon -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="shop_phone" class="form-label">No. Telepon / WhatsApp</label>
                                    <input type="text" class="form-control @error('shop_phone') is-invalid @enderror"
                                        id="shop_phone" name="shop_phone" value="{{ old('shop_phone', $user->shop_phone) }}"
                                        placeholder="081234567890">
                                    @error('shop_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Alamat Toko -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="shop_address" class="form-label">Alamat Toko</label>
                                    <textarea class="form-control @error('shop_address') is-invalid @enderror" id="shop_address" name="shop_address"
                                        rows="3" placeholder="Jl. Merdeka No. 123, Jakarta">{{ old('shop_address', $user->shop_address) }}</textarea>
                                    @error('shop_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="mt-4">

                        <!-- Ganti Password -->
                        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Ganti Password</h4>
                        <p style="font-size: 0.875rem; color: hsl(var(--muted-foreground)); margin-bottom: 1rem;">
                            Kosongkan jika tidak ingin mengubah password
                        </p>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" placeholder="Minimal 8 karakter">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" placeholder="Ketik ulang password baru">
                                </div>
                            </div>
                        </div>

                        <hr class="mt-4">

                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                            <button type="reset" class="btn btn-outline">
                                <i class="me-2 bi bi-x-circle"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="me-2 bi bi-check-circle"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Logo & Info -->
        <div class="col-12 col-lg-4">
            <!-- Logo Toko -->
            <div class="card mb-3 mb-md-4">
                <div class="card-header">
                    <h3 class="card-title">Logo Toko</h3>
                    <p class="card-description">Upload logo Anda</p>
                </div>
                <div class="card-body">
                    <div style="text-align: center; margin-bottom: 1.5rem;">
                        @if ($user->shop_logo)
                            <img src="{{ asset('storage/' . $user->shop_logo) }}" alt="Logo"
                                style="max-width: 150px; max-height: 150px; border-radius: var(--radius); border: 2px solid hsl(var(--border));">
                        @else
                            <div
                                style="width: 150px; height: 150px; margin: 0 auto; background: hsl(var(--muted)); border-radius: var(--radius); display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image" style="font-size: 3rem; color: hsl(var(--muted-foreground));"></i>
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('settings.upload-logo') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="logo" class="form-label">Upload Logo</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo"
                                name="logo" accept="image/jpeg,image/png,image/jpg">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Format: JPG, PNG. Max: 2MB</div>
                        </div>

                        <button type="submit" class="btn btn-primary mt-4" style="width: 100%;">
                            <i class="me-2 bi bi-upload"></i> Upload Logo
                        </button>
                    </form>
                </div>
            </div>

            <!-- Info Subscription -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Subscription</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Paket Saat Ini
                            </div>
                            <span class="badge {{ getSubscriptionBadgeClass($user->subscription_plan) }}"
                                style="font-size: 0.875rem;">
                                {{ ucfirst($user->subscription_plan) }}
                            </span>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Limit Invoice
                            </div>
                            <div style="font-weight: 600;">{{ $user->invoice_limit }} invoice/bulan</div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Berakhir Pada
                            </div>
                            <div style="font-weight: 600;">
                                {{ $user->subscription_ends_at ? $user->subscription_ends_at->format('d M Y') : '-' }}
                            </div>
                        </div>

                        <div class="divider"></div>

                        <a href="{{ route('subscription.index') }}" class="btn btn-outline" style="width: 100%;">
                            <i class="me-2 bi bi-star"></i> Kelola Subscription
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
@endpush
