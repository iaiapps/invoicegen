@extends('layouts.app')

@section('title', 'Daftar Customer')
@section('page-title', 'Customer')
@section('page-description', 'Kelola data customer Anda')

@section('content')
    <div class="row g-3 g-md-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 class="card-title">Daftar Customer</h3>
                        <p class="card-description d-none d-md-block">Kelola semua customer Anda</p>
                    </div>
                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Customer
                    </a>
                </div>

                <div class="card-body">
                    <!-- Search & Filter -->
                    <form method="GET" action="{{ route('customers.index') }}" class="mb-3 mb-md-4">
                        <div class="row g-2 g-md-3">
                            <div class="col-12 col-md-6 col-lg-4">
                                <input type="text" name="search" class="form-control"
                                    placeholder="🔍 Cari nama, telepon, atau email..." value="{{ request('search') }}">
                            </div>
                            <div class="col-6 col-md-3 col-lg-2">
                                <select name="sort_by" class="form-control">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>
                                        Tanggal</option>
                                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nama
                                    </option>
                                </select>
                            </div>
                            <div class="col-6 col-md-3 col-lg-2">
                                <select name="sort_order" class="form-control">
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Terbaru
                                    </option>
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Terlama
                                    </option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div style="display: flex; gap: 0.5rem;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> <span class="d-none d-md-inline">Cari</span>
                                    </button>
                                    <a href="{{ route('customers.index') }}" class="btn btn-outline">
                                        <i class="bi bi-arrow-clockwise"></i> <span class="d-none d-md-inline">Reset</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    @if ($customers->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Telepon</th>
                                        <th>Email</th>
                                        <th>Alamat</th>
                                        <th>Terdaftar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $customer)
                                        <tr>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                    <div class="d-flex"
                                                        style="width: 2.5rem; height: 2.5rem; background: hsl(var(--primary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.875rem;">
                                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div style="font-weight: 600; font-size: 0.875rem;">
                                                            {{ $customer->name }}
                                                        </div>
                                                        <div class="d-md-none"
                                                            style="font-size: 0.75rem; color: hsl(var(--muted-foreground));">
                                                            {{ $customer->phone }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <a href="https://wa.me/{{ formatPhone($customer->phone) }}" target="_blank"
                                                    style="color: #25D366; text-decoration: none;">
                                                    <i class="bi bi-whatsapp"></i> {{ $customer->phone }}
                                                </a>
                                            </td>
                                            <td style="font-size: 0.875rem;">
                                                {{ $customer->email ?: '-' }}
                                            </td>
                                            <td class="" style="font-size: 0.875rem;">
                                                {{ Str::limit($customer->address, 30) ?: '-' }}
                                            </td>
                                            <td style="font-size: 0.8125rem; color: hsl(var(--muted-foreground));">
                                                {{ $customer->created_at->format('d M Y') }}
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                                    <a href="{{ route('customers.show', $customer) }}"
                                                        class="btn btn-outline btn-sm" title="Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('customers.edit', $customer) }}"
                                                        class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button class="btn btn-danger btn-sm" title="Hapus"
                                                        onclick="confirmDelete({{ $customer->id }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Delete Form (Hidden) -->
                                                <form id="delete-form-{{ $customer->id }}"
                                                    action="{{ route('customers.destroy', $customer) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div style="margin-top: 1.5rem;">
                            {{ $customers->withQueryString()->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div style="padding: 3rem 1rem; text-align: center;">
                            <i class="bi bi-people"
                                style="font-size: 4rem; color: hsl(var(--muted-foreground)); opacity: 0.3;"></i>
                            <h4 style="margin-top: 1rem; color: hsl(var(--muted-foreground));">
                                @if (request('search'))
                                    Tidak ada customer yang ditemukan
                                @else
                                    Belum ada customer
                                @endif
                            </h4>
                            <p style="color: hsl(var(--muted-foreground)); font-size: 0.875rem; margin-bottom: 1.5rem;">
                                @if (request('search'))
                                    Coba kata kunci pencarian lain
                                @else
                                    Mulai tambahkan customer pertama Anda
                                @endif
                            </p>
                            @if (!request('search'))
                                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Tambah Customer
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(customerId) {
            if (confirm(
                    'Apakah Anda yakin ingin menghapus customer ini? Data yang sudah dihapus tidak dapat dikembalikan.')) {
                document.getElementById('delete-form-' + customerId).submit();
            }
        }
    </script>
@endpush
