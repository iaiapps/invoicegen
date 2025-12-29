@extends('layouts.app')

@section('title', 'Pengaturan System')
@section('page-title', 'Pengaturan System')
@section('page-description', 'Kelola pengaturan platform dan akun admin')

@section('content')
    <div class="row g-3 g-md-4">
        <!-- Admin Account Settings -->
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Akun Admin</h3>
                    <p class="card-description">Kelola informasi akun admin platform</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Nama -->
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nama Admin <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name', $admin->name) }}" required>
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
                                        id="email" name="email" value="{{ old('email', $admin->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>


                        <!-- Ganti Password -->
                        <h4 class="mt-3 mb-1" style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Ganti Password
                        </h4>
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

                        <div class="mt-3" style="display: flex; justify-content: flex-end; gap: 0.75rem;">
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

            <!-- Platform Configuration -->
            <div class="card mt-3 mt-md-4">
                <div class="card-header">
                    <h3 class="card-title">Konfigurasi Platform</h3>
                    <p class="card-description">Pengaturan platform InvoiceGen</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.platform.update') }}" method="POST">
                        @csrf

                        <!-- Free Plan -->
                        <h5 style="font-size: 0.9375rem; font-weight: 600; margin-bottom: 1rem;">Paket Free</h5>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Limit Invoice <span class="text-danger">*</span></label>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="number"
                                        class="form-control @error('free_invoice_limit') is-invalid @enderror"
                                        name="free_invoice_limit"
                                        value="{{ old('free_invoice_limit', $settings['free_invoice_limit']) }}"
                                        min="1" max="999" required style="max-width: 100px;">
                                    <span>/bulan</span>
                                </div>
                                @error('free_invoice_limit')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Limit Produk <span class="text-danger">*</span></label>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="number"
                                        class="form-control @error('free_product_limit') is-invalid @enderror"
                                        name="free_product_limit"
                                        value="{{ old('free_product_limit', $settings['free_product_limit']) }}"
                                        min="1" max="9999" required style="max-width: 100px;">
                                    <span>produk</span>
                                </div>
                                @error('free_product_limit')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <hr>
                        <h5 style="font-size: 0.9375rem; font-weight: 600; margin-bottom: 1rem;">Paket Basic</h5>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('basic_price') is-invalid @enderror"
                                        name="basic_price" value="{{ old('basic_price', $settings['basic_price']) }}"
                                        min="0" required>
                                    @error('basic_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Limit Invoice <span class="text-danger">*</span></label>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="number"
                                        class="form-control @error('basic_invoice_limit') is-invalid @enderror"
                                        name="basic_invoice_limit"
                                        value="{{ old('basic_invoice_limit', $settings['basic_invoice_limit']) }}"
                                        min="1" max="999" required>
                                    <span>/bulan</span>
                                </div>
                                @error('basic_invoice_limit')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Limit Produk <span class="text-danger">*</span></label>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="number"
                                        class="form-control @error('basic_product_limit') is-invalid @enderror"
                                        name="basic_product_limit"
                                        value="{{ old('basic_product_limit', $settings['basic_product_limit']) }}"
                                        min="1" max="9999" required>
                                    <span>produk</span>
                                </div>
                                @error('basic_product_limit')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <hr>
                        <h5 style="font-size: 0.9375rem; font-weight: 600; margin-bottom: 1rem;">Paket Pro</h5>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('pro_price') is-invalid @enderror"
                                        name="pro_price" value="{{ old('pro_price', $settings['pro_price']) }}"
                                        min="0" required>
                                    @error('pro_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Limit Invoice <span class="text-danger">*</span></label>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="number"
                                        class="form-control @error('pro_invoice_limit') is-invalid @enderror"
                                        name="pro_invoice_limit"
                                        value="{{ old('pro_invoice_limit', $settings['pro_invoice_limit']) }}"
                                        min="1" max="999" required>
                                    <span>/bulan</span>
                                </div>
                                @error('pro_invoice_limit')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Limit Produk <span class="text-danger">*</span></label>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="number"
                                        class="form-control @error('pro_product_limit') is-invalid @enderror"
                                        name="pro_product_limit"
                                        value="{{ old('pro_product_limit', $settings['pro_product_limit']) }}"
                                        min="1" max="9999" required>
                                    <span>produk</span>
                                </div>
                                @error('pro_product_limit')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>
                        <h5 style="font-size: 0.9375rem; font-weight: 600; margin-bottom: 1rem;">Pengaturan Lainnya</h5>

                        <div class="mb-3">
                            <label class="form-label">WhatsApp Admin <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('whatsapp_admin') is-invalid @enderror"
                                name="whatsapp_admin" value="{{ old('whatsapp_admin', $settings['whatsapp_admin']) }}"
                                placeholder="628123456789" required>
                            <small class="form-text">Nomor WhatsApp untuk konfirmasi pembayaran (format: 628xxx)</small>
                            @error('whatsapp_admin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Grace Period <span class="text-danger">*</span></label>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="number"
                                    class="form-control @error('grace_period_days') is-invalid @enderror"
                                    name="grace_period_days"
                                    value="{{ old('grace_period_days', $settings['grace_period_days']) }}" min="1"
                                    max="30" required style="max-width: 100px;">
                                <span>hari</span>
                            </div>
                            <small class="form-text">Masa tenggang setelah subscription berakhir</small>
                            @error('grace_period_days')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="divider"></div>

                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                            <button type="reset" class="btn btn-outline">
                                <i class="bi bi-x-circle me-2"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i> Simpan Konfigurasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Platform Info & Stats -->
        <div class="col-12 col-lg-4">
            <!-- Platform Info -->
            <div class="card mb-3 mb-md-4">
                <div class="card-header">
                    <h3 class="card-title">Platform Info</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Platform Name
                            </div>
                            <div style="font-weight: 600; font-size: 1.125rem;">InvoiceGen</div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Version
                            </div>
                            <div style="font-weight: 600;">v1.0.0</div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Laravel Version
                            </div>
                            <div style="font-weight: 600;">{{ app()->version() }}</div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                PHP Version
                            </div>
                            <div style="font-weight: 600;">{{ PHP_VERSION }}</div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Environment
                            </div>
                            <span
                                class="badge {{ app()->environment('production') ? 'badge-success' : 'badge-warning' }}">
                                {{ strtoupper(app()->environment()) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Platform Stats -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Platform Statistics</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div
                            style="padding: 1rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: var(--radius); color: white;">
                            <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.25rem;">Total Users</div>
                            <div style="font-size: 2rem; font-weight: 700;">{{ $stats['total_users'] }}</div>
                            <div style="font-size: 0.75rem; opacity: 0.8;">Registered users</div>
                        </div>

                        <div
                            style="padding: 1rem; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: var(--radius); color: white;">
                            <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.25rem;">Active Users</div>
                            <div style="font-size: 2rem; font-weight: 700;">{{ $stats['active_users'] }}</div>
                            <div style="font-size: 0.75rem; opacity: 0.8;">Currently active</div>
                        </div>

                        <div
                            style="padding: 1rem; background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: var(--radius); color: white;">
                            <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.25rem;">Total Revenue</div>
                            <div style="font-size: 1.5rem; font-weight: 700;">
                                {{ formatRupiah($stats['total_revenue'], false) }}</div>
                            <div style="font-size: 0.75rem; opacity: 0.8;">All time earnings</div>
                        </div>

                        <div class="divider"></div>

                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline" style="width: 100%;">
                            <i class="bi bi-people"></i> Kelola User
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
