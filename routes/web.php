<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeFileController;
use App\Models\Contributor;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeValueController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\DirectBillController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\BillPaymentController;
use App\Http\Controllers\TyreRepairController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ChequeController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::resource('permissions', PermissionController::class);

    Route::resource('roles', RoleController::class);

    Route::resource('users', UserController::class);
    Route::get('users/{user}/password', [UserController::class, 'editPassword'])->name('users.edit-password');
    Route::put('users/{user}/password', [UserController::class, 'updatePassword'])->name('users.update-password');

    // Attribute Routes
    Route::resource('attributes', AttributeController::class);

    // AttributeValue Routes
    Route::resource('attribute-values', AttributeValueController::class);

    // Bill Routes

    Route::get('/bills/batch-info', [BillController::class, 'getBatchInfo'])
        ->name('bills.batch_info')
        ->middleware('can:view bills');

    Route::get('/bills/payment/{bill?}', [BillController::class, 'paymentPage'])->name('bills.paymentPage');
    Route::post('/bills/payment', [BillController::class, 'processPayment'])->name('bills.processPayment');

    Route::resource('bills', BillController::class);
    Route::post('bills/{bill}/payments', [BillController::class, 'addPayment'])->name('bills.addPayment');

    Route::post('bills/{bill}/add-collection', [BillController::class, 'addCollection'])->name('bills.addCollection');
    Route::get('/bills/{bill}/print', [BillController::class, 'print'])->name('bills.print');
    Route::get('/bills/{bill}/collection/print', [BillController::class, 'printCollection'])->name('bills.printCollection');
    Route::post('/collections/{collection}/edit', [BillController::class, 'editCollection'])->name('collections.edit');
    Route::put('/collections/{collection}', [BillController::class, 'updateCollection'])->name('collections.update');

    Route::delete('/collections/{collection}', [BillController::class, 'destroyCollection'])->name('collections.destroy');
    Route::post('/collections/cancel-edit', [BillController::class, 'cancelEdit'])->name('collections.cancelEdit');

    // Route::get('/reports/collections/export', [ReportController::class, 'exportCollections'])->name('reports.exportCollections');
    // Route::get('/reports/collections/print', [ReportController::class, 'printCollections'])->name('reports.printCollections');
    // Route::get('/reports/collections', [ReportController::class, 'showCollectionReport'])->name('reports.collections');
    // Route::get('/reports/collected/export', [ReportController::class, 'exportCollected'])->name('reports.exportCollected');
    // Route::get('/reports/collected/print', [ReportController::class, 'printCollected'])->name('reports.printCollected');
    // Route::get('/reports/collected', [ReportController::class, 'showCollectedReport'])->name('reports.collected');
    // Route::get('/reports/overdue/export', [ReportController::class, 'exportOverdue'])->name('reports.exportOverdue');
    // Route::get('/reports/overdue/print', [ReportController::class, 'printOverdue'])->name('reports.printOverdue');
    // Route::get('/reports/overdue', [ReportController::class, 'showOverdueReport'])->name('reports.overdue');
    // Route::get('/reports/financial-summary', [ReportController::class, 'showFinancialSummary'])->name('reports.financialSummary');
    // Route::get('/reports/financial-list', [ReportController::class, 'showFinancialList'])->name('reports.financialList');
    // Route::get('/reports/financial-list/export', [ReportController::class, 'exportFinancialList'])->name('reports.exportFinancialList');
    // Route::get('/reports/financial-list/print', [ReportController::class, 'printFinancialList'])->name('reports.printFinancialList');
    // Route::get('/reports/all-bills', [ReportController::class, 'allBills'])->name('reports.allBills');
    // Route::get('/reports/all-bills/export', [ReportController::class, 'allBillsExport'])->name('reports.allBillsExport');
    // Route::get('/reports/closed-bills', [ReportController::class, 'closedBills'])->name('reports.closedBills');
    // Route::get('/reports/closed-bills/export', [ReportController::class, 'closedBillsExport'])->name('reports.closedBillsExport');

    Route::get('/reports/stock-inventory/export', [ReportController::class, 'exportStockInventory'])->name('reports.stock_inventory.export');
    Route::get('/reports/daily-sales/export', [ReportController::class, 'exportDailySales'])->name('reports.daily_sales.export');
    Route::get('/reports/purchase-orders/export', [ReportController::class, 'exportPurchaseOrders'])->name('reports.purchase_orders.export');
    Route::get('/reports/tyre-repairs/export', [ReportController::class, 'exportTyreRepairs'])->name('reports.tyre_repairs.export');
    Route::get('/reports/daily-sales/print', [ReportController::class, 'printDailySales'])->name('reports.daily_sales.print');
    Route::get('/reports/stock-inventory/print', [ReportController::class, 'printStockInventory'])->name('reports.stock_inventory.print');
    Route::get('/reports/purchase-orders/print', [ReportController::class, 'printPurchaseOrders'])->name('reports.purchase_orders.print');
    Route::get('/reports/tyre-repairs/print', [ReportController::class, 'printTyreRepairs'])->name('reports.tyre_repairs.print');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/daily-sales', [ReportController::class, 'dailySales'])->name('reports.daily_sales');
    Route::get('/reports/stock-inventory', [ReportController::class, 'stockInventory'])->name('reports.stock_inventory');
    Route::get('/reports/purchase-orders', [ReportController::class, 'purchaseOrders'])->name('reports.purchase_orders');
    Route::get('/reports/tyre-repairs', [ReportController::class, 'tyreRepairs'])->name('reports.tyre_repairs');
    // Route::get('/send-sms', [SmsController::class, 'send']);

    Route::resource('employees', EmployeeController::class);

    Route::get('/customers/suggestions', [CustomerController::class, 'suggestions'])->name('customers.suggestions');
    Route::resource('customers', CustomerController::class);

    // Stock Items Routes
    Route::get('stock-items/autocomplete', [StockItemController::class, 'autocomplete'])->name('stock-items.autocomplete');

    Route::resource('stock_items', StockItemController::class);

    Route::get('/stock-adjustments/create', [StockAdjustmentController::class, 'create'])->name('stock_adjustments.create');
    Route::post('/stock-adjustments', [StockAdjustmentController::class, 'store'])->name('stock_adjustments.store');
    Route::get('stock_adjustments/{stockBatch}/edit', [StockAdjustmentController::class, 'edit'])->name('stock_adjustments.edit');
    Route::put('stock_adjustments/{stockBatch}', [StockAdjustmentController::class, 'update'])->name('stock_adjustments.update');
    Route::delete('stock_adjustments/{stockBatch}', [StockAdjustmentController::class, 'destroy'])->name('stock_adjustments.destroy');

    // Suppliers Routes
    Route::resource('suppliers', SupplierController::class);

    // Purchase Orders Routes
    Route::get('/purchase-orders/{purchaseOrder}/print', [PurchaseOrderController::class, 'print'])->name('purchase_orders.print');
    Route::get('/purchase-orders/{purchaseOrder}/download', [PurchaseOrderController::class, 'download'])->name('purchase_orders.download');
    Route::get('purchase_orders/search-items', [PurchaseOrderController::class, 'searchItems'])->name('purchase_orders.search_items');
    Route::resource('purchase_orders', PurchaseOrderController::class);

    // Direct Bills Routes
    Route::get('/direct-bills/search-repair-jobs', [DirectBillController::class, 'searchRepairJobs'])->name('direct_bills.search_repair_jobs');
    Route::get('direct_bills/{directBill}/print', [DirectBillController::class, 'print'])->name('direct_bills.print');
    Route::resource('direct_bills', DirectBillController::class);

    Route::get('/direct-bills/batch-info', [DirectBillController::class, 'getBatchInfo'])->name('direct_bills.batch_info');

    // Bill Payments Routes
    Route::post('/direct-bills/{directBill}/payments', [BillPaymentController::class, 'store'])->name('direct_bills.payments.store');

    // Tyre Repairs Routes
    Route::get('tyre_repairs/create-multiple', [TyreRepairController::class, 'createMultiple'])->name('tyre_repairs.create_multiple');
    Route::post('tyre_repairs/store-multiple', [TyreRepairController::class, 'storeMultiple'])->name('tyre_repairs.store_multiple');
    Route::resource('tyre_repairs', TyreRepairController::class);

    // Companies Routes
    Route::resource('cheques', ChequeController::class);

    // Complaints Routes
    Route::resource('companies', CompanyController::class);

    // Complaints Routes
    Route::resource('complaints', ComplaintController::class);

    // Route for deleting individual employee files
    Route::delete('employee_files/{file}', [EmployeeFileController::class, 'destroy'])->name('employee_files.destroy');

    Route::get('/logs', [App\Http\Controllers\HomeController::class, 'showLogs'])->name('logs.index');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
     Route::get('stock_items/{stock_item}/customer-purchases', [StockItemController::class, 'customerPurchases'])
    ->name('stock_items.customer_purchases');
    // Route For Credit Summery
     Route::get('reports/credit-summary', [ReportController::class, 'creditSummary'])->name('reports.credit_summary');
    Route::get('reports/credit-summary/export', [ReportController::class, 'exportCreditSummary'])->name('reports.credit_summary.export');
    Route::get('/direct-bills/get-batch-info', [DirectBillController::class, 'getBatchInfo'])
    ->name('direct_bills.get_batch_info');
    // Route For Customer Credit Report
    Route::get('reports/customer-credit-report', [ReportController::class, 'customerCreditReport'])->name('reports.customer_credit_report');
    Route::get('reports/customer-credit-report/export', [ReportController::class, 'exportCustomerCreditReport'])->name('reports.customer_credit_report.export');
});

require __DIR__ . '/auth.php';

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
