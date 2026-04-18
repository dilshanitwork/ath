<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Print PO - {{ $purchaseOrder->id }}</title>
        <style>
            body {
                font-family: 'Helvetica', 'Arial', sans-serif;
                margin: 0;
                padding: 40px;
                background-color: #fff;
                font-size: 14px;
                color: #000;
            }

            .bill-container {
                max-width: 800px;
                margin: 0 auto;
                border: 1px solid #ccc;
                padding: 40px;
                border-radius: 4px;
            }

            /* Header Section */
            .header {
                text-align: center;
                margin-bottom: 20px;
            }

            .header h1 {
                margin: 0;
                font-size: 24px;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .header p {
                margin: 5px 0 0;
                font-size: 12px;
                color: #333;
            }

            .header-line {
                border-top: 1px solid #000;
                margin-top: 10px;
                margin-bottom: 30px;
                width: 100%;
            }

            .po-title {
                text-align: center;
                font-size: 18px;
                font-weight: bold;
                text-decoration: underline;
                margin-bottom: 40px;
                text-transform: uppercase;
            }

            /* Info Section Layout */
            .info-table {
                width: 100%;
                margin-bottom: 40px;
                border-collapse: collapse;
            }

            .info-col {
                width: 50%;
                vertical-align: top;
            }

            .info-col-left {
                padding-right: 30px;
            }

            .info-col-right {
                padding-left: 30px;
            }

            .info-col h3 {
                font-size: 15px;
                font-weight: bold;
                margin: 0 0 15px 0;
                border-bottom: 1px solid #ddd;
                padding-bottom: 8px;
                color: #000;
                text-transform: uppercase;
            }

            /* Data Rows */
            .info-row {
                display: table;
                width: 100%;
                margin-bottom: 8px;
                line-height: 1.4;
            }

            .info-label {
                display: table-cell;
                font-weight: bold;
                color: #444;
                width: 100px;
                vertical-align: top;
            }

            .info-value {
                display: table-cell;
                vertical-align: top;
                color: #000;
            }

            /* ✅ Notes Section */
            .notes-section {
                margin-bottom: 30px;
                padding: 15px;
                background-color: #f8f9fa;
                border-left: 3px solid #aaa;
                border-radius: 2px;
            }

            .notes-section h3 {
                font-size: 13px;
                font-weight: bold;
                text-transform: uppercase;
                margin: 0 0 8px 0;
                color: #444;
                letter-spacing: 0.5px;
            }

            .notes-section p {
                margin: 0;
                font-size: 13px;
                color: #333;
                white-space: pre-wrap;
                line-height: 1.6;
            }

            /* Items Table */
            .items-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 60px;
                font-size: 13px;
            }

            .items-table th {
                background-color: #f8f9fa;
                font-weight: bold;
                text-transform: uppercase;
                padding: 12px 15px;
                text-align: left;
                border: 1px solid #dee2e6;
                font-size: 11px;
                color: #000;
            }

            .items-table td {
                padding: 12px 15px;
                border: 1px solid #dee2e6;
                vertical-align: top;
            }

            .col-num {
                width: 5%;
                text-align: center;
            }

            .col-desc {
                width: 75%;
            }

            .col-qty {
                width: 20%;
                text-align: center;
            }

            .items-table td.text-center {
                text-align: center;
            }

            /* Signatures */
            .signatures-table {
                width: 100%;
                margin-top: 50px;
            }

            .sig-box {
                width: 35%;
                border-top: 1px solid #000;
                text-align: center;
                padding-top: 5px;
                font-size: 13px;
            }

            .sig-spacer {
                width: 30%;
            }

            /* Print adjustments */
            @media print {
                body {
                    padding: 0;
                }

                .bill-container {
                    border: none;
                    padding: 0;
                }

                .header-line {
                    margin-bottom: 20px;
                }

                /* ✅ Ensure notes background prints correctly */
                .notes-section {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }
        </style>
    </head>

    <body>
        <div class="bill-container">
            <div class="header">
                <h1>Amarasinghe Tyre House</h1>
                <p>476, Kaluthara Rd, Panthiya, Mathugama. Contact: 0342248040</p>
            </div>
            <div class="header-line"></div>

            <div class="po-title">PURCHASE ORDER</div>

            <table class="info-table">
                <tr>
                    <td class="info-col info-col-left">
                        <h3>Supplier Details</h3>
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Address:</span>
                            <span class="info-value">{{ $purchaseOrder->supplier->address ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Contact:</span>
                            <span class="info-value">{{ $purchaseOrder->supplier->contact_number ?? '' }}</span>
                        </div>
                    </td>

                    <td class="info-col info-col-right">
                        <h3>Order Details</h3>
                        <div class="info-row">
                            <span class="info-label">PO Number:</span>
                            <span class="info-value"><b>#{{ $purchaseOrder->id }}</b></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date:</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('Y-m-d') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="info-value">{{ ucfirst($purchaseOrder->status) }}</span>
                        </div>
                    </td>
                </tr>
            </table>

            {{-- ✅ Notes Section - only shown if notes exist --}}
            @if ($purchaseOrder->notes)
                <div class="notes-section">
                    <h3>Notes</h3>
                    <p>{{ $purchaseOrder->notes }}</p>
                </div>
            @endif

            <table class="items-table">
                <thead>
                    <tr>
                        <th class="col-num">#</th>
                        <th class="col-desc">ITEM DESCRIPTION</th>
                        <th class="col-qty">QUANTITY</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchaseOrder->items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <span style="font-weight: 500;">{{ $item->stockItem->name ?? 'Unknown Item' }}</span>
                                @if (optional($item->stockItem)->model_number)
                                    <br><small style="color: #666;">({{ $item->stockItem->model_number }})</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="signatures-table">
                <tr>
                    <td class="sig-box">
                        Authorized Signature
                    </td>
                    <td class="sig-spacer"></td>
                    <td class="sig-box">
                        Supplier Acceptance
                    </td>
                </tr>
            </table>
        </div>

        @if (!isset($is_pdf))
            <script>
                window.onload = function() {
                    window.print();
                }
            </script>
        @endif
    </body>

</html>