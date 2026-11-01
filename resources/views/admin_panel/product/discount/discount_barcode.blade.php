<!DOCTYPE html>
<html>
<head>
    <title>Product Discount Barcode - Young Fashion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f9f9f9;
        }
        .label {
            border: 1px solid #000;
            padding: 10px;
            width: 250px;
            text-align: center;
            background: #fff;
            border-radius: 6px;
        }
        .brand-name {
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 4px;
            color: #0f172a;
        }
        .barcode {
            margin: 6px 0;
            display: flex;
            justify-content: center;
        }
        .product-info {
            font-size: 13px;
            font-weight: bold;
            margin-top: 4px;
            color: #1e293b;
        }
        .variant-meta {
            font-size: 12px;
            font-weight: 700;
            color: #334155;
            margin: 3px 0;
        }
        .price {
            font-size: 12px;
            margin-top: 4px;
        }
        .price s {
            color: #dc2626;
            font-weight: 600;
        }
        .discount-price {
            font-size: 14px;
            font-weight: 900;
            color: #15803d;
            margin-top: 2px;
        }
        @media print {
            body {
                height: auto;
                background: #fff;
            }
        }
    </style>
</head>
<body onload="window.print()">

<div class="label">
    <div class="brand-name">{{ $discount->product->brand->name ?? 'YOUNG FASHION' }}</div>

    @php
        $barcodeVal = $discount->variant_barcode ?: $discount->product->item_code;
    @endphp

    <div class="barcode">
       {!! DNS1D::getBarcodeHTML($barcodeVal, 'C128', 1.3, 26) !!}
    </div>
    <div style="font-size: 11px; font-family: monospace; letter-spacing: 1px;">{{ $barcodeVal }}</div>

    <div class="product-info">{{ $discount->product->item_name }}</div>

    @if($discount->variant_name || $discount->variant_size)
        <div class="variant-meta">
            Size: {{ $discount->variant_size ?? '-' }} &bull; Color: {{ $discount->variant_color ?? '-' }}
        </div>
    @endif

    <div class="price">ORIGINAL: <s>Rs. {{ number_format($discount->actual_price, 2) }}</s></div>
    <div class="discount-price">OFFER: Rs. {{ number_format($discount->final_price, 2) }}</div>
</div>

</body>
</html>
