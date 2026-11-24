<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->item_name }} – Barcodes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Dynamic Print Style injected by JavaScript for exact thermal roll dimensions --}}
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
            --col-gap-mm: 2;
            --total-roll-width-mm: 100;
            --article-font-size: 13;
            --variant-font-size: 12;
            --barcode-height: 16;
            --barcode-num-size: 9.5;
            --preview-zoom: 1.25;
        }

        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 0;
            color: #1e293b;
        }

        /* ── Sticky Modern Top Control Panel ── */
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
            max-width: 1700px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Left: Back + Product Info */
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
            transform: translateX(-2px);
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
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cp-variant-count {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Middle: Size Presets & Dimension Adjustments */
        .cp-sliders {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            background: #1e293b;
            padding: 5px 10px;
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
        }
        .control-val {
            color: #38bdf8;
            font-weight: 700;
            font-family: monospace;
            font-size: 11px;
            background: #0f172a;
            padding: 1px 4px;
            border-radius: 4px;
            border: 1px solid #334155;
        }
        .control-input-wrap {
            display: flex;
            align-items: center;
            gap: 3px;
        }
        .control-input-wrap input[type="range"] {
            -webkit-appearance: none;
            width: 65px;
            height: 4px;
            background: #475569;
            border-radius: 4px;
            outline: none;
            cursor: pointer;
        }
        .control-input-wrap input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: #38bdf8;
            cursor: pointer;
            box-shadow: 0 0 5px rgba(56, 189, 248, 0.6);
        }
        .btn-step {
            background: #334155;
            color: #f8fafc;
            border: none;
            border-radius: 4px;
            width: 19px;
            height: 19px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 11px;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-step:hover {
            background: #38bdf8;
            color: #0f172a;
        }

        .select-preset {
            background: #0f172a;
            color: #38bdf8;
            border: 1px solid #334155;
            border-radius: 5px;
            padding: 4px 6px;
            font-size: 11px;
            font-weight: 700;
            outline: none;
            cursor: pointer;
        }
        .select-preset:focus {
            border-color: #38bdf8;
        }

        .toggle-item {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #cbd5e1;
            cursor: pointer;
            user-select: none;
            padding: 3px 6px;
            border-radius: 5px;
            background: #0f172a;
            border: 1px solid #334155;
        }
        .toggle-item input {
            cursor: pointer;
            width: 13px;
            height: 13px;
            accent-color: #38bdf8;
        }
        .btn-reset {
            background: transparent;
            color: #94a3b8;
            border: 1px solid #475569;
            border-radius: 5px;
            padding: 4px 7px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-reset:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* Right: Print Actions */
        .cp-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .select-all-wrap {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 600;
            color: #f8fafc;
            background: #1e293b;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #334155;
            cursor: pointer;
            user-select: none;
        }
        .select-all-wrap input {
            cursor: pointer;
            width: 14px;
            height: 14px;
            accent-color: #38bdf8;
        }
        .btn-print-selected {
            padding: 7px 13px;
            background: #7c3aed;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 6px rgba(124, 58, 237, 0.3);
            transition: all 0.15s;
        }
        .btn-print-selected:hover { background: #6d28d9; }

        .btn-print-all {
            padding: 7px 15px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
            transition: all 0.15s;
        }
        .btn-print-all:hover { background: #1d4ed8; }

        /* ── Page Grid ── */
        .page-container {
            max-width: 1600px;
            margin: 20px auto;
            padding: 0 16px;
        }
        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            justify-content: center;
            align-items: flex-start;
        }
        .barcode-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            background: #e2e8f0;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            transition: box-shadow 0.2s;
        }
        .barcode-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .card-btns {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            width: 100%;
            padding: 2px;
        }
        .card-select-wrap {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11.5px;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            user-select: none;
        }
        .card-select-wrap input {
            cursor: pointer;
            width: 14px;
            height: 14px;
            accent-color: #2563eb;
        }
        .card-copies-wrap {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            background: #cbd5e1;
            border-radius: 4px;
            padding: 2px 4px;
        }
        .card-copies-wrap .copies-label {
            font-size: 10.5px;
            font-weight: 700;
            color: #475569;
            margin-right: 2px;
        }
        .btn-copy-step {
            width: 18px;
            height: 18px;
            line-height: 16px;
            border: none;
            background: #1e293b;
            color: #ffffff;
            border-radius: 3px;
            font-weight: bold;
            font-size: 11px;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-copy-step:hover {
            background: #38bdf8;
            color: #0f172a;
        }
        .card-copies {
            width: 32px;
            height: 19px;
            text-align: center;
            border: 1px solid #94a3b8;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 700;
            background: #ffffff;
            color: #0f172a;
            padding: 0;
            outline: none;
        }
        .card-copies:focus {
            border-color: #2563eb;
        }
        .btn-print-single {
            padding: 3px 8px;
            background: #059669;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .btn-print-single:hover { background: #047857; }

        /* ── Label Element ── */
        .label {
            width: calc(var(--label-width-mm, 32) * 1mm * var(--preview-zoom, 1.25));
            height: calc(var(--label-height-mm, 25) * 1mm * var(--preview-zoom, 1.25));
            padding: calc(1.5mm * var(--preview-zoom, 1.25));
            box-sizing: border-box;
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            border-radius: 2px;
        }

        /* 1. Article / Product Name */
        .product-name {
            font-size: calc(var(--article-font-size, 13) * 1px * var(--preview-zoom, 1.25));
            font-weight: 800;
            color: #000000;
            line-height: 1.15;
            text-align: center;
            width: 100%;
            word-wrap: break-word;
            overflow-wrap: break-word;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            letter-spacing: -0.2px;
            margin: 0;
            padding: 0;
        }

        /* 2. Shoe Size & Color Badges */
        .variant-info {
            font-size: calc(var(--variant-font-size, 12) * 1px * var(--preview-zoom, 1.25));
            font-weight: 800;
            color: #000000;
            line-height: 1.15;
            margin: calc(1px * var(--preview-zoom, 1.25)) 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: calc(4px * var(--preview-zoom, 1.25));
            flex-wrap: wrap;
            width: 100%;
        }
        .size-pill {
            background: #000000;
            color: #ffffff;
            font-size: calc(var(--variant-font-size, 12) * 1px * var(--preview-zoom, 1.25));
            font-weight: 900;
            padding: calc(1.5px * var(--preview-zoom, 1.25)) calc(5px * var(--preview-zoom, 1.25));
            border-radius: 3px;
            letter-spacing: 0.4px;
            display: inline-block;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            white-space: nowrap;
        }
        .color-pill {
            background: #ffffff;
            color: #000000;
            border: 1px solid #000000;
            font-size: calc(var(--variant-font-size, 12) * 0.9px * var(--preview-zoom, 1.25));
            font-weight: 800;
            padding: calc(1px * var(--preview-zoom, 1.25)) calc(4px * var(--preview-zoom, 1.25));
            border-radius: 3px;
            display: inline-block;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            white-space: nowrap;
        }
        .name-pill {
            font-size: calc(var(--variant-font-size, 12) * 0.85px * var(--preview-zoom, 1.25));
            font-weight: 700;
            color: #000000;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        /* 3. Barcode */
        .barcode-wrap {
            margin: 0 auto;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;
        }
        .barcode-wrap img, .barcode-img {
            display: block;
            margin: 0 auto;
            width: auto;
            max-width: 95%;
            height: calc(var(--barcode-height, 16) * 1px * var(--preview-zoom, 1.25));
            object-fit: contain;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: pixelated;
        }

        /* 4. Barcode Numeric Text */
        .barcode-number {
            font-size: calc(var(--barcode-num-size, 9.5) * 1px * var(--preview-zoom, 1.25));
            font-weight: 700;
            color: #000000;
            letter-spacing: 1px;
            line-height: 1;
            margin-top: 1px;
            text-align: center;
            font-family: monospace;
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* 5. Optional Price */
        .price-tag {
            font-size: calc(var(--variant-font-size, 12) * 0.9px * var(--preview-zoom, 1.25));
            font-weight: 900;
            color: #000000;
            line-height: 1;
            margin-top: 1px;
            display: none;
        }
        body.show-price-mode .price-tag {
            display: block !important;
        }

        /* ── Dedicated Print Container ── */
        #printArea {
            display: none;
        }

        /* ── Print Media Queries ── */
        @media print {
            :root {
                --preview-zoom: 1 !important;
            }
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
            .control-panel, .page-container, .card-btns, .card-select-wrap, .no-print {
                display: none !important;
            }
            #printArea {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                width: calc(var(--total-roll-width-mm, 100) * 1mm) !important;
            }
            .print-row {
                display: flex !important;
                flex-direction: row !important;
                align-items: flex-start !important;
                justify-content: flex-start !important;
                gap: calc(var(--col-gap-mm, 2) * 1mm) !important;
                width: calc(var(--total-roll-width-mm, 100) * 1mm) !important;
                height: calc(var(--label-height-mm, 25) * 1mm) !important;
                page-break-after: always !important;
                break-after: page !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }
            .print-row:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }
            .print-row .label {
                width: calc(var(--label-width-mm, 32) * 1mm) !important;
                height: calc(var(--label-height-mm, 25) * 1mm) !important;
                min-height: calc(var(--label-height-mm, 25) * 1mm) !important;
                max-height: calc(var(--label-height-mm, 25) * 1mm) !important;
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 1.5mm !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .product-name {
                font-size: calc(var(--article-font-size, 13) * 1px) !important;
                color: #000000 !important;
                line-height: 1.15 !important;
            }
            .variant-info {
                font-size: calc(var(--variant-font-size, 12) * 1px) !important;
                color: #000000 !important;
                margin: 1px 0 !important;
                gap: 4px !important;
                line-height: 1.15 !important;
            }
            .size-pill {
                font-size: calc(var(--variant-font-size, 12) * 1px) !important;
                padding: 1.5px 5px !important;
                background: #000000 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .color-pill {
                font-size: calc(var(--variant-font-size, 12) * 0.9px) !important;
                padding: 1px 4px !important;
                background: #ffffff !important;
                color: #000000 !important;
                border: 1px solid #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .barcode-wrap img, .barcode-img {
                display: block !important;
                margin: 0 auto !important;
                width: auto !important;
                max-width: 95% !important;
                height: calc(var(--barcode-height, 16) * 1px) !important;
            }
            .barcode-number {
                font-size: calc(var(--barcode-num-size, 9.5) * 1px) !important;
                color: #000000 !important;
            }
            .price-tag {
                font-size: calc(var(--variant-font-size, 12) * 0.9px) !important;
                color: #000000 !important;
            }
        }
    </style>
</head>
<body>

{{-- ── Modern Interactive Control Panel ── --}}
<div class="control-panel no-print">
    <div class="control-panel-inner">
        
        <!-- Left: Product info & navigation -->
        <div class="cp-left">
            <a href="{{ route('products.show', $product->id) }}" class="btn-back" title="Back to Product View">
                <i class="fas fa-arrow-left"></i> Product
            </a>
            <div class="cp-title">
                <span class="cp-article-name" title="{{ $product->item_name }}">{{ $product->item_name }}</span>
                <span class="cp-variant-count">
                    {{ count($variants) > 0 ? count($variants) . ' Variant Labels' : '1 Main Article Label' }}
                </span>
            </div>
        </div>

        <!-- Middle: Label Size, Font & Barcode Adjustments -->
        <div class="cp-sliders">
            
            <!-- 1. Label Preset -->
            <div class="control-item">
                <div class="control-header">
                    <span><i class="fas fa-tag me-1 text-warning"></i> Preset:</span>
                </div>
                <select id="presetSelect" class="select-preset" onchange="onPresetChange(this.value)" title="Choose sticker size preset">
                    <option value="32x25_3col" selected>32×25mm 3 Cols (100mm Roll)</option>
                    <option value="30x20_3col">30×20mm 3 Cols (94mm Roll)</option>
                    <option value="35x25_3col">35×25mm 3 Cols (109mm Roll)</option>
                    <option value="40x25_2col">40×25mm 2 Cols (82mm Roll)</option>
                    <option value="50x30_2col">50×30mm 2 Cols (102mm Roll)</option>
                    <option value="38x38">38×38mm (Shoe Box / 1 Col)</option>
                    <option value="50x30">50×30mm (Standard / 1 Col)</option>
                    <option value="50x25">50×25mm (Compact / 1 Col)</option>
                    <option value="40x30">40×30mm (1 Col)</option>
                    <option value="custom">Custom Dimensions</option>
                </select>
            </div>

            <!-- 2. Columns (1 to 4) -->
            <div class="control-item">
                <div class="control-header">
                    <span><i class="fas fa-columns me-1 text-info"></i> Cols:</span>
                    <span class="control-val" id="valCols">3</span>
                </div>
                <select id="columnsSelect" class="select-preset" onchange="setColumns(this.value)" title="Roll columns (1, 2, 3 or 4 side-by-side)">
                    <option value="1">1 Col</option>
                    <option value="2">2 Cols</option>
                    <option value="3" selected>3 Cols</option>
                    <option value="4">4 Cols</option>
                </select>
            </div>

            <!-- 3. Gap (mm) between columns -->
            <div class="control-item">
                <div class="control-header">
                    <span>Gap:</span>
                    <span class="control-val" id="valGap">2mm</span>
                </div>
                <div class="control-input-wrap">
                    <button type="button" class="btn-step" onclick="adjustGap(-0.5)">−</button>
                    <input type="range" id="colGap" min="0" max="10" step="0.5" value="2" oninput="setColGap(this.value)">
                    <button type="button" class="btn-step" onclick="adjustGap(0.5)">+</button>
                </div>
            </div>

            <!-- 4. Width (mm) -->
            <div class="control-item">
                <div class="control-header">
                    <span>Width:</span>
                    <span class="control-val" id="lblWidthVal">32mm</span>
                </div>
                <div class="control-input-wrap">
                    <button type="button" class="btn-step" onclick="adjustDimension('lblWidth', -1)">−</button>
                    <input type="range" id="lblWidth" min="20" max="100" value="32" oninput="setLabelWidth(this.value)">
                    <button type="button" class="btn-step" onclick="adjustDimension('lblWidth', 1)">+</button>
                </div>
            </div>

            <!-- 5. Height (mm) -->
            <div class="control-item">
                <div class="control-header">
                    <span>Height:</span>
                    <span class="control-val" id="lblHeightVal">25mm</span>
                </div>
                <div class="control-input-wrap">
                    <button type="button" class="btn-step" onclick="adjustDimension('lblHeight', -1)">−</button>
                    <input type="range" id="lblHeight" min="15" max="80" value="25" oninput="setLabelHeight(this.value)">
                    <button type="button" class="btn-step" onclick="adjustDimension('lblHeight', 1)">+</button>
                </div>
            </div>

            <!-- 6. Article Font Size (Bara Font zoomable up to 45px) -->
            <div class="control-item">
                <div class="control-header">
                    <span>Art Font:</span>
                    <span class="control-val" id="articleFontVal">13px</span>
                </div>
                <div class="control-input-wrap">
                    <button type="button" class="btn-step" onclick="adjustFont('articleFontSize', -1)">−</button>
                    <input type="range" id="articleFontSize" min="10" max="45" value="13" oninput="setArticleFontSize(this.value)">
                    <button type="button" class="btn-step" onclick="adjustFont('articleFontSize', 1)">+</button>
                </div>
            </div>

            <!-- 7. Size / Variant Font Size (Bara Size zoomable up to 36px) -->
            <div class="control-item">
                <div class="control-header">
                    <span>Size/Var:</span>
                    <span class="control-val" id="variantFontVal">12px</span>
                </div>
                <div class="control-input-wrap">
                    <button type="button" class="btn-step" onclick="adjustFont('variantFontSize', -1)">−</button>
                    <input type="range" id="variantFontSize" min="8" max="36" value="12" oninput="setVariantFontSize(this.value)">
                    <button type="button" class="btn-step" onclick="adjustFont('variantFontSize', 1)">+</button>
                </div>
            </div>

            <!-- 8. Barcode Height -->
            <div class="control-item">
                <div class="control-header">
                    <span>Barcode H:</span>
                    <span class="control-val" id="barcodeHeightVal">16px</span>
                </div>
                <div class="control-input-wrap">
                    <button type="button" class="btn-step" onclick="adjustFont('barcodeHeight', -2)">−</button>
                    <input type="range" id="barcodeHeight" min="10" max="35" value="16" oninput="setBarcodeHeight(this.value)">
                    <button type="button" class="btn-step" onclick="adjustFont('barcodeHeight', 2)">+</button>
                </div>
            </div>

            <!-- 9. Screen Preview Zoom (Does not affect print) -->
            <div class="control-item">
                <div class="control-header">
                    <span>Zoom:</span>
                </div>
                <select id="zoomSelect" class="select-preset" onchange="setPreviewZoom(this.value)" title="Screen preview zoom (print stays exact mm)">
                    <option value="1">1.0x</option>
                    <option value="1.25" selected>1.25x</option>
                    <option value="1.5">1.5x</option>
                </select>
            </div>

            <!-- 10. Price Toggle -->
            <label class="toggle-item" title="Show or Hide Price Tag on sticker">
                <input type="checkbox" id="togglePrice" onchange="togglePriceDisplay(this.checked)">
                <span>Price</span>
            </label>

            <!-- Reset Button -->
            <button type="button" class="btn-reset" onclick="resetToDefaults()" title="Reset to standard defaults">
                <i class="fas fa-rotate-left"></i> Reset
            </button>
        </div>

        <!-- Right: Print Action Buttons -->
        <div class="cp-actions">
            <label class="select-all-wrap" title="Select or Deselect all labels">
                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                <span>Select All</span>
            </label>
            <button type="button" class="btn-print-selected" onclick="printSelected()">
                <i class="fas fa-print"></i> Print Selected (<span id="selectedCount">0</span>)
            </button>
            <button type="button" class="btn-print-all" onclick="printAll()">
                <i class="fas fa-print"></i> Print All
            </button>
        </div>

    </div>
</div>

{{-- ── Labels Grid ── --}}
<div class="page-container">
    <div class="grid">
    @forelse ($variants as $index => $variant)
        @php
            $vSize = (!empty($variant['size']) && $variant['size'] !== '-') ? trim($variant['size']) : null;
            $vColor = (!empty($variant['color']) && $variant['color'] !== '-') ? trim($variant['color']) : null;
            $vBarcode = $variant['barcode'] ?? null;
            $vPrice = (float)($variant['sale_price'] ?? 0);
        @endphp
        <div class="barcode-card" id="card-{{ $index }}">
            
            <div class="label" id="label-{{ $index }}">
                
                {{-- 1. Article Name (Bara Font for Shelf & Shoe Box) --}}
                <div class="product-name" title="{{ $product->item_name }}">{{ $product->item_name }}</div>

                {{-- 2. Size & Color Prominent Pill for Stock Boy --}}
                <div class="variant-info">
                    @if($vSize)
                        <span class="size-pill">SIZE: {{ $vSize }}</span>
                    @endif
                    @if($vColor)
                        <span class="color-pill">{{ $vColor }}</span>
                    @endif
                    @if(!$vSize && !$vColor && !empty($variant['name']))
                        <span class="name-pill">{{ $variant['name'] }}</span>
                    @endif
                </div>

                {{-- 3. Barcode (Chota & Crisp) --}}
                <div class="barcode-wrap">
                    @if(!empty($vBarcode))
                        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($vBarcode, 'C128', 2, 28) }}" alt="{{ $vBarcode }}" class="barcode-img">
                    @else
                        <span style="color:#ef4444;font-size:9px;font-weight:700;">No Barcode</span>
                    @endif
                </div>

                {{-- 4. Barcode Numeric Text --}}
                <div class="barcode-number">{{ $vBarcode ?: '—' }}</div>

                {{-- 5. Optional Price Tag --}}
                @if($vPrice > 0)
                    <div class="price-tag">Rs. {{ number_format($vPrice) }}</div>
                @endif
            </div>

            {{-- Card Controls --}}
            <div class="card-btns no-print">
                <label class="card-select-wrap">
                    <input type="checkbox" class="barcode-check" value="card-{{ $index }}" onchange="updateSelectState()">
                    <span>Select</span>
                </label>
                <div class="card-copies-wrap" title="Copies of this variant to print">
                    <span class="copies-label">Qty:</span>
                    <button type="button" class="btn-copy-step" onclick="adjustCardCopies('{{ $index }}', -1)">−</button>
                    <input type="number" class="card-copies" id="copies-{{ $index }}" value="1" min="1" max="100">
                    <button type="button" class="btn-copy-step" onclick="adjustCardCopies('{{ $index }}', 1)">+</button>
                </div>
                <button type="button" class="btn-print-single" onclick="printSingleCard('card-{{ $index }}')">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    @empty
        {{-- Fallback when no variants defined --}}
        @php
            $fallbackCode = $product->barcode_path ?: $product->item_code;
            $fallbackPrice = (float)($product->sale_price_per_piece ?: ($product->sale_price_per_box ?: 0));
        @endphp
        <div class="barcode-card" id="card-main">
            <div class="label" id="label-main">
                <div class="product-name" title="{{ $product->item_name }}">{{ $product->item_name }}</div>
                <div class="variant-info">
                    @if(!empty($product->size))
                        <span class="size-pill">SIZE: {{ $product->size }}</span>
                    @else
                        <span class="size-pill">Standard</span>
                    @endif
                    @if(!empty($product->color) && !is_array(json_decode($product->color)))
                        <span class="color-pill">{{ $product->color }}</span>
                    @endif
                </div>
                <div class="barcode-wrap">
                    @if(!empty($fallbackCode))
                        <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($fallbackCode, 'C128', 2, 28) }}" alt="{{ $fallbackCode }}" class="barcode-img">
                    @else
                        <span style="color:#ef4444;font-size:9px;font-weight:700;">No Barcode</span>
                    @endif
                </div>
                <div class="barcode-number">{{ $fallbackCode ?: '—' }}</div>
                @if($fallbackPrice > 0)
                    <div class="price-tag">Rs. {{ number_format($fallbackPrice) }}</div>
                @endif
            </div>
            <div class="card-btns no-print">
                <label class="card-select-wrap">
                    <input type="checkbox" class="barcode-check" value="card-main" onchange="updateSelectState()">
                    <span>Select</span>
                </label>
                <div class="card-copies-wrap" title="Copies of this label to print">
                    <span class="copies-label">Qty:</span>
                    <button type="button" class="btn-copy-step" onclick="adjustCardCopies('main', -1)">−</button>
                    <input type="number" class="card-copies" id="copies-main" value="1" min="1" max="100">
                    <button type="button" class="btn-copy-step" onclick="adjustCardCopies('main', 1)">+</button>
                </div>
                <button type="button" class="btn-print-single" onclick="printSingleCard('card-main')">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
    @endforelse
    </div>
</div>

{{-- ── Dedicated Multi-Column Print Container (Populated dynamically on print) ── --}}
<div id="printArea"></div>

<script>
    const SETTINGS_KEY = 'yf_barcode_settings_v3';

    const DEFAULTS = {
        preset: '32x25_3col',
        widthMm: 32,
        heightMm: 25,
        cols: 3,
        gap: 2,
        articleFontSize: 13,
        variantFontSize: 12,
        barcodeHeight: 16,
        previewZoom: 1.25,
        showPrice: false
    };

    const PRESETS = {
        '32x25_3col': { widthMm: 32, heightMm: 25, cols: 3, gap: 2, articleFontSize: 13, variantFontSize: 12, barcodeHeight: 16 },
        '30x20_3col': { widthMm: 30, heightMm: 20, cols: 3, gap: 2, articleFontSize: 11, variantFontSize: 10, barcodeHeight: 13 },
        '35x25_3col': { widthMm: 35, heightMm: 25, cols: 3, gap: 2, articleFontSize: 13, variantFontSize: 12, barcodeHeight: 16 },
        '40x25_2col': { widthMm: 40, heightMm: 25, cols: 2, gap: 2, articleFontSize: 14, variantFontSize: 13, barcodeHeight: 16 },
        '50x30_2col': { widthMm: 50, heightMm: 30, cols: 2, gap: 2, articleFontSize: 14, variantFontSize: 13, barcodeHeight: 18 },
        '38x38':      { widthMm: 38, heightMm: 38, cols: 1, gap: 0, articleFontSize: 13, variantFontSize: 12, barcodeHeight: 16 },
        '50x30':      { widthMm: 50, heightMm: 30, cols: 1, gap: 0, articleFontSize: 14, variantFontSize: 13, barcodeHeight: 18 },
        '50x25':      { widthMm: 50, heightMm: 25, cols: 1, gap: 0, articleFontSize: 12, variantFontSize: 11, barcodeHeight: 14 },
        '40x30':      { widthMm: 40, heightMm: 30, cols: 1, gap: 0, articleFontSize: 13, variantFontSize: 12, barcodeHeight: 16 }
    };

    function loadSettings() {
        let saved = null;
        try {
            saved = JSON.parse(localStorage.getItem(SETTINGS_KEY));
        } catch(e) {}

        const settings = Object.assign({}, DEFAULTS, saved || {});

        setLabelWidth(settings.widthMm, false);
        setLabelHeight(settings.heightMm, false);
        setColumns(settings.cols || DEFAULTS.cols, false);
        setColGap(settings.gap !== undefined ? settings.gap : DEFAULTS.gap, false);
        setArticleFontSize(settings.articleFontSize, false);
        setVariantFontSize(settings.variantFontSize, false);
        setBarcodeHeight(settings.barcodeHeight, false);
        setPreviewZoom(settings.previewZoom || 1.25, false);
        togglePriceDisplay(settings.showPrice, false);

        document.getElementById('lblWidth').value = settings.widthMm;
        document.getElementById('lblHeight').value = settings.heightMm;
        document.getElementById('columnsSelect').value = settings.cols || DEFAULTS.cols;
        document.getElementById('colGap').value = settings.gap !== undefined ? settings.gap : DEFAULTS.gap;
        document.getElementById('valCols').textContent = settings.cols || DEFAULTS.cols;
        document.getElementById('valGap').textContent = (settings.gap !== undefined ? settings.gap : DEFAULTS.gap) + 'mm';
        document.getElementById('articleFontSize').value = settings.articleFontSize;
        document.getElementById('variantFontSize').value = settings.variantFontSize;
        document.getElementById('barcodeHeight').value = settings.barcodeHeight;
        document.getElementById('zoomSelect').value = settings.previewZoom || 1.25;
        document.getElementById('togglePrice').checked = settings.showPrice;

        if (settings.preset) {
            document.getElementById('presetSelect').value = settings.preset;
        }

        updateDynamicPrintPage();
    }

    function saveSettings() {
        const settings = {
            preset: document.getElementById('presetSelect').value,
            widthMm: parseInt(document.getElementById('lblWidth').value) || DEFAULTS.widthMm,
            heightMm: parseInt(document.getElementById('lblHeight').value) || DEFAULTS.heightMm,
            cols: parseInt(document.getElementById('columnsSelect').value) || DEFAULTS.cols,
            gap: parseFloat(document.getElementById('colGap').value) || DEFAULTS.gap,
            articleFontSize: parseInt(document.getElementById('articleFontSize').value) || DEFAULTS.articleFontSize,
            variantFontSize: parseInt(document.getElementById('variantFontSize').value) || DEFAULTS.variantFontSize,
            barcodeHeight: parseInt(document.getElementById('barcodeHeight').value) || DEFAULTS.barcodeHeight,
            previewZoom: parseFloat(document.getElementById('zoomSelect').value) || 1.25,
            showPrice: document.getElementById('togglePrice').checked
        };
        try {
            localStorage.setItem(SETTINGS_KEY, JSON.stringify(settings));
        } catch(e) {}
    }

    function onPresetChange(presetKey) {
        if (PRESETS[presetKey]) {
            const p = PRESETS[presetKey];
            document.getElementById('lblWidth').value = p.widthMm;
            document.getElementById('lblHeight').value = p.heightMm;
            document.getElementById('columnsSelect').value = p.cols;
            document.getElementById('colGap').value = p.gap;
            document.getElementById('valCols').textContent = p.cols;
            document.getElementById('valGap').textContent = p.gap + 'mm';
            document.getElementById('articleFontSize').value = p.articleFontSize;
            document.getElementById('variantFontSize').value = p.variantFontSize;
            document.getElementById('barcodeHeight').value = p.barcodeHeight;

            setLabelWidth(p.widthMm, false);
            setLabelHeight(p.heightMm, false);
            setColumns(p.cols, false);
            setColGap(p.gap, false);
            setArticleFontSize(p.articleFontSize, false);
            setVariantFontSize(p.variantFontSize, false);
            setBarcodeHeight(p.barcodeHeight, false);

            updateDynamicPrintPage();
            saveSettings();
        }
    }

    function updateDynamicPrintPage() {
        const w = parseInt(document.getElementById('lblWidth').value) || DEFAULTS.widthMm;
        const h = parseInt(document.getElementById('lblHeight').value) || DEFAULTS.heightMm;
        const cols = parseInt(document.getElementById('columnsSelect')?.value, 10) || 1;
        const gap = parseFloat(document.getElementById('colGap')?.value) || 0;

        const totalWidth = cols === 1 ? w : Math.round((w * cols + gap * (cols - 1)) * 10) / 10;

        document.documentElement.style.setProperty('--label-width-mm', w);
        document.documentElement.style.setProperty('--label-height-mm', h);
        document.documentElement.style.setProperty('--col-gap-mm', gap);
        document.documentElement.style.setProperty('--total-roll-width-mm', totalWidth);

        const css = `
            @page {
                size: ${totalWidth}mm ${h}mm !important;
                margin: 0mm !important;
            }
        `;
        document.getElementById('dynamicPrintStyle').textContent = css;
    }

    function setColumns(val, save = true) {
        val = parseInt(val, 10) || 1;
        document.getElementById('valCols').textContent = val;
        updateDynamicPrintPage();
        if (save) {
            checkIfCustomPreset();
            saveSettings();
        }
    }

    function setColGap(val, save = true) {
        val = parseFloat(val) || 0;
        document.getElementById('valGap').textContent = val + 'mm';
        updateDynamicPrintPage();
        if (save) {
            checkIfCustomPreset();
            saveSettings();
        }
    }

    function adjustGap(step) {
        const input = document.getElementById('colGap');
        if (!input) return;
        let val = parseFloat(input.value) + step;
        const min = parseFloat(input.min);
        const max = parseFloat(input.max);
        if (val < min) val = min;
        if (val > max) val = max;
        input.value = val;
        setColGap(val);
    }

    function setLabelWidth(val, save = true) {
        val = parseInt(val) || 32;
        document.documentElement.style.setProperty('--label-width-mm', val);
        document.getElementById('lblWidthVal').textContent = val + 'mm';
        updateDynamicPrintPage();
        if (save) {
            checkIfCustomPreset();
            saveSettings();
        }
    }

    function setLabelHeight(val, save = true) {
        val = parseInt(val) || 25;
        document.documentElement.style.setProperty('--label-height-mm', val);
        document.getElementById('lblHeightVal').textContent = val + 'mm';
        updateDynamicPrintPage();
        if (save) {
            checkIfCustomPreset();
            saveSettings();
        }
    }

    function checkIfCustomPreset() {
        const w = parseInt(document.getElementById('lblWidth').value);
        const h = parseInt(document.getElementById('lblHeight').value);
        const c = parseInt(document.getElementById('columnsSelect').value);
        const g = parseFloat(document.getElementById('colGap').value);

        let matched = false;
        for (const [key, p] of Object.entries(PRESETS)) {
            if (p.widthMm === w && p.heightMm === h && p.cols === c && Math.abs(p.gap - g) < 0.1) {
                document.getElementById('presetSelect').value = key;
                matched = true;
                break;
            }
        }
        if (!matched) {
            document.getElementById('presetSelect').value = 'custom';
        }
    }

    function setArticleFontSize(val, save = true) {
        val = parseInt(val) || 13;
        document.documentElement.style.setProperty('--article-font-size', val);
        document.getElementById('articleFontVal').textContent = val + 'px';
        if (save) saveSettings();
    }

    function setVariantFontSize(val, save = true) {
        val = parseInt(val) || 12;
        document.documentElement.style.setProperty('--variant-font-size', val);
        document.getElementById('variantFontVal').textContent = val + 'px';
        if (save) saveSettings();
    }

    function setBarcodeHeight(val, save = true) {
        val = parseInt(val) || 16;
        document.documentElement.style.setProperty('--barcode-height', val);
        document.getElementById('barcodeHeightVal').textContent = val + 'px';
        if (save) saveSettings();
    }

    function setPreviewZoom(val, save = true) {
        val = parseFloat(val) || 1.25;
        document.documentElement.style.setProperty('--preview-zoom', val);
        if (save) saveSettings();
    }

    function togglePriceDisplay(checked, save = true) {
        if (checked) {
            document.body.classList.add('show-price-mode');
        } else {
            document.body.classList.remove('show-price-mode');
        }
        if (save) saveSettings();
    }

    function adjustDimension(inputId, step) {
        const input = document.getElementById(inputId);
        if (!input) return;
        let val = parseInt(input.value) + step;
        const min = parseInt(input.min);
        const max = parseInt(input.max);
        if (val < min) val = min;
        if (val > max) val = max;
        input.value = val;

        if (inputId === 'lblWidth') setLabelWidth(val);
        else if (inputId === 'lblHeight') setLabelHeight(val);
    }

    function adjustFont(inputId, step) {
        const input = document.getElementById(inputId);
        if (!input) return;
        let val = parseInt(input.value) + step;
        const min = parseInt(input.min);
        const max = parseInt(input.max);
        if (val < min) val = min;
        if (val > max) val = max;
        input.value = val;

        if (inputId === 'articleFontSize') setArticleFontSize(val);
        else if (inputId === 'variantFontSize') setVariantFontSize(val);
        else if (inputId === 'barcodeHeight') setBarcodeHeight(val);
    }

    function adjustCardCopies(indexSuffix, delta) {
        const input = document.getElementById('copies-' + indexSuffix);
        if (!input) return;
        let val = parseInt(input.value, 10) || 1;
        val += delta;
        if (val < 1) val = 1;
        if (val > 100) val = 100;
        input.value = val;
    }

    function resetToDefaults() {
        document.getElementById('presetSelect').value = '32x25_3col';
        onPresetChange('32x25_3col');
        document.getElementById('zoomSelect').value = 1.25;
        setPreviewZoom(1.25);
        document.getElementById('togglePrice').checked = false;
        togglePriceDisplay(false);
    }

    // ── Multi-Column Print Generation ──
    function buildPrintRows(itemsToPrint) {
        const printArea = document.getElementById('printArea');
        printArea.innerHTML = '';

        const cols = parseInt(document.getElementById('columnsSelect')?.value, 10) || 1;
        let currentRow = null;
        let itemsInCurrentRow = 0;

        itemsToPrint.forEach(item => {
            const card = item.card;
            const copies = Math.max(1, parseInt(item.copies, 10) || 1);
            const label = card.querySelector('.label');
            if (!label) return;

            for (let c = 0; c < copies; c++) {
                if (itemsInCurrentRow === 0) {
                    currentRow = document.createElement('div');
                    currentRow.className = 'print-row';
                    printArea.appendChild(currentRow);
                }

                const clone = label.cloneNode(true);
                currentRow.appendChild(clone);
                itemsInCurrentRow++;

                if (itemsInCurrentRow >= cols) {
                    itemsInCurrentRow = 0;
                }
            }
        });

        updateDynamicPrintPage();
    }

    function printAll() {
        const cards = document.querySelectorAll('.barcode-card');
        if (cards.length === 0) return;

        const list = [];
        cards.forEach(card => {
            const suffix = card.id.replace('card-', '');
            const copies = parseInt(document.getElementById('copies-' + suffix)?.value, 10) || 1;
            list.push({ card, copies });
        });

        buildPrintRows(list);
        window.print();
    }

    function printSingleCard(cardId) {
        const card = document.getElementById(cardId);
        if (!card) return;

        const suffix = cardId.replace('card-', '');
        const copies = parseInt(document.getElementById('copies-' + suffix)?.value, 10) || 1;

        buildPrintRows([{ card, copies }]);
        window.print();
    }

    function toggleSelectAll(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.barcode-check');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateSelectState();
    }

    function updateSelectState() {
        const checkboxes = document.querySelectorAll('.barcode-check');
        const checked = document.querySelectorAll('.barcode-check:checked');
        const master = document.getElementById('selectAll');
        if (master) {
            master.checked = checkboxes.length > 0 && checkboxes.length === checked.length;
        }
        const badge = document.getElementById('selectedCount');
        if (badge) {
            badge.textContent = checked.length;
        }
    }

    function printSelected() {
        const checked = document.querySelectorAll('.barcode-check:checked');
        if (checked.length === 0) {
            alert('Please select at least one barcode label to print!');
            return;
        }

        const list = [];
        checked.forEach(cb => {
            const card = document.getElementById(cb.value);
            if (card) {
                const suffix = cb.value.replace('card-', '');
                const copies = parseInt(document.getElementById('copies-' + suffix)?.value, 10) || 1;
                list.push({ card, copies });
            }
        });

        buildPrintRows(list);
        window.print();
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadSettings();
        updateSelectState();
    });
</script>

</body>
</html>
