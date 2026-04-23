<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Print Direct Bill - {{ $directBill->bill_number }}</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 10px;
                background-color: #f8f9fa;
                font-size: 12px;
            }

            p {
                margin: 0 0 2px 0;
            }

            .bill-container {
                max-width: 700px;
                margin: 0 auto;
                background: #fff;
                padding: 15px;
                border: 1px solid #ccc;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                /* ADDED FOR WATERMARK POSITIONING */
                position: relative;
                z-index: 1;
                overflow: hidden;
            }

            /* --- WATERMARK STYLE --- */
            .watermark {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 350px;
                /* Adjust size as needed */
                opacity: 0.05;
                /* Adjust transparency (0.1 is faint) */
                z-index: -1;
                /* Puts it behind the text */
                pointer-events: none;
                /* Allows clicking through it */
            }

            h1,
            h2,
            h3,
            h4 {
                text-align: center;
                color: #333;
                margin: 5px 0;
            }

            .bill-header {
                border-bottom: 1px solid #007bff;
                padding-bottom: 5px;
                margin-bottom: 10px;
            }

            .bill-summary {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
                font-size: 11px;
            }

            .bill-summary div {
                width: 48%;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
                font-size: 11px;
            }

            .table th,
            .table td {
                border: 1px solid #ddd;
                padding: 4px;
                text-align: left;
            }

            .table th {
                background-color: #f1f1f1;
                color: #333;
            }

            .table td {
                vertical-align: top;
            }

            .text-right {
                text-align: right;
            }

            .text-center {
                text-align: center;
            }

            .total-row {
                font-weight: bold;
                background-color: #f9f9f9;
            }

            .signatures {
                margin-top: 30px;
                display: flex;
                justify-content: space-between;
            }

            .signature-block {
                text-align: center;
                width: 30%;
            }

            .signature-block span {
                display: block;
                margin-top: 30px;
                border-top: 1px solid #333;
                font-size: 11px;
                padding-top: 5px;
            }

            .footer {
                text-align: center;
                margin-top: 10px;
                color: #777;
                font-size: 10px;
            }

            @media print {
                @page {
                    margin: 0.5cm;
                }

                body {
                    background: #fff;
                    padding: 0;
                }

                .bill-container {
                    box-shadow: none;
                    border: none;
                    padding: 0;
                    max-width: 100%;
                }

                .footer {
                    /* display: none; */
                }

                /* Ensure watermark prints correctly */
                .watermark {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }
        </style>
    </head>

    <body>
        <div class="bill-container">
            <!--<img src="{{ asset('img/Tire_logo.png') }}" alt="Watermark" class="watermark">-->
            <div class="bill-header">
                <!-- Drastically reduced font size for header to save space -->
                <h1 style="font-size: 24px; margin: 0;">Tyre House</h1>
                <p style="text-align: center; font-size: 11px;">476, Kaluthara Rd, Panthiya, Mathugama. Contact:
                    0342248040</p>
            </div>

            <h2 style="font-size: 16px; margin: 5px 0;">INVOICE @if ($directBill->type == 'credit')
                    (CREDIT)
                @endif
            </h2>

            <div class="bill-summary">
                <div>
                    <p><strong>Customer:</strong> {{ $directBill->customer_name }}</p>
                    <p><strong>Contact:</strong> {{ $directBill->contact_number }}</p>
                </div>

                <div style="text-align: right;margin-right: 60px;">
                    <p><strong>Invoice No:</strong> {{ $directBill->bill_number }}</p>
                    <p><strong>Date:</strong> {{ $directBill->created_at->format('Y-m-d') }}</p>
                    <p><strong>Vehicle:</strong> {{ $directBill->vehicle }} </p>
                </div>
            </div>

            <div class="bill-items">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 35%;">Description</th>
                            <th style="width: 15%;">Pattern</th>
                            <th class="text-center" style="width: 5%;">Qty</th>
                            <th class="text-right" style="width: 15%;">Price</th>
                            <th class="text-right" style="width: 10%;">Disc.</th>
                            <th class="text-right" style="width: 15%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($directBill->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td style="word-spacing: 3px;">{{ $item->item_name }}</td>
                                <td>{{ $item->stockItem->model_number ?? '-' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-right">{{ number_format($item->item_discount, 2) }}</td>
                                <td class="text-right">{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="6" class="text-right">Subtotal:</td>
                            <td class="text-right">{{ number_format($directBill->bill_total, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="6" class="text-right">Total Discount:</td>
                            <td class="text-right">{{ number_format($directBill->discount, 2) }}</td>
                        </tr>
                        <tr class="total-row" style="font-size: 12px;">
                            <td colspan="6" class="text-right">NET TOTAL (Rs.):</td>
                            <td class="text-right">{{ number_format($directBill->final_amount, 2) }}</td>
                        </tr>
                        <!-- Added Paid and Balance rows for Credit Bills -->
                        @if ($directBill->type == 'credit' || $directBill->balance > 0)
                            <tr class="total-row">
                                <td colspan="6" class="text-right">Paid Amount:</td>
                                <td class="text-right">{{ number_format($directBill->paid, 2) }}</td>
                            </tr>
                            <tr class="total-row">
                                <td colspan="6" class="text-right">Balance Due:</td>
                                <td class="text-right">{{ number_format($directBill->balance, 2) }}</td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>

            @if ($directBill->note)
                <div style="margin-top: 5px; font-size: 11px;">
                    <strong>Note:</strong> {{ $directBill->note }}
                </div>
            @endif

            <div class="signatures">
                <div class="signature-block">
                    <span>Customer Signature</span>
                </div>
                <div class="signature-block">
                    <span>Cashier Signature</span>
                </div>
            </div>

            <div class="footer">
                <p>Thank you for your business!</p>
                <p style="font-size: 9px;">Sasinna.com - Solutions</p>
            </div>
        </div>

        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    </body>

</html>
