<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->item_name }} – Shelf Tag</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style id="dynamicPrintStyle">
        @page {
            size: 100mm 25mm;
            margin: 0mm;
        }
    </style>

    <style>
        :root {
            --label-width-mm: 32;
            --label-height-mm: 25;
            --row-width-mm: 100;
            --col-gap-mm: 2;
            --name-font-size: 12;
            --price-font-size: 10;
            --offer-font-size: 13;
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
            color: #94a3b8;
            font-weight: 600;
            gap: 6px;
        }
        .control-val {
            color: #38bdf8;
            font-family: monospace;
            font-size: 11px;
            font-weight: bold;
        }
        .control-input-wrap {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .control-input-wrap input[type="range"] {
            -webkit-appearance: none;
            width: 72px;
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
            box-shadow: 0 0 5px rgba(56, 189, 248, 0.6);
        }
        .preset-select {
            background: #1e293b;
            color: #f8fafc;
            border: 1px solid #475569;
            border-radius: 6px;
            padding: 5px 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            outline: none;
        }
        .preset-select:focus {
            border-color: #38bdf8;
        }
        .copies-input {
            width: 52px;
            background: #1e293b;
            color: #38bdf8;
            border: 1px solid #475569;
            border-radius: 6px;
            padding: 4px 6px;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
            outline: none;
        }
        .copies-input:focus {
            border-color: #38bdf8;
        }
        .btn-reset {
            background: transparent;
            color: #94a3b8;
            border: 1px solid #475569;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-reset:hover {
            background: #334155;
            color: #ffffff;
        }
        .btn-print-all {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
            transition: background 0.15s ease;
        }
        .btn-print-all:hover {
            background: #1d4ed8;
        }

        /* ── Screen Preview Container ── */
        .page-container {
            max-width: 1600px;
            margin: 20px auto;
            padding: 0 16px;
        }
        .grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
            align-items: center;
            justify-content: center;
        }
        .tag-row {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: calc(var(--col-gap-mm, 2) * 1mm * var(--preview-zoom, 1.35));
        }

        /* ── Shelf Tag Card ── */
        .shelf-tag {
            width: calc(var(--label-width-mm, 32) * 1mm * var(--preview-zoom, 1.35));
            height: calc(var(--label-height-mm, 25) * 1mm * var(--preview-zoom, 1.35));
            padding: calc(1.5mm * var(--preview-zoom, 1.35)) calc(2mm * var(--preview-zoom, 1.35));
            background: #ffffff;
            border: 1.5px solid #1e293b;
            border-radius: calc(1.5mm * var(--preview-zoom, 1.35));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: calc(0.8mm * var(--preview-zoom, 1.35));
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            flex-shrink: 0;
            box-sizing: border-box;
        }

        .tag-rack {
            display: inline-flex;
            align-items: center;
            gap: calc(1mm * var(--preview-zoom, 1.35));
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #86efac;
            border-radius: 999px;
            padding: calc(0.3mm * var(--preview-zoom, 1.35)) calc(1.8mm * var(--preview-zoom, 1.35));
            font-size: calc(8px * var(--preview-zoom, 1.35));
            font-weight: 800;
            letter-spacing: 0.2px;
            line-height: 1.1;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .tag-product-name {
            font-size: calc(var(--name-font-size, 12) * 1px * var(--preview-zoom, 1.35));
            font-weight: 800;
            color: #0f172a;
            line-height: 1.15;
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
            gap: calc(1mm * var(--preview-zoom, 1.35));
            flex-wrap: wrap;
            font-size: calc(var(--price-font-size, 10) * 1px * var(--preview-zoom, 1.35));
            font-weight: 800;
            color: #000000;
            line-height: 1.1;
        }
        .price-strike {
            color: #000000;
            text-decoration: line-through 1.5px;
            font-weight: 800;
        }
        .disc-pill {
            background: #000000;
            color: #ffffff;
            padding: calc(0.2mm * var(--preview-zoom, 1.35)) calc(1.2mm * var(--preview-zoom, 1.35));
            border-radius: 3px;
            font-size: calc((var(--price-font-size, 10) - 1) * 1px * var(--preview-zoom, 1.35));
            font-weight: 800;
            line-height: 1.1;
        }

        .tag-offer-row {
            font-size: calc(var(--offer-font-size, 13) * 1px * var(--preview-zoom, 1.35));
            font-weight: 900;
            color: #000000;
            line-height: 1.1;
            letter-spacing: -0.2px;
        }

        @media print {
            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
            .page-container {
                margin: 0 !important;
                padding: 0 !important;
                max-width: none !important;
                width: 100% !important;
            }
            .grid {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .tag-row {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: flex-start !important;
                gap: calc(var(--col-gap-mm, 2) * 1mm) !important;
                width: calc(var(--row-width-mm, 100) * 1mm) !important;
                height: calc(var(--label-height-mm, 25) * 1mm) !important;
                margin: 0 !important;
                padding: 0 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                page-break-after: always !important;
                break-after: page !important;
                box-sizing: border-box !important;
            }
            .tag-row:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }
            .shelf-tag {
                width: calc(var(--label-width-mm, 32) * 1mm) !important;
                height: calc(var(--label-height-mm, 25) * 1mm) !important;
                border: 1px solid #000000 !important;
                border-radius: 1mm !important;
                box-shadow: none !important;
                padding: 1.2mm 1.5mm !important;
                margin: 0 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                page-break-after: avoid !important;
                break-after: avoid !important;
                flex-shrink: 0 !important;
                box-sizing: border-box !important;
            }
            .tag-rack {
                font-size: 7.5px !important;
                padding: 0.2mm 1.5mm !important;
                border: 1px solid #000000 !important;
                color: #000000 !important;
                background: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .tag-product-name {
                font-size: calc(var(--name-font-size, 12) * 1px) !important;
                color: #000000 !important;
                font-weight: 800 !important;
            }
            .tag-price-row {
                font-size: calc(var(--price-font-size, 10) * 1px) !important;
                color: #000000 !important;
                font-weight: 800 !important;
            }
            .price-strike {
                color: #000000 !important;
                font-weight: 800 !important;
                text-decoration: line-through 1.5px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .disc-pill {
                background: #000000 !important;
                color: #ffffff !important;
                font-weight: 800 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .tag-offer-row {
                font-size: calc(var(--offer-font-size, 13) * 1px) !important;
                color: #000000 !important;
                font-weight: 900 !important;
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
                    <option value="32x25_3col">3-Up 32 × 25 mm (3 Columns Roll)</option>
                    <option value="30x20_3col">3-Up 30 × 20 mm (3 Columns Roll)</option>
                    <option value="35x25_3col">3-Up 35 × 25 mm (3 Columns Roll)</option>
                    <option value="40x25_2col">2-Up 40 × 25 mm (2 Columns Roll)</option>
                    <option value="50x30_2col">2-Up 50 × 30 mm (2 Columns Roll)</option>
                    <option value="50x30">50 × 30 mm (Single Column)</option>
                    <option value="60x40">60 × 40 mm (Single Column)</option>
                    <option value="38x38">38 × 38 mm (Single Column)</option>
                    <option value="50x25">50 × 25 mm (Single Column)</option>
                </select>
            </div>

            <!-- Columns Selector -->
            <div class="control-item">
                <div class="control-header"><span>Columns:</span><span class="control-val" id="valCols">3</span></div>
                <select id="columnsSelect" class="preset-select" onchange="setColumns(this.value)" title="Labels per row on the roll">
                    <option value="1">1 Col (Single)</option>
                    <option value="2">2 Cols (2-Up)</option>
                    <option value="3" selected>3 Cols (3-Up Roll)</option>
                    <option value="4">4 Cols (4-Up)</option>
                </select>
            </div>

            <div class="control-item">
                <div class="control-header"><span>Width</span><span class="control-val" id="valWidth">32mm</span></div>
                <div class="control-input-wrap">
                    <input type="range" id="lblWidth" min="20" max="90" value="32" oninput="setWidth(this.value)">
                </div>
            </div>

            <div class="control-item">
                <div class="control-header"><span>Height</span><span class="control-val" id="valHeight">25mm</span></div>
                <div class="control-input-wrap">
                    <input type="range" id="lblHeight" min="15" max="60" value="25" oninput="setHeight(this.value)">
                </div>
            </div>

            <!-- Horizontal Gap between labels -->
            <div class="control-item">
                <div class="control-header"><span>Gap</span><span class="control-val" id="valGap">2mm</span></div>
                <div class="control-input-wrap">
                    <input type="range" id="colGap" min="0" max="6" value="2" oninput="setColGap(this.value)">
                </div>
            </div>

            <div class="control-item">
                <div class="control-header"><span>Name</span><span class="control-val" id="valNameFont">12px</span></div>
                <div class="control-input-wrap">
                    <input type="range" id="nameFont" min="8" max="24" value="12" oninput="setNameFont(this.value)">
                </div>
            </div>

            <div class="control-item">
                <div class="control-header"><span>Offer</span><span class="control-val" id="valOfferFont">13px</span></div>
                <div class="control-input-wrap">
                    <input type="range" id="offerFont" min="9" max="26" value="13" oninput="setOfferFont(this.value)">
                </div>
            </div>

            <div class="control-item">
                <div class="control-header"><span>Copies</span></div>
                <input type="number" id="copiesInput" class="copies-input" min="1" max="200" value="3" onchange="renderCopies(this.value)">
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
    const STORAGE_KEY = 'yf_shelf_tag_settings_v3';
    const DEFAULTS = {
        widthMm: 32,
        heightMm: 25,
        cols: 3,
        gap: 2,
        nameFont: 12,
        offerFont: 13,
        preset: '32x25_3col',
        copies: 3
    };

    const PRESETS = {
        '32x25_3col': { widthMm: 32, heightMm: 25, cols: 3, gap: 2, nameFont: 12, offerFont: 13 },
        '30x20_3col': { widthMm: 30, heightMm: 20, cols: 3, gap: 2, nameFont: 10, offerFont: 11 },
        '35x25_3col': { widthMm: 35, heightMm: 25, cols: 3, gap: 2, nameFont: 13, offerFont: 14 },
        '40x25_2col': { widthMm: 40, heightMm: 25, cols: 2, gap: 2, nameFont: 13, offerFont: 14 },
        '50x30_2col': { widthMm: 50, heightMm: 30, cols: 2, gap: 2, nameFont: 13, offerFont: 15 },
        '50x30':      { widthMm: 50, heightMm: 30, cols: 1, gap: 0, nameFont: 13, offerFont: 15 },
        '60x40':      { widthMm: 60, heightMm: 40, cols: 1, gap: 0, nameFont: 15, offerFont: 18 },
        '38x38':      { widthMm: 38, heightMm: 38, cols: 1, gap: 0, nameFont: 11, offerFont: 13 },
        '50x25':      { widthMm: 50, heightMm: 25, cols: 1, gap: 0, nameFont: 11, offerFont: 13 }
    };

    function updatePrintPageSize() {
        const w = parseFloat(document.getElementById('lblWidth').value) || 32;
        const h = parseFloat(document.getElementById('lblHeight').value) || 25;
        const cols = parseInt(document.getElementById('columnsSelect').value, 10) || 1;
        const gap = parseFloat(document.getElementById('colGap')?.value || 2);

        const totalWidth = cols === 1 ? w : (w * cols + gap * (cols - 1));

        document.documentElement.style.setProperty('--row-width-mm', totalWidth);
        document.documentElement.style.setProperty('--col-gap-mm', gap);

        document.getElementById('dynamicPrintStyle').textContent = `
            @page {
                size: ${totalWidth}mm ${h}mm;
                margin: 0mm;
            }
        `;
    }

    function saveSettings() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({
            widthMm: +document.getElementById('lblWidth').value,
            heightMm: +document.getElementById('lblHeight').value,
            cols: +document.getElementById('columnsSelect').value || 1,
            gap: +document.getElementById('colGap').value || 2,
            nameFont: +document.getElementById('nameFont').value,
            offerFont: +document.getElementById('offerFont').value,
            preset: document.getElementById('presetSelect').value,
            copies: +document.getElementById('copiesInput').value || 1
        }));
    }

    function setColumns(val, persist = true) {
        val = parseInt(val, 10) || 1;
        document.getElementById('valCols').textContent = val;
        renderCopies(document.getElementById('copiesInput').value, false);
        updatePrintPageSize();
        if (persist) saveSettings();
    }

    function setColGap(val, persist = true) {
        val = parseFloat(val) || 0;
        document.documentElement.style.setProperty('--col-gap-mm', val);
        document.getElementById('valGap').textContent = val + 'mm';
        updatePrintPageSize();
        if (persist) saveSettings();
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
        document.documentElement.style.setProperty('--price-font-size', Math.max(8, Math.round(val * 0.75)));
        document.getElementById('valOfferFont').textContent = val + 'px';
        if (persist) saveSettings();
    }

    function applyPreset(name) {
        const p = PRESETS[name];
        if (!p) return;
        document.getElementById('lblWidth').value = p.widthMm;
        document.getElementById('lblHeight').value = p.heightMm;
        document.getElementById('columnsSelect').value = p.cols;
        document.getElementById('colGap').value = p.gap;
        document.getElementById('valCols').textContent = p.cols;
        document.getElementById('valGap').textContent = p.gap + 'mm';
        document.getElementById('nameFont').value = p.nameFont;
        document.getElementById('offerFont').value = p.offerFont;

        setWidth(p.widthMm, false);
        setHeight(p.heightMm, false);
        setNameFont(p.nameFont, false);
        setOfferFont(p.offerFont, false);
        renderCopies(document.getElementById('copiesInput').value, false);
        updatePrintPageSize();
        saveSettings();
    }

    function renderCopies(count, persist = true) {
        const n = Math.max(1, Math.min(200, parseInt(count, 10) || 1));
        document.getElementById('copiesInput').value = n;
        const grid = document.getElementById('tagGrid');
        const tpl = document.getElementById('shelfTagTemplate');
        grid.innerHTML = '';

        const cols = parseInt(document.getElementById('columnsSelect')?.value, 10) || 1;

        let currentRow = null;
        for (let i = 0; i < n; i++) {
            if (i % cols === 0) {
                currentRow = document.createElement('div');
                currentRow.className = 'tag-row';
                grid.appendChild(currentRow);
            }
            currentRow.appendChild(tpl.content.cloneNode(true));
        }

        updatePrintPageSize();
        if (persist) saveSettings();
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
        document.getElementById('columnsSelect').value = s.cols || DEFAULTS.cols;
        document.getElementById('valCols').textContent = s.cols || DEFAULTS.cols;
        document.getElementById('colGap').value = s.gap !== undefined ? s.gap : DEFAULTS.gap;
        document.getElementById('valGap').textContent = (s.gap !== undefined ? s.gap : DEFAULTS.gap) + 'mm';
        document.getElementById('nameFont').value = s.nameFont;
        document.getElementById('offerFont').value = s.offerFont;

        setWidth(s.widthMm, false);
        setHeight(s.heightMm, false);
        setNameFont(s.nameFont, false);
        setOfferFont(s.offerFont, false);
        renderCopies(s.copies || DEFAULTS.copies, false);
        updatePrintPageSize();
    })();
</script>
</body>
</html>
