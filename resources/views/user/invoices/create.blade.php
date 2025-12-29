@extends('layouts.app')

@section('title', 'Buat Invoice')
@section('page-title', 'Buat Invoice Baru')
@section('page-description', 'Buat invoice untuk customer Anda')

@section('content')
    <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm">
        @csrf

        <div class="row g-3 g-md-4">
            <!-- Main Form -->
            <div class="col-12 col-lg-8">
                <div class="card mb-3 mb-md-4">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Invoice</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Customer -->
                            <div class="col-12">
                                <label for="customer_id" class="form-label">
                                    <i class="bi bi-person"></i> Customer <span style="color: red;">*</span>
                                </label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <select class="form-control @error('customer_id') is-invalid @enderror" id="customer_id"
                                        name="customer_id" required style="flex: 1;">
                                        <option value="">-- Pilih Customer --</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} - {{ $customer->phone }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#addCustomerModal">
                                        <i class="bi bi-plus-circle"></i> Tambah
                                    </button>
                                </div>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date Fields -->
                            <div class="col-md-6">
                                <label for="invoice_date" class="form-label">
                                    <i class="bi bi-calendar"></i> Tanggal Invoice <span style="color: red;">*</span>
                                </label>
                                <input type="date" class="form-control @error('invoice_date') is-invalid @enderror"
                                    id="invoice_date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}"
                                    required>
                                @error('invoice_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="due_date" class="form-label">
                                    <i class="bi bi-calendar-check"></i> Jatuh Tempo <span style="color: red;">*</span>
                                </label>
                                <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                    id="due_date" name="due_date"
                                    value="{{ old('due_date', date('Y-m-d', strtotime('+7 days'))) }}" required>
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="card mb-3 mb-md-4">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 class="card-title">Item Invoice</h3>
                        <button type="button" class="btn btn-success btn-sm" onclick="addItem()">
                            <i class="bi bi-plus-circle"></i> Tambah Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="itemsContainer">
                            <!-- Items will be added here by JavaScript -->
                        </div>
                        @error('items')
                            <div class="text-danger" style="font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div class="card">
                    <div class="card-body">
                        <label for="notes" class="form-label">
                            <i class="bi bi-sticky"></i> Catatan (Opsional)
                        </label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                            placeholder="Terima kasih atas kepercayaan Anda...">{{ old('notes') }}</textarea>
                        <small class="form-text">Catatan ini akan muncul di bagian bawah invoice</small>
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="col-12 col-lg-4">
                <div class="card" style="position: sticky; top: 100px;">
                    <div class="card-header">
                        <h3 class="card-title">Ringkasan</h3>
                    </div>
                    <div class="card-body">
                        <!-- Subtotal -->
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Subtotal:</span>
                            <strong id="displaySubtotal">Rp 0</strong>
                        </div>

                        <!-- Tax -->
                        <div style="margin-bottom: 1rem;">
                            <label for="tax_percentage" class="form-label">Pajak (%):</label>
                            <input type="number" class="form-control" id="tax_percentage" name="tax_percentage"
                                value="{{ old('tax_percentage', 0) }}" min="0" max="100" step="0.01"
                                onchange="calculateTotal()">
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Jumlah Pajak:</span>
                            <strong id="displayTax">Rp 0</strong>
                        </div>

                        <!-- Discount -->
                        <div style="margin-bottom: 1rem;">
                            <label for="discount_amount" class="form-label">Diskon (Rp):</label>
                            <input type="number" class="form-control" id="discount_amount" name="discount_amount"
                                value="{{ old('discount_amount', 0) }}" min="0" step="1"
                                onchange="calculateTotal()">
                        </div>

                        <hr>

                        <!-- Total -->
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                            <h4 style="margin: 0;">TOTAL:</h4>
                            <h4 style="margin: 0; color: hsl(var(--primary));" id="displayTotal">Rp 0</h4>
                        </div>

                        <!-- Buttons -->
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save"></i> Simpan Invoice
                            </button>
                            <a href="{{ route('invoices.index') }}" class="btn btn-outline w-100">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- Modal Add Customer -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Customer Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="quickAddCustomerForm">
                        @csrf
                        <div class="mb-3">
                            <label for="modal_name" class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal_name" name="name" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="modal_phone" class="form-label">No. Telepon <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal_phone" name="phone" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="modal_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="modal_email" name="email">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="modal_address" class="form-label">Alamat</label>
                            <textarea class="form-control" id="modal_address" name="address" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveCustomerBtn">
                        <i class="bi bi-check-circle"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Quick Add Customer Script -->
    <script>
        document.getElementById('saveCustomerBtn').addEventListener('click', function() {
            const btn = this;
            const form = document.getElementById('quickAddCustomerForm');
            const formData = new FormData(form);

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Menyimpan...';

            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            fetch('{{ route('customers.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const select = document.getElementById('customer_id');
                        const option = new Option(
                            `${data.customer.name} - ${data.customer.phone}`,
                            data.customer.id,
                            true,
                            true
                        );
                        select.add(option);

                        const modal = bootstrap.Modal.getInstance(document.getElementById('addCustomerModal'));
                        modal.hide();
                        form.reset();
                    } else if (data.errors) {
                        for (const [field, messages] of Object.entries(data.errors)) {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const feedback = input.nextElementSibling;
                                if (feedback) {
                                    feedback.textContent = messages[0];
                                    feedback.style.display = 'block';
                                }
                            }
                        }
                    }
                })
                .catch(error => alert('Terjadi kesalahan'))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle"></i> Simpan';
                });
        });
    </script>

    <script>
        // Products data from backend
        const products = @json($products);

        let itemCount = 0;

        // Add first item on page load
        document.addEventListener('DOMContentLoaded', function() {
            addItem();
        });

        function addItem() {
            itemCount++;
            const container = document.getElementById('itemsContainer');

            const itemHtml = `
        <div class="card mb-3" id="item-${itemCount}">
            <div class="card-body">
                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1rem;">
                    <h5 style="margin: 0; font-size: 1rem;">Item #${itemCount}</h5>
                    <button type="button" class="btn btn-danger btn-sm ms-auto" onclick="removeItem(${itemCount})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>

                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label">Pilih Produk (Opsional)</label>
                        <select class="form-control" onchange="selectProduct(${itemCount}, this.value)">
                            <option value="">-- Pilih dari katalog atau isi manual --</option>
                            ${products.map(p => `<option value="${p.id}" data-name="${p.name}" data-price="${p.price}" data-desc="${p.description || ''}">${p.name} - ${formatRupiah(p.price)}</option>`).join('')}
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Nama Item <span style="color: red;">*</span></label>
                        <input
                            type="text"
                            class="form-control"
                            name="items[${itemCount}][product_name]"
                            id="item_name_${itemCount}"
                            required
                            placeholder="Nama produk/jasa"
                        >
                        <input type="hidden" name="items[${itemCount}][product_id]" id="item_product_id_${itemCount}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <input
                            type="text"
                            class="form-control"
                            name="items[${itemCount}][description]"
                            id="item_desc_${itemCount}"
                            placeholder="Deskripsi item (opsional)"
                        >
                    </div>

                    <div class="col-4">
                        <label class="form-label">Qty <span style="color: red;">*</span></label>
                        <input
                            type="number"
                            class="form-control"
                            name="items[${itemCount}][quantity]"
                            id="item_qty_${itemCount}"
                            value="1"
                            min="1"
                            step="1"
                            required
                            onchange="calculateTotal()"
                        >
                    </div>

                    <div class="col-4">
                        <label class="form-label">Harga <span style="color: red;">*</span></label>
                        <input
                            type="number"
                            class="form-control"
                            name="items[${itemCount}][price]"
                            id="item_price_${itemCount}"
                            value="0"
                            min="0"
                            step="1"
                            required
                            onchange="calculateTotal()"
                        >
                    </div>

                    <div class="col-4">
                        <label class="form-label">Total</label>
                        <input
                            type="text"
                            class="form-control"
                            id="item_total_${itemCount}"
                            readonly
                            style="background: #f0f0f0; font-weight: 600;"
                            value="Rp 0"
                        >
                    </div>
                </div>
            </div>
        </div>
    `;

            container.insertAdjacentHTML('beforeend', itemHtml);
            calculateTotal();
        }

        function removeItem(id) {
            if (document.querySelectorAll('[id^="item-"]').length <= 1) {
                alert('Minimal harus ada 1 item!');
                return;
            }
            document.getElementById(`item-${id}`).remove();
            calculateTotal();
        }

        function selectProduct(itemId, productId) {
            if (!productId) return;

            const product = products.find(p => p.id == productId);
            if (product) {
                document.getElementById(`item_product_id_${itemId}`).value = product.id;
                document.getElementById(`item_name_${itemId}`).value = product.name;
                document.getElementById(`item_desc_${itemId}`).value = product.description || '';
                document.getElementById(`item_price_${itemId}`).value = product.price;
                calculateTotal();
            }
        }

        function calculateTotal() {
            let subtotal = 0;

            // Calculate subtotal
            document.querySelectorAll('[id^="item-"]').forEach(item => {
                const id = item.id.split('-')[1];
                const qty = parseInt(document.getElementById(`item_qty_${id}`).value) || 0;
                const price = parseInt(document.getElementById(`item_price_${id}`).value) || 0;
                const itemTotal = qty * price;

                document.getElementById(`item_total_${id}`).value = formatRupiah(itemTotal);
                subtotal += itemTotal;
            });

            // Calculate tax (rounded to integer)
            const taxPercentage = parseFloat(document.getElementById('tax_percentage').value) || 0;
            const taxAmount = Math.round((subtotal * taxPercentage) / 100);

            // Calculate discount
            const discountAmount = parseInt(document.getElementById('discount_amount').value) || 0;

            // Calculate total
            const total = subtotal + taxAmount - discountAmount;

            // Display
            document.getElementById('displaySubtotal').textContent = formatRupiah(subtotal);
            document.getElementById('displayTax').textContent = formatRupiah(taxAmount);
            document.getElementById('displayTotal').textContent = formatRupiah(total);
        }

        function formatRupiah(amount) {
            return 'Rp ' + Math.round(amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Auto calculate due date (7 days from invoice date)
        document.getElementById('invoice_date').addEventListener('change', function() {
            const invoiceDate = new Date(this.value);
            const dueDate = new Date(invoiceDate);
            dueDate.setDate(dueDate.getDate() + 7);

            const dueDateStr = dueDate.toISOString().split('T')[0];
            document.getElementById('due_date').value = dueDateStr;
        });
    </script>
@endpush
