<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Invoice #{{ $purchase->invoice_no ?? $purchase->id }} - Young Fashion</title>
    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/all.min.css') }}">

    <style>
        :root {
            --primary-dark: #0f172a;
            --border-dark: #1e293b;
        }

        body {
            background-color: #f1f5f9;
            color: #0f172a;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* ── Sticky Top Action Bar ── */
        .action-bar {
            background: #ffffff;
            border-bottom: 1px solid #cbd5e1;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        /* ── Screen Invoice Container (Styled as A5 Landscape Preview) ── */
        .invoice-sheet-wrap {
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .invoice-half-sheet {
            width: 210mm;
            min-height: 140mm;
            max-width: 100%;
            background: #ffffff;
            padding: 14px 18px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            box-sizing: border-box;
            position: relative;
        }

        .copy-watermark {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            background: #f8fafc;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .half-inv-table th,
        .half-inv-table td {
            padding: 4px 6px !important;
            vertical-align: middle;
            border-color: #000000 !important;
        }

        .half-inv-table thead th {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-weight: 700;
            letter-spacing: 0.3px;
        }

        .cut-line-separator {
            width: 210mm;
            max-width: 100%;
            display: none;
            text-align: center;
            position: relative;
            margin: 6px 0;
        }
        .cut-line-separator::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            border-top: 1px dashed #64748b;
            z-index: 1;
        }
        .cut-line-separator span {
            position: relative;
            z-index: 2;
            background: #f1f5f9;
            padding: 0 12px;
            font-size: 10px;
            font-weight: 600;
            color: #64748b;
        }

        /* ── Print Media Queries (Landscape Half Page - Zero Waste) ── */
        @media print {
            .no-print, .action-bar {
                display: none !important;
            }

            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 10px !important;
            }

            .invoice-sheet-wrap {
                padding: 0 !important;
                margin: 0 !important;
                gap: 0 !important;
                display: block !important;
            }

            /* Single Half-Page (A5 Landscape) Default */
            @page {
                size: 210mm 148mm; /* Standard A5 Landscape */
                margin: 3mm 5mm 3mm 5mm;
            }

            .invoice-half-sheet {
                width: 100% !important;
                max-width: 200mm !important;
                min-height: auto !important;
                max-height: 142mm !important;
                padding: 4px 6px !important;
                margin: 0 auto !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }

            /* ── Mode: Print 2-in-1 on A4 Portrait (Store + Vendor Copy) ── */
            body.print-double-mode @page {
                size: A4 portrait !important; /* 210mm x 297mm */
                margin: 5mm !important;
            }

            body.print-double-mode .invoice-half-sheet {
                max-height: 138mm !important;
                padding: 6px 8px !important;
            }

            body.print-double-mode .cut-line-separator {
                display: block !important;
                margin: 4mm 0 !important;
            }
            body.print-double-mode .cut-line-separator span {
                background: #ffffff !important;
            }

            body.print-double-mode #invoiceCopyVendor {
                display: block !important;
            }
        }
    </style>
</head>

<body>

    <!-- ── Sticky Action Bar (Screen Only) ── -->
    <div class="action-bar no-print">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark text-white fw-bold px-2 py-1 fs-6">
                    <i class="fas fa-file-invoice text-warning me-1"></i>
                    {{ $purchase->invoice_no ?? ('PUR-' . str_pad($purchase->id, 3, '0', STR_PAD_LEFT)) }}
                </span>
                <span class="badge bg-success text-white px-2 py-1">
                    {{ $purchase->status_purchase ?? 'Approved' }}
                </span>
                <span class="text-muted small ms-2 d-none d-md-inline">
                    <i class="fas fa-info-circle text-primary me-1"></i>
                    Landscape Half-Page (A5) Layout • Zero Paper Waste
                </span>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- 1. Print Half Page (A5 Landscape) -->
                <button type="button" onclick="printSingleCopy()" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm d-flex align-items-center gap-1.5" style="border-radius: 8px;">
                    <i class="fas fa-print"></i> Print Half-Page (A5 Landscape)
                </button>

                <!-- 2. Print 2-Copies on Single A4 Sheet -->
                <button type="button" onclick="printDoubleCopy()" class="btn btn-dark btn-sm px-3 fw-bold shadow-sm d-flex align-items-center gap-1.5" style="border-radius: 8px; background: #1e293b;" title="Prints Store Copy on Top & Vendor Copy on Bottom of 1 A4 page">
                    <i class="fas fa-copy text-warning"></i> Print 2-in-1 on A4 (Double Copy)
                </button>

                <!-- 3. Thermal Receipt Option -->
                <a href="{{ route('purchase.receipt', $purchase->id) }}" class="btn btn-outline-secondary btn-sm px-2.5 fw-semibold d-flex align-items-center gap-1" style="border-radius: 8px;" title="Thermal Roll Receipt (72mm)">
                    <i class="fas fa-receipt"></i> Thermal Slip
                </a>

                <!-- 4. Back Button -->
                <a href="{{ route('Purchase.home') }}" class="btn btn-outline-dark btn-sm px-3 fw-semibold" style="border-radius: 8px;">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- ── Screen & Print Wrapper ── -->
    <div class="invoice-sheet-wrap">

        {{-- ── COPY 1: STORE / OFFICE COPY ── --}}
        <div class="invoice-half-sheet" id="invoiceCopyStore">
            @include('admin_panel.purchase.partials.half_invoice_content', ['copyTitle' => 'Store / Office Copy'])
        </div>

        {{-- ── CUT LINE (Visible only in 2-in-1 Double Mode) ── --}}
        <div class="cut-line-separator" id="cutLineSeparator">
            <span>✂️ - - - - - - - - - - - - CUT HERE (FOLD &amp; SEPARATE) - - - - - - - - - - - - ✂️</span>
        </div>

        {{-- ── COPY 2: VENDOR / SUPPLIER COPY (Visible in 2-in-1 Double Mode) ── --}}
        <div class="invoice-half-sheet" id="invoiceCopyVendor" style="display: none;">
            @include('admin_panel.purchase.partials.half_invoice_content', ['copyTitle' => 'Supplier / Vendor Copy'])
        </div>

    </div>

    <script>
        function printSingleCopy() {
            document.body.classList.remove('print-double-mode');
            document.getElementById('invoiceCopyVendor').style.display = 'none';
            document.getElementById('cutLineSeparator').style.display = 'none';
            window.print();
        }

        function printDoubleCopy() {
            document.body.classList.add('print-double-mode');
            document.getElementById('invoiceCopyVendor').style.display = 'block';
            document.getElementById('cutLineSeparator').style.display = 'block';
            window.print();
        }

        window.onafterprint = function() {
            // Restore screen preview to single copy
            document.body.classList.remove('print-double-mode');
            document.getElementById('invoiceCopyVendor').style.display = 'none';
            document.getElementById('cutLineSeparator').style.display = 'none';
        };
    </script>
</body>

</html>
