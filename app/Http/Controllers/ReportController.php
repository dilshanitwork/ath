<?php

namespace App\Http\Controllers;

use App\Models\DirectBill;
use App\Models\StockItem;
use App\Models\PurchaseOrder;
use App\Models\TyreRepair;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view reports')->only(['index', 'dailySales', 'exportDailySales', 'stockInventory', 'exportStockInventory', 'purchaseOrders', 'exportPurchaseOrders', 'tyreRepairs', 'exportTyreRepairs']);
    }

    public function index()
    {
        return view('reports.index');
    }

    // ==========================================
    // 1. DAILY SALES REPORT
    // ==========================================

    /**
     * Show Daily Sales View
     */
    // public function dailySales(Request $request)
    // {
    //     $date = $request->input('date', Carbon::today()->format('Y-m-d'));
    //     $paymentType = $request->input('type'); // 'cash' or 'credit'
    //     $userId = $request->input('user_id'); // Filter by Cashier

    //     $query = DirectBill::with('user')->whereDate('created_at', $date)->orderBy('created_at', 'desc');

    //     if ($paymentType) {
    //         $query->where('type', $paymentType);
    //     }
    //     if ($userId) {
    //         $query->where('user_id', $userId);
    //     }

    //     $bills = $query->get();

    //     // Calculate Totals for View
    //     $totalSales = $bills->sum('final_amount');
    //     $totalPaid = $bills->sum('paid');
    //     $totalBalance = $bills->sum('balance');
    //     $cashSales = $bills->where('type', 'cash')->sum('final_amount');
    //     $creditSales = $bills->where('type', 'credit')->sum('final_amount');

    //     $users = User::all();
    //     $perPage = $request->input('per_page', 10);
    //     $bills = $query->latest()->paginate($perPage);

    //     return view('reports.daily_sales', compact('bills', 'date', 'totalSales', 'totalPaid', 'totalBalance', 'cashSales', 'creditSales', 'users', 'paymentType', 'userId'));
    // }
    public function dailySales(Request $request)
    {
        // Support both single-date (date_from only) and date range (date_from + date_to)
        $dateFrom = $request->input('date_from', Carbon::today()->format('Y-m-d'));
        $dateTo = $request->input('date_to', $dateFrom); // if empty, treat as single day
        $paymentType = $request->input('type'); // 'cash' or 'credit'
        $userId = $request->input('user_id'); // Filter by Cashier

        // Base query for the range
        $query = DirectBill::with('user')->whereDate('created_at', '>=', $dateFrom)->whereDate('created_at', '<=', $dateTo)->orderBy('customer_name', 'asc')->orderBy('created_at', 'desc');

        if ($paymentType) {
            $query->where('type', $paymentType);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Use a full collection to compute totals across the whole range (not just the current page)
        $allBills = (clone $query)->get();

        // Calculate Totals for View
        $totalSales = $allBills->sum('final_amount');
        $totalPaid = $allBills->sum('paid');
        $totalBalance = $allBills->sum('balance');
        $cashSales = $allBills->where('type', 'cash')->sum('final_amount');
        $creditSales = $allBills->where('type', 'credit')->sum('final_amount');

        $users = User::all();
        $perPage = $request->input('per_page', 10);
        $bills = $query->latest()->paginate($perPage);

        // Keep 'date' for backward compatibility with views (set to dateFrom)
        $date = $dateFrom;

        return view('reports.daily_sales', compact('bills', 'date', 'dateFrom', 'dateTo', 'totalSales', 'totalPaid', 'totalBalance', 'cashSales', 'creditSales', 'users', 'paymentType', 'userId'));
    }
    /**
     * Export Daily Sales to CSV
     */
    // public function exportDailySales(Request $request)
    // {
    //     $date = $request->input('date', Carbon::today()->format('Y-m-d'));
    //     $paymentType = $request->input('type');
    //     $userId = $request->input('user_id');

    //     $query = DirectBill::with('user')->whereDate('created_at', $date)->orderBy('created_at', 'desc');

    //     if ($paymentType) {
    //         $query->where('type', $paymentType);
    //     }
    //     if ($userId) {
    //         $query->where('user_id', $userId);
    //     }

    //     $bills = $query->get();

    //     $response = new StreamedResponse(function () use ($bills) {
    //         $handle = fopen('php://output', 'w');

    //         // CSV Header
    //         fputcsv($handle, ['Bill Number', 'Date', 'Customer', 'Type', 'Amount', 'Paid', 'Balance', 'Cashier']);

    //         // CSV Rows
    //         foreach ($bills as $bill) {
    //             fputcsv($handle, [$bill->bill_number, $bill->created_at->format('Y-m-d H:i'), $bill->customer_name, ucfirst($bill->type), number_format($bill->final_amount, 2), number_format($bill->paid, 2), number_format($bill->balance, 2), $bill->user->name ?? 'System']);
    //         }
    //         fclose($handle);
    //     });

    //     $fileName = 'Daily_Sales_' . $date . '.csv';
    //     $response->headers->set('Content-Type', 'text/csv');
    //     $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

    //     return $response;
    // }
    public function exportDailySales(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::today()->format('Y-m-d'));
        $dateTo = $request->input('date_to', $dateFrom);
        $paymentType = $request->input('type');
        $userId = $request->input('user_id');

        $query = DirectBill::with('user')->whereDate('created_at', '>=', $dateFrom)->whereDate('created_at', '<=', $dateTo)->where('balance', '!=', 0)->orderBy('customer_name', 'asc')->orderBy('created_at', 'desc');

        if ($paymentType) {
            $query->where('type', $paymentType);
        }
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $bills = $query->get();

        $response = new StreamedResponse(function () use ($bills, $dateFrom, $dateTo) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            fputcsv($handle, ['Bill', 'Customer', 'Amount', 'Paid', 'Balance', 'Date']);

            // CSV Rows
            foreach ($bills as $bill) {
                fputcsv($handle, [
                    $bill->bill_number,
                    $bill->customer_name,
                    //ucfirst($bill->type),
                    number_format($bill->final_amount, 2),
                    number_format($bill->paid, 2),
                    number_format($bill->balance, 2),
                    $bill->created_at->format('Y-m-d'),
                    // $bill->user->name ?? 'System'
                ]);
            }
            fclose($handle);
        });

        $fileName = 'Daily_Sales_' . $dateFrom . ($dateFrom !== $dateTo ? '_to_' . $dateTo : '') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
    // ==========================================
    // 2. STOCK INVENTORY REPORT
    // ==========================================

    /**
     * Show Stock Inventory View
     */
    public function stockInventory(Request $request)
    {
        $supplierId = $request->input('supplier_id');
        $stockStatus = $request->input('stock_status');
        $vehicleType = $request->input('vehicle_type');
        $search = $request->input('search');

        $vehicleTypes = ['Truck', 'Light Truck', 'PCR', 'Motorcycle', '3 Wheeler', 'Tube', 'Battery', 'Other'];

        $query = StockItem::with([
            'supplier',
            'stockBatches' => function ($q) {
                $q->where('quantity', '>', 0);
            },
        ]);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
        if ($vehicleType) {
            $query->where('vehicle_type', $vehicleType);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('model_number', 'like', "%{$search}%");
            });
        }

        // More robust way: Use withSum always for quantity
        $query->withSum(
            [
                'stockBatches as current_stock' => function ($q) {
                    $q->where('quantity', '>', 0);
                },
            ],
            'quantity',
        );

        if ($stockStatus) {
            if ($stockStatus === 'out') {
                $query->having('current_stock', '=', 0)->orHavingNull('current_stock');
            } elseif ($stockStatus === 'low') {
                $query->having('current_stock', '>', 0)->having('current_stock', '<', 5);
            } elseif ($stockStatus === 'in') {
                $query->having('current_stock', '>=', 5);
            }
        }

        // Calculate total valuation from ALL matching items (not just current page)
        $totalInventoryValue = (clone $query)->get()->sum('total_value');

        if ($request->input('export') === 'pdf') {
            $stockItems = $query->get()->sortByDesc('total_value');
            $pdf = Pdf::loadView('reports.print.stock_inventory', compact('stockItems', 'totalInventoryValue'))->setPaper('a4', 'portrait');
            return $pdf->download('Stock_Inventory.pdf');
        }

        $suppliers = Supplier::orderBy('name')->get();
        $perPage = $request->input('per_page', 10);

        // Final paginated results
        $stockItems = $query->orderBy('name')->paginate($perPage);

        return view('reports.stock_inventory', compact('stockItems', 'totalInventoryValue', 'suppliers', 'vehicleTypes'));
    }

    /**
     * Export Stock Inventory to CSV
     */
    public function exportStockInventory(Request $request)
    {
        $supplierId = $request->input('supplier_id');
        $stockStatus = $request->input('stock_status');
        $vehicleType = $request->input('vehicle_type');
        $search = $request->input('search');

        $query = StockItem::with([
            'supplier',
            'stockBatches' => function ($q) {
                $q->where('quantity', '>', 0);
            },
        ]);

        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
        if ($vehicleType) {
            $query->where('vehicle_type', $vehicleType);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('model_number', 'like', "%{$search}%");
            });
        }

        $query->withSum(
            [
                'stockBatches as current_stock' => function ($q) {
                    $q->where('quantity', '>', 0);
                },
            ],
            'quantity',
        );

        if ($stockStatus) {
            if ($stockStatus === 'out') {
                $query->having('current_stock', '=', 0)->orHavingNull('current_stock');
            } elseif ($stockStatus === 'low') {
                $query->having('current_stock', '>', 0)->having('current_stock', '<', 5);
            } elseif ($stockStatus === 'in') {
                $query->having('current_stock', '>=', 5);
            }
        }

        $stockItems = $query->get()->sortByDesc('total_value');

        $response = new StreamedResponse(function () use ($stockItems) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Item Name', 'Vehicle Type', 'Model/Pattern', 'Supplier', 'Current Stock', 'Status', 'Est. Unit Cost', 'Total Value']);

            foreach ($stockItems as $item) {
                $statusText = 'In Stock';
                if ($item->current_stock == 0) {
                    $statusText = 'Out of Stock';
                } elseif ($item->current_stock < 5) {
                    $statusText = 'Low Stock';
                }

                fputcsv($handle, [$item->name, $item->vehicle_type ?? '-', $item->model_number ?? '-', $item->supplier->name ?? 'N/A', $item->current_stock, $statusText, number_format($item->avg_cost, 2), number_format($item->total_value, 2)]);
            }
            fclose($handle);
        });

        $fileName = 'stock_inventory_' . now()->format('Y-m-d_H:i:s') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }

    // ==========================================
    // 3. PURCHASE ORDER REPORT
    // ==========================================

    /**
     * Show Purchase Order View
     */
    public function purchaseOrders(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $status = $request->input('status');
        $supplierId = $request->input('supplier_id');

        $query = PurchaseOrder::with('supplier')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->orderBy('order_date', 'desc');

        if ($status) {
            $query->where('status', $status);
        }
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $purchaseOrders = $query->get();

        $totalPurchases = $purchaseOrders->sum('total_amount');
        $receivedCount = $purchaseOrders->where('status', 'received')->count();
        $pendingCount = $purchaseOrders->where('status', 'pending')->count();
        $suppliers = Supplier::orderBy('name')->get();

        $perPage = $request->input('per_page', 10);
        $purchaseOrders = $query->latest()->paginate($perPage);
        return view('reports.purchase_orders', compact('purchaseOrders', 'startDate', 'endDate', 'totalPurchases', 'receivedCount', 'pendingCount', 'suppliers', 'status', 'supplierId'));
    }

    /**
     * Export Purchase Orders to CSV
     */
    public function exportPurchaseOrders(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $status = $request->input('status');
        $supplierId = $request->input('supplier_id');

        $query = PurchaseOrder::with('supplier')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->orderBy('order_date', 'desc');

        if ($status) {
            $query->where('status', $status);
        }
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }

        $purchaseOrders = $query->get();

        $response = new StreamedResponse(function () use ($purchaseOrders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['PO Number', 'Date', 'Supplier', 'Status', 'Total Amount', 'Items Count']);

            foreach ($purchaseOrders as $po) {
                fputcsv($handle, [$po->po_number, $po->order_date, $po->supplier->name ?? 'Unknown', ucfirst($po->status), number_format($po->total_amount, 2), $po->items->count()]);
            }
            fclose($handle);
        });

        $fileName = 'Purchase_Orders_' . now()->format('Y-m-d') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }

    // ==========================================
    // 4. TYRE REPAIR REPORT
    // ==========================================

    /**
     * Show Tyre Repair View
     */
    public function tyreRepairs(Request $request)
    {
        $status = $request->input('status');
        $customerId = $request->input('customer_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = TyreRepair::with('customer');

        if ($startDate) {
            $query->whereDate('received_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('received_date', '<=', $endDate);
        }
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($status) {
            if ($status === 'pending') {
                $query->whereNull('sent_date');
            } elseif ($status === 'sent') {
                $query->whereNotNull('sent_date')->whereNull('received_from_company_date');
            } elseif ($status === 'received') {
                $query->whereNotNull('received_from_company_date')->whereNull('issued_date');
            } elseif ($status === 'completed') {
                $query->whereNotNull('issued_date');
            }
        }

        $repairs = $query->orderBy('item_number', 'desc')->get();

        $stats = [
            'pending' => TyreRepair::whereNull('sent_date')->count(),
            'sent' => TyreRepair::whereNotNull('sent_date')->whereNull('received_from_company_date')->count(),
            'received' => TyreRepair::whereNotNull('received_from_company_date')->whereNull('issued_date')->count(),
            'completed' => TyreRepair::whereNotNull('issued_date')->count(),
        ];

        $customers = Customer::select('id', 'name')->orderBy('name')->get();
        $perPage = $request->input('per_page', 10);
        $repairs = $query->latest()->paginate($perPage);
        return view('reports.tyre_repairs', compact('repairs', 'stats', 'status', 'customers', 'startDate', 'endDate', 'customerId'));
    }

    /**
     * Export Tyre Repairs to CSV
     */
    public function exportTyreRepairs(Request $request)
    {
        $status = $request->input('status');
        $customerId = $request->input('customer_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = TyreRepair::with('customer');

        if ($startDate) {
            $query->whereDate('received_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('received_date', '<=', $endDate);
        }
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        if ($status) {
            if ($status === 'pending') {
                $query->whereNull('sent_date');
            } elseif ($status === 'sent') {
                $query->whereNotNull('sent_date')->whereNull('received_from_company_date');
            } elseif ($status === 'received') {
                $query->whereNotNull('received_from_company_date')->whereNull('issued_date');
            } elseif ($status === 'completed') {
                $query->whereNotNull('issued_date');
            }
        }

        $repairs = $query->orderBy('item_number', 'desc')->get();

        $response = new StreamedResponse(function () use ($repairs) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Item #', 'Job #', 'Customer', 'Mobile', 'Tyre Size', 'Tyre Make', 'Status', 'Amount']);

            foreach ($repairs as $repair) {
                $statusText = 'Pending';
                if ($repair->issued_date) {
                    $statusText = 'Completed';
                } elseif ($repair->received_from_company_date) {
                    $statusText = 'Received (Ready)';
                } elseif ($repair->sent_date) {
                    $statusText = 'Sent to Company';
                }

                fputcsv($handle, [$repair->item_number, $repair->job_number, $repair->customer->name ?? '-', $repair->customer->mobile ?? '-', $repair->tyre_size, $repair->tyre_make, $statusText, number_format($repair->amount, 2)]);
            }
            fclose($handle);
        });

        $fileName = 'Tyre_Repairs_' . now()->format('Y-m-d') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
    public function creditSummary(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::today()->format('Y-m-d'));
        $dateTo = $request->input('date_to', $dateFrom);
        $userId = $request->input('user_id');
        $status = $request->input('status'); // NEW
        $perPage = (int) $request->input('per_page', 10);

        $query = DirectBill::with('user')->where('type', 'credit')->whereDate('created_at', '>=', $dateFrom)->whereDate('created_at', '<=', $dateTo)->orderBy('created_at', 'desc');

        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($status) {
            // NEW: filter by status
            $query->where('status', $status);
        }

        $allBills = (clone $query)->get();
        $totalFinal = $allBills->sum('final_amount');
        $totalPaid = $allBills->sum('paid');
        $totalBalance = $allBills->sum('balance');

        $users = User::all();
        $bills = $query->paginate($perPage)->withQueryString();

        // pass status to view
        return view('reports.credit_summary', compact('bills', 'dateFrom', 'dateTo', 'totalFinal', 'totalPaid', 'totalBalance', 'users', 'status'));
    }

    public function exportCreditSummary(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::today()->format('Y-m-d'));
        $dateTo = $request->input('date_to', $dateFrom);
        $userId = $request->input('user_id');
        $status = $request->input('status'); // NEW

        $query = DirectBill::with('user')->where('type', 'credit')->whereDate('created_at', '>=', $dateFrom)->whereDate('created_at', '<=', $dateTo)->orderBy('created_at', 'desc');

        if ($userId) {
            $query->where('user_id', $userId);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $bills = $query->get();

        $response = new StreamedResponse(function () use ($bills) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Bill Number', 'Customer', 'Final Amount', 'Paid', 'Balance', 'Status', 'Cashier']); // added Status
            foreach ($bills as $b) {
                fputcsv($handle, [
                    $b->created_at->format('Y-m-d H:i'),
                    $b->bill_number,
                    $b->customer_name,
                    number_format($b->final_amount, 2),
                    number_format($b->paid, 2),
                    number_format($b->balance, 2),
                    $b->status, // added
                    $b->user->name ?? 'System',
                ]);
            }
            fclose($handle);
        });

        $fileName = 'Credit_Summary_' . $dateFrom . ($dateFrom !== $dateTo ? '_to_' . $dateTo : '') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
    
    public function customerCreditReport(Request $request)
{
    $dateFrom     = $request->input('date_from');
    $dateTo       = $request->input('date_to', $dateFrom);
    $status       = $request->input('status');
    $customerName = $request->input('customer_name');
    $perPage      = (int) $request->input('per_page', 10);
    $reportDate   = now(); // single consistent timestamp for the whole report

    $query = DirectBill::with('user')
        ->where('type', 'credit')
        ->orderBy('created_at', 'desc');

    if ($dateFrom) {
        $query->whereDate('created_at', '>=', $dateFrom);
    }
    if ($dateTo) {
        $query->whereDate('created_at', '<=', $dateTo);
    }
    if ($status) {
        $query->where('status', $status);
    }
    if ($customerName) {
        $query->where('customer_name', 'like', "%{$customerName}%");
    }
    
        
    $customerContact = $customerName ? (clone $query)->value('contact_number') : null;

    $allBills     = (clone $query)->get();
    $totalFinal   = $allBills->sum('final_amount');
    $totalPaid    = $allBills->sum('paid');
    $totalBalance = $allBills->sum('balance');

    // ── PDF export ──────────────────────────────────────────────────
    if ($request->input('export') === 'pdf') {
        $pdf = Pdf::loadView('reports.print.customer_credit_report', [
            'bills'        => $allBills,
            'totalFinal'   => $totalFinal,
            'totalPaid'    => $totalPaid,
            'totalBalance' => $totalBalance,
            'dateFrom'     => $dateFrom,
            'dateTo'       => $dateTo,
            'status'       => $status,
            'customerName' => $customerName,
            'customerContact' => $customerContact,
            'reportDate'   => $reportDate, // ← added
        ])->setPaper('a4', 'landscape');

        $fileName = 'Customer_Credit_Report_' . $reportDate->format('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }
    // ────────────────────────────────────────────────────────────────

    $bills = $query->paginate($perPage)->withQueryString();

    return view('reports.customer_credit_report', compact(
        'bills',
        'dateFrom',
        'dateTo',
        'totalFinal',
        'totalPaid',
        'totalBalance',
        'status',
        'customerName',
        'customerContact',
        'reportDate' // ← added
    ));
}

    public function exportCustomerCreditReport(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to', $dateFrom);
        $status = $request->input('status');
        $customerName = $request->input('customer_name');

        $query = DirectBill::with('user')->where('type', 'credit')->orderBy('created_at', 'desc');

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        if ($status) {
            $query->where('status', $status);
        }
        if ($customerName) {
            $query->where('customer_name', 'like', "%{$customerName}%");
        }

        $bills = $query->get();

        $response = new StreamedResponse(function () use ($bills) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Bill Number', 'Customer', 'Final Amount', 'Paid', 'Balance', 'Status', 'Cashier']);
            foreach ($bills as $b) {
                fputcsv($handle, [$b->created_at->format('Y-m-d H:i'), $b->bill_number, $b->customer_name, number_format($b->final_amount, 2), number_format($b->paid, 2), number_format($b->balance, 2), $b->status, $b->user->name ?? 'System']);
            }
            fclose($handle);
        });

        $fileName = 'Customer_Credit_Report_' . now()->format('Y-m-d_H:i') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
}
