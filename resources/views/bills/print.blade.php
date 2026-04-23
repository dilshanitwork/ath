<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Print Bill - {{ $bill->bill_number }}</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #f8f9fa;
            }

            .bill-container {
                max-width: 800px;
                margin: 0 auto;
                background: #fff;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            h3,
            h4 {
                text-align: center;
                color: #333;
            }

            .bill-header {
                border-bottom: 2px solid #007bff;
                padding-bottom: 10px;
                margin-bottom: 20px;
            }

            .bill-summary {
                display: flex;
                justify-content: space-between;
                margin-bottom: 20px;
            }

            .bill-summary div {
                width: 48%;
            }

            .bill-summary p {
                margin: 5px 0;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
            }

            .table th,
            .table td {
                border: 1px solid #ddd;
                padding: 10px;
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
                margin-top: 40px;
                display: flex;
                justify-content: space-between;
            }

            .signature-block {
                text-align: center;
                width: 30%;
            }

            .signature-block span {
                display: block;
                margin-top: 60px;
                border-top: 1px solid #333;
                font-size: 14px;
            }

            .footer {
                text-align: center;
                margin-top: 20px;
                color: #777;
                font-size: 14px;
            }

            @media print {
                body {
                    background: #fff;
                }

                .bill-container {
                    box-shadow: none;
                    border: none;
                }

                .footer {
                    display: none;
                }
            }
        </style>
    </head>

    <body>
        <div class="bill-container">
            <!-- Bill Header -->
            <div class="bill-header">
                <h3>Invoice</h3>
                <h1>Tyre Management System</h1>
                <p>Contact: 0771764746 / 0778781096</p>
                <p><strong>Bill Number:</strong> {{ $bill->bill_number }}</p>
            </div>

            <!-- Combined Customer and Payment Details -->
            <div class="bill-summary">
                <div>
                    <h4>Customer Details</h4>
                    <p><strong>Name:</strong> {{ $bill->customer->name }}</p>
                    <p><strong>NIC:</strong> {{ $bill->customer->nic }}</p>
                    <p><strong>Mobile:</strong> {{ $bill->customer->mobile }}</p>
                    <p><strong>Bill Date:</strong> {{ $bill->created_at->format('Y-m-d') }}</p>
                    <p><strong>Address:</strong> {{ $bill->customer->address }}</p>
                </div>
                <div>
                    <h4>Payment Details</h4>
                    <p><strong>Total Price:</strong> Rs.{{ number_format($bill->total_price, 2) }}</p>
                    <p><strong>Advance Payment:</strong> Rs.{{ number_format($bill->advance_payment, 2) }}</p>
                    <p><strong>Balance:</strong> Rs.{{ number_format($bill->balance, 2) }}</p>
                    <p><strong>Installment Payment:</strong> Rs.{{ number_format($bill->installment_payment, 2) }}
                        <b>x</b> {{ $bill->installments }}
                    </p>
                    <p><strong>Next Bill Date:</strong> {{ $bill->next_bill }}</p>
                </div>
            </div>

            @if (!empty($bill->guarantor_name))
                <div class="bill-summary">
                    <div>
                        <h4>Guarantor Details</h4>
                        <p><strong>Name:</strong> {{ $bill->guarantor_name }}</p>
                        <p><strong>NIC:</strong> {{ $bill->guarantor_nic }}</p>
                        <p><strong>Mobile:</strong> {{ $bill->guarantor_mobile }}</p>
                    </div>
                </div>
            @endif

            <!-- Bill Items -->
            <div class="bill-items">
                <h4>Bill Items</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bill->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td class="text-center">{{ $item->item_quantity }}</td>
                                <td class="text-right">Rs.{{ number_format($item->item_price, 2) }}</td>
                                <td class="text-right">Rs.{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="4" class="text-right">Total:</td>
                            <td class="text-right">Rs.{{ number_format($bill->total_price, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Signature Section -->
            <div class="signatures">
                <div class="signature-block">
                    <span>Customer Signature</span>
                </div>
                <div class="signature-block">
                    <span>Guarantor Signature</span>
                </div>
                <div class="signature-block">
                    <span>User Signature</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>Thank you for your business!</p>
            </div>
        </div>

        <script>
            window.print();
        </script>
    </body>

</html>
