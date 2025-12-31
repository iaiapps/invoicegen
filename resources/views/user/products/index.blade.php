@extends('layouts.app')

@section('title', 'Daftar Produk')
@section('page-title', 'Produk')
@section('page-description', 'Kelola katalog produk/jasa Anda')

@section('content')
    <div class="row g-3 g-md-4">
        <!-- Product Limit Warning -->
        @if (!Auth::user()->hasRole('admin'))
            @php
                $remaining = getRemainingProducts();
                $limit = getProductLimit(Auth::user()->subscription_plan);
                $currentCount = \App\Models\Product::where('user_id', Auth::id())->count();
            @endphp

            @if ($remaining <= 5)
                <div class="col-12">
                    <div class="alert {{ $remaining == 0 ? 'alert-danger' : 'alert-warning' }} mb-0">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="bi {{ $remaining == 0 ? 'bi-x-circle-fill' : 'bi-exclamation-triangle-fill' }}"
                                style="font-size: 1.5rem;"></i>
                            <div style="flex: 1;">
                                @if ($remaining == 0)
                                    <strong>Limit Produk Tercapai!</strong> Anda telah menggunakan
                                    {{ $currentCount }}/{{ $limit }} produk.
                                    <a href="{{ route('subscription.index') }}" class="alert-link">Upgrade paket</a> untuk
                                    menambah limit.
                                @else
                                    <strong>Peringatan Limit!</strong> Sisa kuota produk Anda: {{ $remaining }} dari
                                    {{ $limit }} produk (Paket {{ strtoupper(Auth::user()->subscription_plan) }}).
                                    @if (Auth::user()->subscription_plan == 'free')
                                        <a href="{{ route('subscription.index') }}" class="alert-link">Upgrade sekarang</a>
                                        untuk limit lebih besar.
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <div class="col-12">
            <div class="card">
                <div class="card-header"
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 class="card-title">Katalog Produk</h3>
                        <p class="card-description d-none d-md-block">
                            Kelola semua produk dan jasa Anda
                            @if (!Auth::user()->hasRole('admin'))
                                ({{ $currentCount ?? 0 }}/{{ $limit ?? 0 }} produk)
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('products.create') }}"
                        class="btn btn-primary {{ !canCreateProduct() ? 'disabled' : '' }}">
                        <i class="me-2 bi bi-plus-circle"></i> Tambah Produk
                    </a>
                </div>

                <div class="card-body">
                    <!-- Search & Filter -->
                    <form method="GET" action="{{ route('products.index') }}" class="mb-3 mb-md-4">
                        <div class="row g-2 g-md-3">
                            <div class="col-12 col-md-4 col-lg-3">
                                <input type="text" name="search" class="form-control" placeholder="🔍 Cari nama, SKU..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-6 col-md-3 col-lg-2">
                                <select name="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif
                                    </option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak
                                        Aktif</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <select name="sort_by" class="form-control">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>
                                        Tanggal</option>
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama
                                    </option>
                                    <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Harga
                                    </option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <select name="sort_order" class="form-control">
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Terbaru
                                    </option>
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Terlama
                                    </option>
                                </select>
                            </div>
                            <div class="col-12 col-md-1 col-lg-2">
                                <div style="display: flex; gap: 0.5rem;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <a href="{{ route('products.index') }}" class="btn btn-outline">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Products Grid -->
                    @if ($products->count() > 0)
                        <div class="row g-3 g-md-4">
                            @foreach ($products as $product)
                                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                    <div class="card" style="height: 100%; position: relative;">
                                        <!-- Status Badge -->
                                        <div style="position: absolute; top: 1rem; right: 1rem; z-index: 10;">
                                            @if ($product->is_active)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-secondary">Tidak Aktif</span>
                                            @endif
                                        </div>

                                        <div class="card-body" style="display: flex; flex-direction: column; height: 100%;">
                                            <!-- Product Icon -->
                                            <div
                                                style="width: 4rem; height: 4rem; background: hsl(var(--primary) / 0.1); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                                                <i class="bi bi-box-seam"
                                                    style="font-size: 2rem; color: hsl(var(--primary));"></i>
                                            </div>

                                            <!-- Product Name -->
                                            <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem;">
                                                {{ Str::limit($product->name, 40) }}
                                            </h4>

                                            <!-- SKU -->
                                            @if ($product->sku)
                                                <div
                                                    style="font-size: 0.75rem; color: hsl(var(--muted-foreground)); margin-bottom: 0.5rem;">
                                                    <i class="bi bi-upc"></i> {{ $product->sku }}
                                                </div>
                                            @endif

                                            <!-- Description -->
                                            @if ($product->description)
                                                <p
                                                    style="font-size: 0.875rem; color: hsl(var(--muted-foreground)); margin-bottom: 1rem; flex: 1;">
                                                    {{ Str::limit($product->description, 80) }}
                                                </p>
                                            @else
                                                <div style="flex: 1;"></div>
                                            @endif

                                            <!-- Price -->
                                            <div style="margin-bottom: 1rem;">
                                                <div style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">Harga
                                                </div>
                                                <div
                                                    style="font-size: 1.25rem; font-weight: 700; color: hsl(var(--primary));">
                                                    {{ formatRupiah($product->price) }}
                                                </div>
                                            </div>

                                            <!-- Actions -->
                                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                                <a href="{{ route('products.show', $product) }}"
                                                    class="btn btn-outline btn-sm" title="Detail" style="flex: 1;">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('products.edit', $product) }}"
                                                    class="btn btn-primary btn-sm" title="Edit" style="flex: 1;">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('products.toggle-status', $product) }}"
                                                    method="POST" style="flex: 1;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-secondary btn-sm w-100"
                                                        title="{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                        <i
                                                            class="bi bi-{{ $product->is_active ? 'eye-slash' : 'eye' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div style="margin-top: 2rem;">
                            {{ $products->withQueryString()->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div style="padding: 3rem 1rem; text-align: center;">
                            <i class="bi bi-box-seam"
                                style="font-size: 4rem; color: hsl(var(--muted-foreground)); opacity: 0.3;"></i>
                            <h4 style="margin-top: 1rem; color: hsl(var(--muted-foreground));">
                                @if (request('search') || request('status'))
                                    Tidak ada produk yang ditemukan
                                @else
                                    Belum ada produk
                                @endif
                            </h4>
                            <p style="color: hsl(var(--muted-foreground)); font-size: 0.875rem; margin-bottom: 1.5rem;">
                                @if (request('search') || request('status'))
                                    Coba ubah filter pencarian Anda
                                @else
                                    Mulai tambahkan produk pertama Anda
                                @endif
                            </p>
                            @if (!request('search') && !request('status'))
                                <a href="{{ route('products.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Tambah Produk
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
