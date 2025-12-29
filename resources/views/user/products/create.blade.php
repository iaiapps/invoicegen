@extends('layouts.app')

@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk')
@section('page-description', 'Tambahkan produk/jasa baru ke katalog')

@section('content')
    <div class="row g-3 g-md-4">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Produk Baru</h3>
                    <p class="card-description">Isi data produk dengan lengkap</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="bi bi-box-seam"></i> Nama Produk/Jasa <span style="color: red;">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required placeholder="Laptop HP Core i5"
                                autofocus>
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
                                name="sku" value="{{ old('sku') }}" placeholder="LT-HP-001">
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
                                    id="price" name="price" value="{{ old('price') }}" required min="0"
                                    step="1" placeholder="5000000">
                            </div>
                            <small class="form-text">Masukkan harga tanpa titik atau koma</small>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="bi bi-text-paragraph"></i> Deskripsi
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="4" placeholder="Laptop HP Core i5, RAM 8GB, SSD 256GB...">{{ old('description') }}</textarea>
                            <small class="form-text">Opsional - Deskripsi detail produk/jasa</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Produk
                            </button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline">
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
                                Produk yang sudah disimpan bisa langsung digunakan saat membuat invoice.
                            </p>
                        </div>
                    </div>

                    <div
                        style="background: hsl(var(--muted)); padding: 1rem; border-radius: var(--radius); margin-bottom: 1rem;">
                        <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.75rem;">Field Wajib:</h5>
                        <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.875rem;">
                            <li>Nama Produk/Jasa</li>
                            <li>Harga</li>
                        </ul>
                    </div>

                    <div style="background: hsl(var(--muted)); padding: 1rem; border-radius: var(--radius);">
                        <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.75rem;">Contoh Produk:</h5>
                        <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.875rem;">
                            <li>Barang fisik (Laptop, HP, dll)</li>
                            <li>Jasa (Konsultasi, Desain, dll)</li>
                            <li>Layanan (Maintenance, Support)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Format price input (remove non-numeric)
        document.getElementById('price').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = value;
        });

        // Generate SKU suggestion from name
        document.getElementById('name').addEventListener('blur', function() {
            const skuField = document.getElementById('sku');
            if (skuField.value === '') {
                const name = this.value;
                const words = name.split(' ').filter(w => w.length > 0);
                if (words.length > 0) {
                    const suggestion = words.map(w => w.charAt(0).toUpperCase()).join('') + '-' + Date.now()
                        .toString().slice(-4);
                    skuField.placeholder = 'Contoh: ' + suggestion;
                }
            }
        });
    </script>
@endpush
