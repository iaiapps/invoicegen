@extends('layouts.app')

@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')
@section('page-description', 'Update data produk')

@section('content')
    <div class="row g-3 g-md-4">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Edit Produk</h3>
                    <p class="card-description">Update data produk: {{ $product->name }}</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.update', $product) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="bi bi-box-seam"></i> Nama Produk/Jasa <span style="color: red;">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name', $product->name) }}" required
                                placeholder="Laptop HP Core i5" autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SKU -->
                        <div class="mb-3">
                            <label for="sku" class="form-label">
                                <i class="bi bi-upc"></i> SKU / Kode Produk
                            </label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku"
                                name="sku" value="{{ old('sku', $product->sku) }}" placeholder="LT-HP-001">
                            <small class="form-text">Opsional - Kode unik untuk identifikasi produk</small>
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div class="mb-3">
                            <label for="price" class="form-label">
                                <i class="bi bi-currency-dollar"></i> Harga <span style="color: red;">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control @error('price') is-invalid @enderror"
                                    id="price" name="price" value="{{ old('price', $product->price) }}" required
                                    min="0" step="1" placeholder="5000000">
                            </div>
                            <small class="form-text">Masukkan harga tanpa titik atau koma</small>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="bi bi-text-paragraph"></i> Deskripsi
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="4" placeholder="Laptop HP Core i5, RAM 8GB, SSD 256GB...">{{ old('description', $product->description) }}</textarea>
                            <small class="form-text">Opsional - Deskripsi detail produk/jasa</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <div class="form-check" style="padding-left: 2rem;">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active" style="font-weight: 500;">
                                    Aktifkan produk ini
                                </label>
                                <small class="form-text d-block">Produk yang tidak aktif tidak
                                    akan muncul saat membuat invoice</small>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                            <button type="submit" class="btn btn-primary">
                                <i class="me-2 bi bi-save"></i> Update Produk
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline">
                                <i class="me-2 bi bi-x-circle"></i> Batal
                            </a>
                            <button type="button" class="btn btn-danger ms-auto" onclick="confirmDelete()">
                                <i class="me-2 bi bi-trash"></i> Hapus Produk
                            </button>
                        </div>
                    </form>

                    <!-- Delete Form (Hidden) -->
                    <form id="delete-form" action="{{ route('products.destroy', $product) }}" method="POST"
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
                    <h3 class="card-title">Info Produk</h3>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Status
                            </div>
                            <div style="font-weight: 600;">
                                @if ($product->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Dibuat
                            </div>
                            <div style="font-weight: 600;">
                                {{ $product->created_at->format('d M Y H:i') }}
                            </div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Terakhir Diupdate
                            </div>
                            <div style="font-weight: 600;">
                                {{ $product->updated_at->format('d M Y H:i') }}
                            </div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.25rem;">
                                Digunakan dalam Invoice
                            </div>
                            <div style="font-weight: 600;">
                                {{ $product->invoiceItems()->count() }} kali
                            </div>
                        </div>

                        @if ($product->invoiceItems()->count() > 0)
                            <div style="margin-top: 1rem;">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline w-100">
                                    <i class=" me-2 bi bi-eye"></i> Lihat Detail & Riwayat
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
                                Produk tidak dapat dihapus jika sudah digunakan dalam invoice. Anda bisa menonaktifkan
                                produk.
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
        // Format price input
        document.getElementById('price').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = value;
        });

        // Confirm delete
        function confirmDelete() {
            if (confirm(
                    'Apakah Anda yakin ingin menghapus produk ini?\n\nPeringatan: Produk yang sudah digunakan dalam invoice tidak dapat dihapus!'
                )) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
@endpush
