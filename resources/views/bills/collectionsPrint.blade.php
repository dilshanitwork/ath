<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payment Receipt</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #f8f9fa;
                color: #333;
            }

            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background: #fff;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .header {
                text-align: center;
                margin-bottom: 20px;
            }

            .header h1 {
                font-size: 24px;
                margin: 0;
                color: #007bff;
            }

            .header p {
                margin: 5px 0;
                font-size: 14px;
                color: #555;
            }

            .details {
                margin-bottom: 20px;
                font-size: 16px;
            }

            .details p {
                margin: 8px 0;
            }

            .details strong {
                color: #333;
            }

            .signatures {
                margin-top: 40px;
                display: flex;
                justify-content: space-between;
            }

            .signature-block {
                text-align: center;
                width: 45%;
            }

            .signature-block span {
                display: block;
                margin-top: 60px;
                border-top: 1px solid #333;
                font-size: 14px;
                color: #555;
            }

            @media print {
                body {
                    background-color: #fff;
                }

                .container {
                    box-shadow: none;
                    border: none;
                }
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="header">
                <h1>Tyre Management System</h1>
                <p>Contact: 0771764746 / 0778781096</p>
            </div>

            <div class="details">
                <p><strong>Date:</strong> {{ now()->format('Y-m-d') }}</p>
                <p><strong>Bill Number:</strong> {{ $bill->bill_number }}</p>
                <p><strong>Customer Name:</strong> {{ $bill->customer->name }}</p>
                <p><strong>Paid Amount:</strong> Rs.{{ number_format($lastCollection->payment, 2) }}</p>
            </div>

            <div class="signatures">
                <div class="signature-block">
                    <span>Customer Signature</span>
                </div>
                <div class="signature-block">
                    <span>User Signature</span>
                </div>
            </div>
        </div>

        <script>
            window.print();
        </script>

    </body>

</html>
