<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Print PO - {{ $purchaseOrder->po_number }}</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #f8f9fa;
                font-size: 14px;
            }

            .bill-container {
                max-width: 800px;
                margin: 0 auto;
                background: #fff;
                padding: 30px;
                border: 1px solid #ccc;
                border-radius: 8px;
            }

            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }

            .header h1 {
                margin: 0;
                font-size: 28px;
                text-transform: uppercase;
            }

            .header p {
                margin: 5px 0 0;
                font-size: 14px;
                color: #555;
            }

            .po-title {
                text-align: center;
                font-size: 20px;
                font-weight: bold;
                margin: 20px 0;
                text-decoration: underline;
            }

            .info-section {
                display: flex;
                justify-content: space-between;
                margin-bottom: 30px;
            }

            .info-box {
                width: 48%;
            }

            .info-box h3 {
                font-size: 16px;
                border-bottom: 1px solid #ddd;
                padding-bottom: 5px;
                margin-bottom: 10px;
            }

            .info-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 5px;
            }

            .info-label {
                font-weight: bold;
                color: #555;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }

            th {
                background-color: #f4f4f4;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 12px;
            }

            .text-right {
                text-align: right;
            }

            .text-center {
                text-align: center;
            }

            .total-section {
                display: flex;
                justify-content: flex-end;
                margin-top: 20px;
            }

            .total-box {
                width: 300px;
            }

            .total-row {
                display: flex;
                justify-content: space-between;
                padding: 5px 0;
                font-size: 16px;
                font-weight: bold;
                border-top: 2px solid #333;
            }

            /* ✅ Notes Section */
            .notes-section {
                margin-bottom: 25px;
                padding: 12px 15px;
                background-color: #f8f9fa;
                border-left: 3px solid #aaa;
                border-radius: 2px;
            }

            .notes-section h3 {
                font-size: 13px;
                font-weight: bold;
                text-transform: uppercase;
                margin: 0 0 6px 0;
                color: #444;
                letter-spacing: 0.5px;
                border-bottom: none;
                padding-bottom: 0;
            }

            .notes-section p {
                margin: 0;
                font-size: 13px;
                color: #333;
                white-space: pre-wrap;
                line-height: 1.6;
            }

            .signatures {
                margin-top: 80px;
                display: flex;
                justify-content: space-between;
            }

            .signature-box {
                text-align: center;
                width: 30%;
                border-top: 1px solid #333;
                padding-top: 10px;
            }

            @media print {
                body {
                    background-color: #fff;
                    padding: 0;
                }

                .bill-container {
                    border: none;
                    padding: 0;
                    max-width: 100%;
                }

                .no-print {
                    display: none;
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

            <div class="po-title">PURCHASE ORDER</div>

            <div class="info-section">
                <div class="info-box">
                    <h3>Supplier Details</h3>
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span>{{ $purchaseOrder->supplier->name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span>{{ $purchaseOrder->supplier->address ?? '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Contact:</span>
                        <span>{{ $purchaseOrder->supplier->contact_number ?? '' }}</span>
                    </div>
                </div>
                <div class="info-box">
                    <h3>Order Details</h3>
                    <div class="info-row">
                        <span class="info-label">PO Number:</span>
                        <span><b>#{{ $purchaseOrder->id }}</b></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date:</span>
                        <span>{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('Y-m-d') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span>{{ ucfirst($purchaseOrder->status) }}</span>
                    </div>
                </div>
            </div>

            {{-- ✅ Notes Section - only shown if notes exist --}}
            @if ($purchaseOrder->notes)
                <div class="notes-section">
                    <h3>Notes</h3>
                    <p>{{ $purchaseOrder->notes }}</p>
                </div>
            @endif

            <table>
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%">#</th>
                        <th style="width: 45%">Item Description</th>
                        <th class="text-center" style="width: 15%">Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchaseOrder->items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{ $item->stockItem->name ?? 'Unknown Item' }}
                                @if (optional($item->stockItem)->model_number)
                                    <br><small>({{ $item->stockItem->model_number }})</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="signatures">
                <div class="signature-box">
                    Authorized Signature
                </div>
                <div class="signature-box">
                    Supplier Acceptance
                </div>
            </div>
        </div>

        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    </body>

</html>