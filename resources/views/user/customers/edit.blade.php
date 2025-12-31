@extends('layouts.app')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')
@section('page-description', 'Update data customer')

@section('content')
    <div class="row g-3 g-md-4">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Customer</h3>
                    <p class="card-description">Update data customer: {{ $customer->name }}</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('customers.update', $customer) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="bi bi-person"></i> Nama Customer <span style="color: red;">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $customer->name) }}" required
                                placeholder="PT. Maju Jaya" autofocus>
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
                                name="phone" value="{{ old('phone', $customer->phone) }}" required
                                placeholder="081234567890">
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
                                name="email" value="{{ old('email', $customer->email) }}"
                                placeholder="customer@example.com">
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
                                placeholder="Jl. Merdeka No. 123, Jakarta">{{ old('address', $customer->address) }}</textarea>
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
                                placeholder="Catatan khusus untuk customer ini...">{{ old('notes', $customer->notes) }}</textarea>
                            <small class="form-text">Opsional - Catatan internal yang tidak terlihat oleh customer</small>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Customer
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <button type="button" class="btn btn-danger ms-auto" onclick="confirmDelete()">
                                <i class="bi bi-trash"></i> Hapus Customer
                            </button>
                        </div>
                    </form>

                    <!-- Delete Form (Hidden) -->
                    <form id="delete-form" action="{{ route('customers.destroy', $customer) }}" method="POST"
                        style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Info Customer</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Terdaftar
                            </div>
                            <div style="font-weight: 600;">
                                {{ $customer->created_at->format('d M Y H:i') }}
                            </div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Terakhir Diupdate
                            </div>
                            <div style="font-weight: 600;">
                                {{ $customer->updated_at->format('d M Y H:i') }}
                            </div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Total Invoice
                            </div>
                            <div style="font-weight: 600;">
                                {{ $customer->invoices()->count() }} invoice
                            </div>
                        </div>

                        @if ($customer->invoices()->count() > 0)
                            <div style="margin-top: 1rem;">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline w-100">
                                    <i class="bi bi-eye"></i> Lihat Detail & Invoice
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card" style="margin-top: 1rem;">
                <div class="card-body">
                    <div style="display: flex; align-items: start; gap: 1rem;">
                        <div
                            style="width: 3rem; height: 3rem; background: hsl(var(--destructive) / 0.1); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="bi bi-exclamation-triangle"
                                style="font-size: 1.5rem; color: hsl(var(--destructive));"></i>
                        </div>
                        <div>
                            <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Perhatian</h5>
                            <p style="font-size: 0.875rem; color: hsl(var(--muted-foreground)); margin: 0;">
                                Customer tidak dapat dihapus jika masih memiliki invoice.
                            </p>
                        </div>
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

        // Confirm delete
        function confirmDelete() {
            if (confirm(
                    'Apakah Anda yakin ingin menghapus customer ini?\n\nPeringatan: Customer yang memiliki invoice tidak dapat dihapus!'
                    )) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
@endpush
