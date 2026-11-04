{{-- ── 1. Top Header (Company & Invoice Title) ── --}}
<div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom border-dark" style="border-bottom-width: 2px !important;">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-black mb-0 text-dark text-uppercase" style="font-weight: 800; font-size: 19px; letter-spacing: -0.3px;">
                {{ \App\Models\Setting::get('company_name', 'Young Fashion') }}
            </h4>
            <span class="copy-watermark ms-1">{{ $copyTitle ?? 'Original Copy' }}</span>
        </div>
        <div style="font-size: 11px; color: #475569; line-height: 1.4;">
            <span><i class="fas fa-map-marker-alt text-muted me-1"></i> {{ \App\Models\Setting::get('company_address', 'Shaibazar, Hyderabad') }}</span>
            <span class="mx-2">•</span>
            <span><i class="fas fa-phone-alt text-muted me-1"></i> {{ \App\Models\Setting::get('company_phone', '+92 317 3836223') }}</span>
        </div>
    </div>

    <div class="text-end">
        <div class="d-inline-block bg-dark text-white px-2.5 py-1 rounded fw-bold text-uppercase" style="font-size: 10.5px; letter-spacing: 0.8px;">
            Purchase Invoice
        </div>
        <div class="fw-bold text-dark font-monospace mt-1" style="font-size: 14px; line-height: 1.2;">
            #{{ $purchase->invoice_no ?? ('PUR-' . str_pad($purchase->id, 3, '0', STR_PAD_LEFT)) }}
        </div>
        <div class="text-muted mt-0.5" style="font-size: 10.5px;">
            Date: <strong class="text-dark">{{ \Carbon\Carbon::parse($purchase->purchase_date ?? $purchase->created_at)->format('d-M-Y') }}</strong>
        </div>
    </div>
</div>

{{-- ── 2. Vendor & Warehouse Details Grid ── --}}
<div class="row g-2 mb-3" style="font-size: 11px;">
    {{-- Vendor Info --}}
    <div class="col-6">
        <div class="p-2 border rounded bg-light" style="border-color: #cbd5e1 !important; height: 100%;">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted fw-bold text-uppercase" style="font-size: 9.5px; letter-spacing: 0.5px;">Supplier / Vendor Details</span>
                <span class="badge bg-secondary text-white" style="font-size: 9px;">ID: #{{ $purchase->vendor_id ?? '-' }}</span>
            </div>
            <div class="fw-bold text-dark mb-0.5" style="font-size: 12.5px;">
                {{ $purchase->vendor->name ?? 'Walk-in Vendor' }}
            </div>
            <div class="text-truncate text-secondary mb-0.5" style="font-size: 10.5px;">
                <i class="fas fa-location-dot text-muted me-1"></i> {{ $purchase->vendor->address ?? 'Shaibazar, Hyderabad' }}
            </div>
            <div class="text-dark" style="font-size: 10.5px;">
                <strong>Mob:</strong> {{ $purchase->vendor->phone ?? 'N/A' }}
            </div>
        </div>
    </div>

    {{-- Shipment & Receiving Info --}}
    <div class="col-6">
        <div class="p-2 border rounded bg-light" style="border-color: #cbd5e1 !important; height: 100%;">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted fw-bold text-uppercase" style="font-size: 9.5px; letter-spacing: 0.5px;">Receiving &amp; Inventory</span>
                <span class="badge bg-success text-white px-2 py-0.5 text-uppercase" style="font-size: 9px;">{{ $purchase->status_purchase ?? 'Approved' }}</span>
            </div>
            <div class="d-flex justify-content-between py-0.5">
                <div><span class="text-muted">Warehouse:</span> <strong>{{ $purchase->warehouse->warehouse_name ?? 'Main Showroom' }}</strong></div>
                <div><span class="text-muted">Inv Ref:</span> <span class="font-monospace">INV-{{ $purchase->id }}</span></div>
            </div>
            <div class="d-flex justify-content-between py-0.5">
                <div><span class="text-muted">Payment:</span> <strong class="text-dark">{{ $purchase->due_amount <= 0 && $purchase->paid_amount > 0 ? 'Full Paid' : ($purchase->paid_amount > 0 ? 'Partial' : 'Credit / Payable') }}</strong></div>
                <div><span class="text-muted">Printed:</span> <span>{{ date('d/m/y H:i') }}</span></div>
            </div>
        </div>
    </div>
</div>

{{-- ── 3. Items Table (Optimized for Footwear Variants) ── --}}
<table class="table table-bordered mb-3 half-inv-table" style="font-size: 11px;">
    <thead>
        <tr>
            <th class="text-center" style="width: 5%;">#</th>
            <th class="text-start" style="width: 38%;">Shoe Article &amp; Model</th>
            <th class="text-center" style="width: 10%;">Size</th>
            <th class="text-center" style="width: 11%;">Color</th>
            <th class="text-center" style="width: 12%;">Barcode</th>
            <th class="text-center" style="width: 8%;">Qty</th>
            <th class="text-end" style="width: 8%;">Rate</th>
            <th class="text-end" style="width: 8%;">Total</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalQtySum = 0;
            $itemsList = $purchase->items ?? [];
        @endphp
        @forelse($itemsList as $idx => $item)
            @php
                $vSize = '-';
                $vColor = '-';
                $vBarcode = $item->product->item_code ?? '-';
                if (!empty($item->color)) {
                    $decodedColor = base64_decode($item->color, true);
                    $vData = ($decodedColor !== false) ? json_decode($decodedColor, true) : null;
                    if (empty($vData)) {
                        $vData = json_decode($item->color, true);
                    }
                    if (!empty($vData) && is_array($vData)) {
                        $vSize = (!empty($vData['size']) && $vData['size'] !== '-') ? $vData['size'] : '-';
                        $vColor = (!empty($vData['color']) && $vData['color'] !== '-') ? $vData['color'] : '-';
                        if (!empty($vData['barcode'])) $vBarcode = $vData['barcode'];
                    } else {
                        $vColor = (string)$item->color;
                    }
                }
                $qty = (float)$item->qty;
                $totalQtySum += $qty;
            @endphp
            <tr>
                <td class="text-center text-muted fw-bold">{{ $idx + 1 }}</td>
                <td class="text-start fw-bold text-dark">
                    {{ $item->product->item_name ?? 'Shoe Item' }}
                </td>
                <td class="text-center">
                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 10px;">{{ $vSize }}</span>
                </td>
                <td class="text-center">
                    <span class="badge bg-secondary text-white px-2 py-1" style="font-size: 10px;">{{ $vColor }}</span>
                </td>
                <td class="text-center font-monospace text-dark" style="font-size: 10.5px;">
                    {{ $vBarcode }}
                </td>
                <td class="text-center fw-bold text-dark">
                    {{ $qty }} <small class="text-muted">Prs</small>
                </td>
                <td class="text-end font-monospace">
                    {{ number_format((float)$item->price, 2) }}
                </td>
                <td class="text-end fw-bold text-dark font-monospace">
                    {{ number_format((float)$item->line_total, 2) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-3 text-muted">No items recorded in this invoice.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot class="table-light fw-bold" style="font-size: 11px;">
        <tr>
            <td colspan="5" class="text-end text-uppercase pe-2">Total Quantity:</td>
            <td class="text-center text-primary">{{ $totalQtySum }} Prs</td>
            <td class="text-end pe-2">Subtotal:</td>
            <td class="text-end font-monospace">Rs. {{ number_format((float)$purchase->subtotal, 2) }}</td>
        </tr>
    </tfoot>
</table>

{{-- ── 4. Bottom Row: Signatures & Final Totals ── --}}
<div class="row g-3 align-items-end mt-1" style="font-size: 11px;">
    {{-- Left Column: Remarks & 3 Signatures --}}
    <div class="col-7 d-flex flex-column justify-content-between" style="min-height: 120px;">
        <div>
            @if(!empty($purchase->note))
                <div class="p-2 mb-2 bg-light border rounded text-secondary" style="font-size: 10.5px; font-style: italic;">
                    <strong>Note:</strong> {{ $purchase->note }}
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-between pt-4 px-2 text-center" style="font-size: 10.5px;">
            <div style="width: 30%;">
                <div class="border-top border-dark border-1 pt-1.5 fw-bold text-dark">Prepared By</div>
            </div>
            <div style="width: 30%;">
                <div class="border-top border-dark border-1 pt-1.5 fw-bold text-dark">Stock Incharge</div>
            </div>
            <div style="width: 30%;">
                <div class="border-top border-dark border-1 pt-1.5 fw-bold text-dark">Supplier Sign</div>
            </div>
        </div>
    </div>

    {{-- Right Column: Accounting Breakdown Box --}}
    <div class="col-5">
        <div class="p-2 border border-dark rounded bg-light" style="border-width: 1.5px !important;">
            <table class="table table-borderless table-sm mb-0" style="font-size: 11px; line-height: 1.4;">
                <tr>
                    <td class="py-1 text-secondary">Gross Amount:</td>
                    <td class="py-1 text-end font-monospace">Rs. {{ number_format((float)$purchase->subtotal, 2) }}</td>
                </tr>
                @if($purchase->additional_discount > 0)
                    <tr>
                        <td class="py-1 text-danger">Discount:</td>
                        <td class="py-1 text-end text-danger font-monospace">-Rs. {{ number_format((float)$purchase->additional_discount, 2) }}</td>
                    </tr>
                @endif
                @if($purchase->extra_cost > 0)
                    <tr>
                        <td class="py-1 text-secondary">Freight / Extra:</td>
                        <td class="py-1 text-end font-monospace">+Rs. {{ number_format((float)$purchase->extra_cost, 2) }}</td>
                    </tr>
                @endif
                <tr class="border-top border-bottom border-dark fw-bold" style="background: #e2e8f0;">
                    <td class="py-1.5 text-dark" style="font-size: 11.5px;">Net Bill Total:</td>
                    <td class="py-1.5 text-end font-monospace text-dark" style="font-size: 12.5px;">
                        Rs. {{ number_format((float)$purchase->net_amount, 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="py-1 text-success fw-bold">Paid Amount:</td>
                    <td class="py-1 text-end font-monospace text-success fw-bold">Rs. {{ number_format((float)$purchase->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="py-1 text-danger fw-bold">Bill Dues:</td>
                    <td class="py-1 text-end font-monospace text-danger fw-bold">
                        Rs. {{ number_format(max(0, (float)$purchase->net_amount - (float)$purchase->paid_amount), 2) }}
                    </td>
                </tr>
                <tr class="border-top" style="font-size: 10px; border-color: #cbd5e1 !important;">
                    <td class="py-1 text-muted">Prev Balance:</td>
                    <td class="py-1 text-end font-monospace text-muted">Rs. {{ number_format((float)($previousBalance ?? 0), 2) }}</td>
                </tr>
                <tr class="fw-bold text-dark border-top border-dark" style="background: #f1f5f9;">
                    <td class="py-1 text-danger">Vendor Closing:</td>
                    <td class="py-1 text-end font-monospace text-danger" style="font-size: 11px;">
                        Rs. {{ number_format((float)($currentBalance ?? 0), 2) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>