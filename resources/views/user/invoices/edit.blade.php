@extends('layouts.app')

@section('title', 'Edit Invoice')
@section('page-title', 'Edit Invoice')
@section('page-description', $invoice->invoice_number)

@section('content')
    <form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoiceForm">
        @csrf
        @method('PUT')

        <div class="row g-3 g-md-4">
            <!-- Main Form -->
            <div class="col-12 col-lg-8">
                <div class="card mb-3 mb-md-4">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Invoice</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Invoice Number (Read Only) -->
                            <div class="col-12">
                                <label class="form-label">
                                    <i class="bi bi-hash"></i> No Invoice
                                </label>
                                <input type="text" class="form-control" value="{{ $invoice->invoice_number }}" readonly
                                    style="background: #f0f0f0; font-weight: 600;">
                            </div>

                            <!-- Customer (Read Only) -->
                            <div class="col-12">
                                <label class="form-label">
                                    <i class="bi bi-person"></i> Customer
                                </label>
                                <input type="text" class="form-control"
                                    value="{{ $invoice->customer->name }} - {{ $invoice->customer->phone }}" readonly
                                    style="background: #f0f0f0;">
                                <input type="hidden" name="customer_id" value="{{ $invoice->customer_id }}">
                                <small class="form-text text-muted">Customer tidak dapat diubah setelah invoice
                                    dibuat</small>
                            </div>

                            <!-- Date Fields -->
                            <div class="col-md-6">
                                <label for="invoice_date" class="form-label">
                                    <i class="bi bi-calendar"></i> Tanggal Invoice <span style="color: red;">*</span>
                                </label>
                                <input type="date" class="form-control @error('invoice_date') is-invalid @enderror"
                                    id="invoice_date" name="invoice_date"
                                    value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required>
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
                                    value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required>
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
                            <i class="me-2 bi bi-plus-circle"></i> Tambah Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="itemsContainer">
                            <!-- Items will be loaded from existing data -->
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
                            placeholder="Terima kasih atas kepercayaan Anda...">{{ old('notes', $invoice->notes) }}</textarea>
                        <small class="form-text">Catatan ini akan muncul di bagian bawah invoice</small>
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="col-12 col-lg-4">
                <div class="card">
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
                                value="{{ old('tax_percentage', $invoice->tax_percentage) }}" min="0" max="100"
                                step="1" onchange="calculateTotal()">
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span>Jumlah Pajak:</span>
                            <strong id="displayTax">Rp 0</strong>
                        </div>

                        <!-- Discount -->
                        <div style="margin-bottom: 1rem;">
                            <label for="discount_amount" class="form-label">Diskon (Rp):</label>
                            <input type="number" class="form-control" id="discount_amount" name="discount_amount"
                                value="{{ old('discount_amount', $invoice->discount_amount) }}" min="0"
                                step="1" onchange="calculateTotal()">
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
                                <i class="me-2 bi bi-save"></i> Update Invoice
                            </button>
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline w-100">
                                <i class="me-2 bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Warning -->
                <div class="card" style="margin-top: 1rem;">
                    <div class="card-body">
                        <div style="display: flex; align-items: start; gap: 1rem;">
                            <div
                                style="width: 3rem; height: 3rem; background: hsl(38 92% 50% / 0.1); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="bi bi-exclamation-triangle"
                                    style="font-size: 1.5rem; color: hsl(38 92% 50%);"></i>
                            </div>
                            <div>
                                <h5 style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem;">Perhatian</h5>
                                <p style="font-size: 0.875rem; color: hsl(var(--muted-foreground)); margin: 0;">
                                    Perubahan yang Anda buat akan menggantikan data invoice yang lama.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        // Products data from backend
        const products = @json($products);

        // Existing items data
        const existingItems = @json($invoice->items);

        let itemCount = 0;

        // Load existing items on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (existingItems && existingItems.length > 0) {
                existingItems.forEach(item => {
                    addItem(item);
                });
            } else {
                addItem();
            }
        });

        function addItem(existingData = null) {
            itemCount++;
            const container = document.getElementById('itemsContainer');

            // Set default values from existing data or empty
            const productName = existingData ? existingData.product_name : '';
            const productId = existingData ? existingData.product_id : '';
            const description = existingData ? existingData.description : '';
            const quantity = existingData ? existingData.quantity : 1;
            const price = existingData ? existingData.price : 0;

            const itemHtml = `
        <div class="card mb-3" id="item-${itemCount}">
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h5 style="margin: 0; font-size: 1rem;">Item #${itemCount}</h5>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${itemCount})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>

                <div class="row g-2">
                    <div class="col-12">
                        <label class="form-label">Pilih Produk (Opsional)</label>
                        <select class="form-control" onchange="selectProduct(${itemCount}, this.value)">
                            <option value="">-- Pilih dari katalog atau isi manual --</option>
                            ${products.map(p => `<option value="${p.id}" ${p.id == productId ? 'selected' : ''} data-name="${p.name}" data-price="${p.price}" data-desc="${p.description || ''}">${p.name} - ${formatRupiah(p.price)}</option>`).join('')}
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Nama Item <span style="color: red;">*</span></label>
                        <input
                            type="text"
                            class="form-control"
                            name="items[${itemCount}][product_name]"
                            id="item_name_${itemCount}"
                            value="${productName}"
                            required
                            placeholder="Nama produk/jasa"
                        >
                        <input type="hidden" name="items[${itemCount}][product_id]" id="item_product_id_${itemCount}" value="${productId}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <input
                            type="text"
                            class="form-control"
                            name="items[${itemCount}][description]"
                            id="item_desc_${itemCount}"
                            value="${description}"
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
                            value="${quantity}"
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
                            value="${price}"
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
                const qtyInput = document.getElementById(`item_qty_${id}`);
                const priceInput = document.getElementById(`item_price_${id}`);

                if (qtyInput && priceInput) {
                    const qty = parseInt(qtyInput.value) || 0;
                    const price = parseInt(priceInput.value) || 0;
                    const itemTotal = qty * price;

                    const totalInput = document.getElementById(`item_total_${id}`);
                    if (totalInput) {
                        totalInput.value = formatRupiah(itemTotal);
                    }
                    subtotal += itemTotal;
                }
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

        // Auto calculate due date
        document.getElementById('invoice_date').addEventListener('change', function() {
            const invoiceDate = new Date(this.value);
            const currentDueDate = new Date(document.getElementById('due_date').value);

            // Only update if due date is before invoice date
            if (currentDueDate < invoiceDate) {
                const dueDate = new Date(invoiceDate);
                dueDate.setDate(dueDate.getDate() + 7);
                document.getElementById('due_date').value = dueDate.toISOString().split('T')[0];
            }
        });
    </script>
@endpush
