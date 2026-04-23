<!DOCTYPE html>
<html>

    <head>
        <title>Stock Inventory Report</title>
        <style>
            body {
                font-family: sans-serif;
                font-size: 12px;
            }

            .header {
                text-align: center;
                margin-bottom: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            th,
            td {
                border: 1px solid #444;
                padding: 6px;
                text-align: left;
            }

            th {
                background-color: #eee;
            }

            .text-right {
                text-align: right;
            }

            .text-center {
                text-align: center;
            }

            .total-box {
                margin-bottom: 20px;
                padding: 10px;
                border: 1px solid #000;
                font-weight: bold;
            }
        </style>
    </head>

    <body>
        <div class="header">
            <h2>Tyre Management System</h2>
            <h3>Stock Inventory Report</h3>
            <p>Generated: {{ now()->format('Y-m-d H:i') }}</p>
        </div>

        <div class="total-box">
            Total Inventory Value: Rs. {{ number_format($totalInventoryValue, 2) }}
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Vehicle Type</th>
                    <th>Model</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Cost</th>
                    <th class="text-right">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stockItems as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->vehicle_type ?? '-' }}</td>
                        <td>{{ $item->model_number }}</td>
                        <td class="text-center">{{ $item->current_stock }}</td>
                        <td class="text-right">{{ number_format($item->avg_cost, 2) }}</td>
                        <td class="text-right">{{ number_format($item->total_value, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>

</html>
