@extends('layouts.app')

@section('title', 'Tambah Customer')
@section('page-title', 'Tambah Customer')
@section('page-description', 'Tambahkan customer baru')

@section('content')
    <div class="row g-3 g-md-4">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Customer Baru</h3>
                    <p class="card-description">Isi data customer dengan lengkap</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('customers.store') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="bi bi-person"></i> Nama Customer <span style="color: red;">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required placeholder="PT. Maju Jaya" autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                <i class="bi bi-telephone"></i> Nomor Telepon/WhatsApp <span style="color: red;">*</span>
                            </label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone') }}" required placeholder="081234567890">
                            <small class="form-text">Format: 08xxxxxxxxxx (tanpa spasi atau tanda hubung)</small>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope"></i> Email
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" placeholder="customer@example.com">
                            <small class="form-text">Opsional</small>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="mb-3">
                            <label for="address" class="form-label">
                                <i class="bi bi-geo-alt"></i> Alamat
                            </label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                                placeholder="Jl. Merdeka No. 123, Jakarta">{{ old('address') }}</textarea>
                            <small class="form-text">Opsional</small>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">
                                <i class="bi bi-sticky"></i> Catatan
                            </label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                                placeholder="Catatan khusus untuk customer ini...">{{ old('notes') }}</textarea>
                            <small class="form-text">Opsional - Catatan internal yang tidak terlihat oleh customer</small>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Customer
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 1.5rem;">
                        <div
                            style="width: 3rem; height: 3rem; background: hsl(var(--primary) / 0.1); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-lightbulb" style="font-size: 1.5rem; color: hsl(var(--primary));"></i>
                        </div>
                        <div>
                            <h4 style="margin-bottom: 0.5rem; font-size: 1rem;">Tips</h4>
                            <p style="font-size: 0.875rem; color: hsl(var(--muted-foreground)); margin: 0;">
                                Nomor telepon akan digunakan untuk mengirim invoice via WhatsApp.
                            </p>
                        </div>
                    </div>

                    <div style="background: hsl(var(--muted)); padding: 1rem; border-radius: var(--radius);">
                        <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.75rem;">Field Wajib:</h5>
                        <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.875rem;">
                            <li>Nama Customer</li>
                            <li>Nomor Telepon</li>
                        </ul>
                    </div>

                    <div
                        style="margin-top: 1rem; padding: 1rem; background: hsl(142 76% 36% / 0.1); border-left: 3px solid hsl(142 76% 36%); border-radius: var(--radius);">
                        <p style="font-size: 0.875rem; margin: 0; color: hsl(142 76% 36%);">
                            <i class="bi bi-check-circle-fill"></i> Data customer akan tersimpan dan bisa digunakan untuk
                            membuat invoice
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto-format phone number
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('62')) {
                value = '0' + value.substring(2);
            }
            e.target.value = value;
        });
    </script>
@endpush
