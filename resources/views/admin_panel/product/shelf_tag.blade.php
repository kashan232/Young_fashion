<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->item_name }} – Shelf Tag</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style id="dynamicPrintStyle">
        @page {
            size: 50mm 30mm;
            margin: 0mm;
        }
    </style>

    <style>
        :root {
            --label-width-mm: 50;
            --label-height-mm: 30;
            --name-font-size: 13;
            --price-font-size: 11;
            --offer-font-size: 15;
            --preview-zoom: 1.35;
        }

        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 0;
            color: #0f172a;
        }

        /* ── Top Control Panel ── */
        .control-panel {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: #0f172a;
            color: #f8fafc;
            border-bottom: 2px solid #334155;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 8px 16px;
        }
        .control-panel-inner {
            max-width: 1600px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .cp-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #334155;
            color: #f8fafc;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-back:hover {
            background: #475569;
            color: #ffffff;
        }
        .cp-title {
            display: flex;
            flex-direction: column;
        }
        .cp-article-name {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
            max-width: 240px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cp-sub {
            font-size: 11px;
            color: #fbbf24;
            font-weight: 600;
        }

        .cp-sliders {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            background: #1e293b;
            padding: 5px 12px;
            border-radius: 8px;
            border: 1px solid #334155;
        }
        .control-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .control-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 600;
            color: #cbd5e1;
            gap: 6px;
        }
        .control-val {
            color: #38bdf8;
            font-weight: 700;
            font-family: monospace;
            font-size: 11px;
            background: #0f172a;
            padding: 1px 5px;
            border-radius: 4px;
            border: 1px solid #334155;
        }
        .preset-select, .copies-input {
            background: #0f172a;
            color: #f8fafc;
            border: 1px solid #334155;
            border-radius: 5px;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 600;
            outline: none;
        }
        .copies-input {
            width: 62px;
            text-align: center;
        }
        .control-input-wrap input[type="range"] {
            -webkit-appearance: none;
            width: 78px;
            height: 4px;
            background: #475569;
            border-radius: 4px;
            outline: none;
            cursor: pointer;
        }
        .control-input-wrap input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #38bdf8;
            cursor: pointer;
        }
        .btn-reset {
            background: transparent;
            color: #94a3b8;
            border: 1px solid #475569;
            border-radius: 5px;
            padding: 5px 9px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-reset:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .btn-print-all {
            padding: 7px 16px;
            background: #d97706;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 2px 6px rgba(217, 119, 6, 0.35);
        }
        .btn-print-all:hover { background: #b45309; }

        /* ── Preview Grid ── */
        .page-container {
            max-width: 1400px;
            margin: 28px auto;
            padding: 0 20px;
        }
        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            align-items: flex-start;
        }

        /* ── Shelf Tag Card ── */
        .shelf-tag {
            width: calc(var(--label-width-mm, 50) * 1mm * var(--preview-zoom, 1.35));
            height: calc(var(--label-height-mm, 30) * 1mm * var(--preview-zoom, 1.35));
            padding: calc(1.8mm * var(--preview-zoom, 1.35)) calc(2.4mm * var(--preview-zoom, 1.35));
            background: #ffffff;
            border: 1.5px solid #1e293b;
            border-radius: calc(2mm * var(--preview-zoom, 1.35));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: calc(1mm * var(--preview-zoom, 1.35));
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .tag-rack {
            display: inline-flex;
            align-items: center;
            gap: calc(1mm * var(--preview-zoom, 1.35));
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #86efac;
            border-radius: 999px;
            padding: calc(0.4mm * var(--preview-zoom, 1.35)) calc(2.2mm * var(--preview-zoom, 1.35));
            font-size: calc(8.5px * var(--preview-zoom, 1.35));
            font-weight: 800;
            letter-spacing: 0.2px;
            line-height: 1.15;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tag-product-name {
            font-size: calc(var(--name-font-size, 13) * 1px * var(--preview-zoom, 1.35));
            font-weight: 800;
            color: #0f172a;
            line-height: 1.18;
            width: 100%;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }

        .tag-price-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: calc(1.4mm * var(--preview-zoom, 1.35));
            flex-wrap: wrap;
            font-size: calc(var(--price-font-size, 11) * 1px * var(--preview-zoom, 1.35));
            font-weight: 700;
            color: #334155;
            line-height: 1.1;
        }
        .price-strike {
            color: #dc2626;
            text-decoration: line-through;
            font-weight: 700;
        }
        .disc-pill {
            background: #dc2626;
            color: #ffffff;
            padding: calc(0.3mm * var(--preview-zoom, 1.35)) calc(1.4mm * var(--preview-zoom, 1.35));
            border-radius: 4px;
            font-size: calc((var(--price-font-size, 11) - 1) * 1px * var(--preview-zoom, 1.35));
            font-weight: 800;
            line-height: 1.15;
        }

        .tag-offer-row {
            font-size: calc(var(--offer-font-size, 15) * 1px * var(--preview-zoom, 1.35));
            font-weight: 900;
            color: #15803d;
            line-height: 1.1;
            letter-spacing: -0.2px;
        }

        @media print {
            body {
                background: #ffffff;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .page-container {
                margin: 0;
                padding: 0;
                max-width: none;
            }
            .grid {
                display: block;
                gap: 0;
            }
            .shelf-tag {
                --preview-zoom: 1;
                box-shadow: none;
                border: 1px solid #000000;
                border-radius: 1.5mm;
                margin: 0;
                page-break-after: always;
                break-after: page;
            }
            .shelf-tag:last-child {
                page-break-after: auto;
                break-after: auto;
            }
        }
    </style>
</head>
<body>
@php
    $origPrice = (float) ($discInfo['original_price'] ?? 0);
    $offerPrice = (float) ($discInfo['final_price'] ?? $origPrice);
    $hasDiscount = !empty($discInfo['has_discount']);
    $discPct = (float) ($discInfo['discount_percentage'] ?? 0);
    $discAmt = (float) ($discInfo['discount_amount'] ?? 0);
    $pctLabel = $discPct > 0
        ? rtrim(rtrim(number_format($discPct, 2), '0'), '.') . '%'
        : 'Rs. ' . number_format($discAmt);
@endphp

<div class="control-panel no-print">
    <div class="control-panel-inner">
        <div class="cp-left">
            <a href="{{ route('product') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Product Catalog
            </a>
            <a href="{{ route('generate-barcode-image', $product->id) }}" class="btn-back" style="background:#0f766e;">
                <i class="fas fa-barcode"></i> Barcodes
            </a>
            <div class="cp-title">
                <span class="cp-article-name" title="{{ $product->item_name }}">{{ $product->item_name }}</span>
                <span class="cp-sub">
                    SHELF TAG
                    @if(!empty($product->rack_shelf))
                        • {{ $product->rack_shelf }}
                    @else
                        • No Rack/Shelf Assigned
                    @endif
                </span>
            </div>
        </div>

        <div class="cp-sliders">
            <div class="control-item">
                <div class="control-header"><span>Tag Preset</span></div>
                <select id="presetSelect" class="preset-select" onchange="applyPreset(this.value)">
                    <option value="50x30">50 × 30 mm (Shelf Default)</option>
                    <option value="60x40">60 × 40 mm (Large Shelf)</option>
                    <option value="50x38">50 × 38 mm</option>
                    <option value="38x38">38 × 38 mm</option>
                    <option value="50x25">50 × 25 mm</option>
                </select>
            </div>

            <div class="control-item">
                <div class="control-header"><span>Width</span><span class="control-val" id="valWidth">50mm</span></div>
                <div class="control-input-wrap">
                    <input type="range" id="lblWidth" min="30" max="90" value="50" oninput="setWidth(this.value)">
                </div>
            </div>

            <div class="control-item">
                <div class="control-header"><span>Height</span><span class="control-val" id="valHeight">30mm</span></div>
                <div class="control-input-wrap">
                    <input type="range" id="lblHeight" min="20" max="60" value="30" oninput="setHeight(this.value)">
                </div>
            </div>

            <div class="control-item">
                <div class="control-header"><span>Name</span><span class="control-val" id="valNameFont">13px</span></div>
                <div class="control-input-wrap">
                    <input type="range" id="nameFont" min="9" max="20" value="13" oninput="setNameFont(this.value)">
                </div>
            </div>

            <div class="control-item">
                <div class="control-header"><span>Offer</span><span class="control-val" id="valOfferFont">15px</span></div>
                <div class="control-input-wrap">
                    <input type="range" id="offerFont" min="10" max="24" value="15" oninput="setOfferFont(this.value)">
                </div>
            </div>

            <div class="control-item">
                <div class="control-header"><span>Copies</span></div>
                <input type="number" id="copiesInput" class="copies-input" min="1" max="50" value="1" onchange="renderCopies(this.value)">
            </div>

            <button type="button" class="btn-reset" onclick="resetDefaults()">
                <i class="fas fa-undo"></i> Reset
            </button>
        </div>

        <button type="button" class="btn-print-all" onclick="window.print()">
            <i class="fas fa-print"></i> Print Shelf Tag
        </button>
    </div>
</div>

<div class="page-container">
    <div class="grid" id="tagGrid"></div>
</div>

<template id="shelfTagTemplate">
    <div class="shelf-tag">
        @if(!empty($product->rack_shelf))
            <div class="tag-rack">
                <i class="fas fa-layer-group"></i> {{ $product->rack_shelf }}
            </div>
        @endif

        <div class="tag-product-name" title="{{ $product->item_name }}">{{ $product->item_name }}</div>

        @if($hasDiscount)
            <div class="tag-price-row">
                <span>Price: <span class="price-strike">Rs. {{ number_format($origPrice) }}</span></span>
                <span class="disc-pill">{{ $pctLabel }} OFF</span>
            </div>
            <div class="tag-offer-row">Offer: Rs. {{ number_format($offerPrice) }}</div>
        @else
            <div class="tag-offer-row">Price: Rs. {{ number_format($origPrice) }}</div>
        @endif
    </div>
</template>

<script>
    const STORAGE_KEY = 'yf_shelf_tag_settings_v1';
    const DEFAULTS = { widthMm: 50, heightMm: 30, nameFont: 13, offerFont: 15, preset: '50x30', copies: 1 };
    const PRESETS = {
        '50x30': { widthMm: 50, heightMm: 30, nameFont: 13, offerFont: 15 },
        '60x40': { widthMm: 60, heightMm: 40, nameFont: 15, offerFont: 18 },
        '50x38': { widthMm: 50, heightMm: 38, nameFont: 13, offerFont: 16 },
        '38x38': { widthMm: 38, heightMm: 38, nameFont: 11, offerFont: 13 },
        '50x25': { widthMm: 50, heightMm: 25, nameFont: 11, offerFont: 13 }
    };

    function updatePrintPageSize() {
        const w = document.getElementById('lblWidth').value;
        const h = document.getElementById('lblHeight').value;
        document.getElementById('dynamicPrintStyle').textContent = `@page { size: ${w}mm ${h}mm; margin: 0mm; }`;
    }

    function saveSettings() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({
            widthMm: +document.getElementById('lblWidth').value,
            heightMm: +document.getElementById('lblHeight').value,
            nameFont: +document.getElementById('nameFont').value,
            offerFont: +document.getElementById('offerFont').value,
            preset: document.getElementById('presetSelect').value,
            copies: +document.getElementById('copiesInput').value || 1
        }));
    }

    function setWidth(val, persist = true) {
        document.documentElement.style.setProperty('--label-width-mm', val);
        document.getElementById('valWidth').textContent = val + 'mm';
        updatePrintPageSize();
        if (persist) saveSettings();
    }

    function setHeight(val, persist = true) {
        document.documentElement.style.setProperty('--label-height-mm', val);
        document.getElementById('valHeight').textContent = val + 'mm';
        updatePrintPageSize();
        if (persist) saveSettings();
    }

    function setNameFont(val, persist = true) {
        document.documentElement.style.setProperty('--name-font-size', val);
        document.getElementById('valNameFont').textContent = val + 'px';
        if (persist) saveSettings();
    }

    function setOfferFont(val, persist = true) {
        document.documentElement.style.setProperty('--offer-font-size', val);
        document.documentElement.style.setProperty('--price-font-size', Math.max(9, Math.round(val * 0.75)));
        document.getElementById('valOfferFont').textContent = val + 'px';
        if (persist) saveSettings();
    }

    function applyPreset(name) {
        const p = PRESETS[name];
        if (!p) return;
        document.getElementById('lblWidth').value = p.widthMm;
        document.getElementById('lblHeight').value = p.heightMm;
        document.getElementById('nameFont').value = p.nameFont;
        document.getElementById('offerFont').value = p.offerFont;
        setWidth(p.widthMm, false);
        setHeight(p.heightMm, false);
        setNameFont(p.nameFont, false);
        setOfferFont(p.offerFont, false);
        saveSettings();
    }

    function renderCopies(count) {
        const n = Math.max(1, Math.min(50, parseInt(count, 10) || 1));
        document.getElementById('copiesInput').value = n;
        const grid = document.getElementById('tagGrid');
        const tpl = document.getElementById('shelfTagTemplate');
        grid.innerHTML = '';
        for (let i = 0; i < n; i++) {
            grid.appendChild(tpl.content.cloneNode(true));
        }
        saveSettings();
    }

    function resetDefaults() {
        localStorage.removeItem(STORAGE_KEY);
        document.getElementById('presetSelect').value = DEFAULTS.preset;
        applyPreset(DEFAULTS.preset);
        renderCopies(DEFAULTS.copies);
    }

    (function init() {
        let saved = null;
        try { saved = JSON.parse(localStorage.getItem(STORAGE_KEY)); } catch (e) {}
        const s = Object.assign({}, DEFAULTS, saved || {});
        document.getElementById('presetSelect').value = s.preset || DEFAULTS.preset;
        document.getElementById('lblWidth').value = s.widthMm;
        document.getElementById('lblHeight').value = s.heightMm;
        document.getElementById('nameFont').value = s.nameFont;
        document.getElementById('offerFont').value = s.offerFont;
        setWidth(s.widthMm, false);
        setHeight(s.heightMm, false);
        setNameFont(s.nameFont, false);
        setOfferFont(s.offerFont, false);
        renderCopies(s.copies || 1);
    })();
</script>
</body>
</html>
