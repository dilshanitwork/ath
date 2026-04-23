<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title>Customer Bill Report</title>
        <style>
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 11px;
                color: #1a1a1a;
            }

            /* ── Page Header ── */
            .page-header {
                text-align: center;
                margin-bottom: 14px;
                border-bottom: 2px solid #1a56db;
                padding-bottom: 10px;
            }

            .page-header h2 {
                font-size: 16px;
                font-weight: bold;
                color: #1a56db;
                margin-bottom: 2px;
            }

            .page-header h3 {
                font-size: 13px;
                font-weight: normal;
                color: #333;
                margin-bottom: 4px;
            }

            .page-header p {
                font-size: 10px;
                color: #666;
            }

            /* ── Active Filters Strip ── */
            .filters-bar {
                background: #f3f4f6;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                padding: 6px 10px;
                margin-bottom: 12px;
                font-size: 10px;
                color: #374151;
            }

            .filters-bar strong {
                margin-right: 6px;
            }

            .filter-tag {
                display: inline-block;
                background: #dbeafe;
                color: #1e40af;
                border-radius: 3px;
                padding: 1px 6px;
                margin-right: 5px;
            }

            /* ── Summary Cards ── */
            .summary-row {
                width: 80%;
                margin: 0 auto 10px auto;
                border-collapse: separate;
                border-spacing: 4px 0;
            }

            .summary-row td {
                width: 25%;
                border: 1px solid #e5e7eb;
                border-radius: 3px;
                padding: 4px 6px;
                vertical-align: top;
            }

            .summary-label {
                font-size: 9px;
                text-transform: uppercase;
                color: #6b7280;
                font-weight: bold;
                margin-bottom: 3px;
            }

            .summary-value {
                font-size: 14px;
                font-weight: bold;
            }

            .summary-value.blue  { color: #1a56db; }
            .summary-value.green { color: #16a34a; }
            .summary-value.red   { color: #dc2626; }
            .summary-value.dark  { color: #111827; }

            /* ── Data Table ── */
            table.data-table {
                width: 80%;
                border-collapse: collapse;
                margin-top: 4px;
                margin-left: auto;
                margin-right: auto;
            }

            table.data-table thead tr {
                background-color: #1e3a5f;
                color: #ffffff;
            }

            table.data-table thead th {
                padding: 4px 5px;
                font-size: 9px;
                text-transform: uppercase;
                letter-spacing: 0.3px;
                border: none;
                white-space: nowrap;
            }

            .col-date { width: 12% !important; }
            .col-bill { width: 6% !important; }
            .col-customer { width: 12% !important; }
            .col-contact { width: 8% !important; }
            .col-status { width: 3% !important; }
            .col-total { width: 9% !important; }
            .col-paid { width: 9% !important; }
            .col-balance { width: 9% !important; }
            .col-days { width: 5% !important; }

            .cell-balance-negative { color: #dc2626; font-weight: bold; }

            table.data-table tbody tr:nth-child(even) { background-color: #f9fafb; }
            table.data-table tbody tr:nth-child(odd)  { background-color: #ffffff; }

            table.data-table tbody td {
                padding: 2px 3px;
                border-bottom: 1px solid #e5e7eb;
                vertical-align: middle;
                white-space: nowrap;
                font-size: 15px !important;
                line-height: 1.2 !important;
            }

            .text-right  { text-align: right; }
            .text-center { text-align: center; }
            .text-left   { text-align: left; }

            /* Status badges */
            .badge {
                display: inline-block;
                padding: 1px 4px;
                border-radius: 2px;
                font-size: 8px;
                font-weight: bold;
                text-transform: uppercase;
                white-space: nowrap;
            }

            .badge-success { background: #d1fae5; color: #065f46; }
            .badge-warning { background: #fef3c7; color: #92400e; }
            .badge-danger  { background: #fee2e2; color: #991b1b; }

            /* Days badge */
            .badge-days {
                display: inline-block;
                padding: 1px 4px;
                border-radius: 2px;
                font-size: 8px;
                font-weight: bold;
                background: #ede9fe;
                color: #5b21b6;
                white-space: nowrap;
            }

            /* Totals footer row */
            .totals-row td {
                background-color: #1e3a5f;
                color: #ffffff;
                font-weight: bold;
                padding: 4px 5px;
                border: none;
                white-space: nowrap;
            }

            /* Page footer */
            .page-footer {
                margin-top: 16px;
                font-size: 9px;
                color: #9ca3af;
                text-align: center;
                border-top: 1px solid #e5e7eb;
                padding-top: 6px;
            }
        </style>
    </head>

    <body>

        {{-- ── Page Header ── --}}
        <div class="page-header">
            <h2>Tyre Management System</h2>
            <h3>Customer Bill Report</h3>
            <p>Generated: {{ $reportDate->format('Y-m-d H:i') }}</p>
        </div>

        {{-- ── Active Filters Strip ── --}}
        @if ($dateFrom || $dateTo || $customerName || $status)
            <div class="filters-bar">
                <strong>Selected:</strong>

                @if ($customerName)
                    <span class="filter-tag">Customer: {{ $customerName }} : {{ $customerContact }}</span>
                @endif

                @if ($dateFrom)
                    <span class="filter-tag">
                        From: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}
                    </span>
                @endif

                @if ($dateTo && $dateTo !== $dateFrom)
                    <span class="filter-tag">
                        To: {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                    </span>
                @endif

                @if ($status)
                    <span class="filter-tag">Status: {{ ucfirst($status) }}</span>
                @endif
            </div>
        @endif

        {{-- ── Summary Cards ── --}}
        <table class="summary-row">
            <tr>
                <td>
                    <div class="summary-label">Total Amount</div>
                    <div class="summary-value blue">Rs. {{ number_format($totalFinal, 2) }}</div>
                </td>
                <td>
                    <div class="summary-label">Total Paid</div>
                    <div class="summary-value green">Rs. {{ number_format($totalPaid, 2) }}</div>
                </td>
                <td>
                    <div class="summary-label">Total Balance</div>
                    <div class="summary-value red">Rs. {{ number_format($totalBalance, 2) }}</div>
                </td>
                <td>
                    <div class="summary-label">Bills</div>
                    <div class="summary-value dark">{{ $bills->count() }}</div>
                </td>
            </tr>
        </table>

        {{-- ── Pre-compute column visibility ── --}}
        @php
            $showCustomer = empty($customerName);
            $showStatus   = empty($status);

            // Count total visible columns for colspan calculations
            $totalCols = 3; // Date, Bill#, Days always visible
            $totalCols += 3; // Total, Paid, Balance always visible
            if ($showCustomer) $totalCols += 2; // Customer + Contact
            if ($showStatus)   $totalCols += 1; // Status
        @endphp

        {{-- ── Data Table ── --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-left col-date">Date</th>
                    <th class="text-left col-bill">Bill #</th>
                    @if ($showCustomer)
                        <th class="text-left col-customer">Customer</th>
                        <th class="text-left col-contact">Contact</th>
                    @endif
                    @if ($showStatus)
                        <th class="text-center col-status">Status</th>
                    @endif
                    <th class="text-right col-total">Total (Rs.)</th>
                    <th class="text-right col-paid">Paid (Rs.)</th>
                    <th class="text-right col-balance">Balance (Rs.)</th>
                    <th class="text-center col-days">Days</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bills as $bill)
                    @php
                        $s    = $bill->status ?? ($bill->balance > 0 ? 'open' : 'closed');
                        $days = (int) $bill->created_at->diffInDays($reportDate);
                    @endphp
                    <tr>
                        <td class="cell-date col-date">{{ $bill->created_at->format('Y-m-d H:i') }}</td>
                        <td class="cell-bill col-bill"><strong>{{ $bill->bill_number }}</strong></td>
                        @if ($showCustomer)
                            <td class="cell-customer col-customer">{{ $bill->customer_name }}</td>
                            <td class="cell-contact col-contact">{{ $bill->contact_number ?? '-' }}</td>
                        @endif
                        @if ($showStatus)
                            <td class="cell-status text-center col-status">
                                @if ($s === 'closed')
                                    <span class="badge badge-success">Closed</span>
                                @elseif ($s === 'partial')
                                    <span class="badge badge-warning">Partial</span>
                                @else
                                    <span class="badge badge-danger">Open</span>
                                @endif
                            </td>
                        @endif
                        <td class="cell-total text-right col-total">{{ number_format($bill->final_amount, 2) }}</td>
                        <td class="cell-paid text-right col-paid">{{ number_format($bill->paid, 2) }}</td>
                        <td class="cell-balance text-right col-balance {{ $bill->balance > 0 ? 'cell-balance-negative' : '' }}">{{ number_format($bill->balance, 2) }}</td>
                        <td class="cell-days text-center col-days">
                            <span class="badge-days">{{ $days }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $totalCols }}" class="text-center"
                            style="padding: 20px; color: #9ca3af;">
                            No records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            {{-- ── Totals Footer Row ── --}}
            @if ($bills->count() > 0)
                @php
                    // Colspan for label cell = all cols before Total
                    $labelCols = 2; // Date + Bill#
                    if ($showCustomer) $labelCols += 2;
                    if ($showStatus)   $labelCols += 1;
                @endphp
                <tfoot>
                    <tr class="totals-row">
                        <td colspan="{{ $labelCols }}" class="text-right" style="padding: 3px 4px !important">TOTALS</td>
                        <td class="text-right" style="padding: 3px 4px !important">{{ number_format($totalFinal, 2) }}</td>
                        <td class="text-right" style="padding: 3px 4px !important">{{ number_format($totalPaid, 2) }}</td>
                        <td class="text-right" style="padding: 3px 4px !important">{{ number_format($totalBalance, 2) }}</td>
                        <td style="padding: 3px 4px !important"></td>{{-- Days column --}}
                    </tr>
                </tfoot>
            @endif
        </table>

        {{-- ── Page Footer ── --}}
        <div class="page-footer">
            Tyre Management System &mdash; Customer Bill Report &mdash;
            &mdash; Total {{ $bills->count() }} record(s)
        </div>

    </body>

</html>