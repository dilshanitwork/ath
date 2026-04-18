<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\DirectBill;
use App\Models\Collection;
use App\Models\User;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OLDReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view finance')->only(['showfinancialSummary']);
    }

    // Installments Report - Show pending next payments for a date range with search and sorting
    public function showOverdueReport(Request $request)
    {
        // Fetch overdue bills using days count
        $overdueDays = $request->input('overdue_days');
        $date = today()->subDays((int) $overdueDays)->toDateString();
        // Bills are overdue if next_bill <= $date
        $query = Bill::where('next_bill', '<', $date)->whereNotNull('next_bill')->where('balance', '>', 0);

        // Filter by user's role
        if (auth()->user()->hasRole('Van User')) {
            $query->where('category', 1); // Only category 0 for Van User
        } elseif (auth()->user()->hasRole('Showroom User')) {
            $query->where('category', 0); // Only category 1 for Showroom User
        }

        // Apply search filters
        if ($request->filled('customer_name')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('customer_name') . '%');
            });
        }
        if ($request->filled('customer_nic')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('nic', 'like', '%' . $request->input('customer_nic') . '%');
            });
        }
        if ($request->filled('customer_mobile')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('mobile', 'like', '%' . $request->input('customer_mobile') . '%');
            });
        }
        if ($request->filled('bill_number')) {
            $query->where('bill_number', 'like', '%' . $request->input('bill_number') . '%');
        }

        if ($request->filled('hometown_value')) {
            $query->whereHas('customer.hometownValue', function ($q) use ($request) {
                $q->where('value', 'like', '%' . $request->input('hometown_value') . '%');
            });
        }

        $totalOverdue = $query->sum('next_payment');

        // Apply sorting
        $sortBy = $request->input('sort_by', 'next_payment'); // Default sorting by next_payment
        $sortOrder = $request->input('sort_order', 'asc'); // Default sorting order is ascending

        // Query hometown values
        $hometowns = AttributeValue::select('value')->distinct()->get();

        // Apply the sorting and paginate
        $installments = $query->orderBy($sortBy, $sortOrder)->paginate(10);

        // Return the view with necessary data
        return view('reports.overdue', compact('installments', 'overdueDays', 'hometowns', 'totalOverdue'));
    }

    public function exportOverdue(Request $request)
    {
        $overdueDays = $request->input('overdue_days');
        $date = today()->subDays((int) $overdueDays)->toDateString();

        $customerName = $request->input('customer_name');
        $customerNic = $request->input('customer_nic');
        $customerMobile = $request->input('customer_mobile');
        $billNumber = $request->input('bill_number');
        $hometownValue = $request->input('hometown_value');

        $sortBy = $request->input('sort_by', 'next_payment');
        $sortOrder = $request->input('sort_order', 'asc');

        $query = Bill::with(['customer.hometownValue'])
            ->where('next_bill', '<', $date)
            ->whereNotNull('next_bill')
            ->where('balance', '>', 0);

        // Filter by user's role
        if (auth()->user()->hasRole('Van User')) {
            $query->where('category', 1); // Only category 1 for Van User
        } elseif (auth()->user()->hasRole('Showroom User')) {
            $query->where('category', 0); // Only category 0 for Showroom User
        }

        // Apply all filters
        if ($customerName) {
            $query->whereHas('customer', function ($q) use ($customerName) {
                $q->where('name', 'like', "%{$customerName}%");
            });
        }
        if ($customerNic) {
            $query->whereHas('customer', function ($q) use ($customerNic) {
                $q->where('nic', 'like', "%{$customerNic}%");
            });
        }
        if ($customerMobile) {
            $query->whereHas('customer', function ($q) use ($customerMobile) {
                $q->where('mobile', 'like', "%{$customerMobile}%");
            });
        }
        if ($billNumber) {
            $query->where('bill_number', 'like', "%{$billNumber}%");
        }
        if ($hometownValue) {
            $query->whereHas('customer.hometownValue', function ($q) use ($hometownValue) {
                $q->where('value', 'like', "%{$hometownValue}%");
            });
        }

        $installments = $query->orderBy($sortBy, $sortOrder)->get();

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($installments) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            fputcsv($handle, ['Bill Number', 'Customer', 'Mobile', 'Contact', 'Area', 'Total Price', 'Advance Payment', 'Balance', 'Next Payment Date', 'Next Payment Amount']);

            // CSV Rows
            foreach ($installments as $bill) {
                fputcsv($handle, [$bill->bill_number, $bill->customer->name ?? 'N/A', $bill->customer->mobile ?? 'N/A', $bill->customer->mobile_2 ?? 'N/A', $bill->customer->hometownValue->value ?? 'N/A', number_format($bill->total_price, 2), number_format($bill->advance_payment, 2), number_format($bill->balance, 2), $bill->next_bill, number_format($bill->next_payment, 2)]);
            }

            fclose($handle);
        });

        $fileName = 'overdue_payments_' . now()->format('Y-m-d_H:i:s') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }

    public function printOverdue(Request $request)
    {
        $overdueDays = $request->input('overdue_days');
        $date = today()->subDays((int) $overdueDays)->toDateString();

        $sortBy = $request->input('sort_by', 'next_payment');
        $sortOrder = $request->input('sort_order', 'asc');

        $query = Bill::with(['customer.hometownValue'])
            ->where('next_bill', '<', $date)
            ->whereNotNull('next_bill')
            ->where('balance', '>', 0);

        // Filter by user's role
        if (auth()->user()->hasRole('Van User')) {
            $query->where('category', 1); // Only category 1 for Van User
        } elseif (auth()->user()->hasRole('Showroom User')) {
            $query->where('category', 0); // Only category 0 for Showroom User
        }

        // Filters
        if ($request->filled('customer_name')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('customer_name') . '%');
            });
        }
        if ($request->filled('customer_nic')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('nic', 'like', '%' . $request->input('customer_nic') . '%');
            });
        }
        if ($request->filled('customer_mobile')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('mobile', 'like', '%' . $request->input('customer_mobile') . '%');
            });
        }
        if ($request->filled('bill_number')) {
            $query->where('bill_number', 'like', '%' . $request->input('bill_number') . '%');
        }
        if ($request->filled('hometown_value')) {
            $query->whereHas('customer.hometownValue', function ($q) use ($request) {
                $q->where('value', 'like', '%' . $request->input('hometown_value') . '%');
            });
        }

        $installments = $query->orderBy($sortBy, $sortOrder)->get();

        // Return the view for printing, reuse a view or make a new one if needed
        return view('reports.print-overdue', compact('installments', 'overdueDays'));
    }

    // Collections Report - Show collection details for a date range with search and sorting
    public function showCollectionReport(Request $request)
    {
        // Default date range: today
        $startDate = $request->input('start_date', today()->toDateString());
        $endDate = $request->input('end_date', today()->toDateString());

        // Base query for bills with pending next payments
        $query = Bill::whereBetween('next_bill', ['2025-04-30', $endDate])
            ->whereNotNull('next_bill')
            ->where('balance', '>', 0);

        // Filter by user's role
        if (auth()->user()->hasRole('Van User')) {
            $query->where('category', 1); // Only category 0 for Van User
        } elseif (auth()->user()->hasRole('Showroom User')) {
            $query->where('category', 0); // Only category 1 for Showroom User
        }

        // Apply search filters
        if ($request->filled('customer_name')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('customer_name') . '%');
            });
        }
        if ($request->filled('customer_nic')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('nic', 'like', '%' . $request->input('customer_nic') . '%');
            });
        }
        if ($request->filled('customer_mobile')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('mobile', 'like', '%' . $request->input('customer_mobile') . '%');
            });
        }
        if ($request->filled('bill_number')) {
            $query->where('bill_number', 'like', '%' . $request->input('bill_number') . '%');
        }

        if ($request->filled('hometown_value')) {
            $query->whereHas('customer.hometownValue', function ($q) use ($request) {
                $q->where('value', 'like', '%' . $request->input('hometown_value') . '%');
            });
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', 'next_payment'); // Default sorting by next_payment
        $sortOrder = $request->input('sort_order', 'asc'); // Default sorting order is ascending

        // Apply the sorting and paginate
        $installments = $query->orderBy($sortBy, $sortOrder)->paginate(10);

        // Query hometown values
        $hometowns = AttributeValue::select('value')->distinct()->get();

        // Return the view with necessary data
        return view('reports.collections', compact('installments', 'startDate', 'endDate', 'hometowns'));
    }

    public function exportCollections(Request $request)
    {
        $startDate = $request->input('start_date', today()->toDateString());
        $endDate = $request->input('end_date', today()->toDateString());
        $customerName = $request->input('customer_name');
        $customerNic = $request->input('customer_nic');
        $customerMobile = $request->input('customer_mobile');
        $billNumber = $request->input('bill_number');
        $hometownValue = $request->input('hometown_value');

        $query = Bill::with(['customer.hometownValue'])
            ->whereBetween('next_bill', ['2025-04-30', $endDate])
            ->whereNotNull('next_bill')
            ->where('balance', '>', 0);

        // Filter by user's role (must match showCollectionReport logic)
        if (auth()->user()->hasRole('Van User')) {
            $query->where('category', 1); // Only category 1 for Van User
        } elseif (auth()->user()->hasRole('Showroom User')) {
            $query->where('category', 0); // Only category 0 for Showroom User
        }

        // Apply search filters (must match showCollectionReport)
        if ($customerName) {
            $query->whereHas('customer', function ($q) use ($customerName) {
                $q->where('name', 'like', "%{$customerName}%");
            });
        }
        if ($customerNic) {
            $query->whereHas('customer', function ($q) use ($customerNic) {
                $q->where('nic', 'like', "%{$customerNic}%");
            });
        }
        if ($customerMobile) {
            $query->whereHas('customer', function ($q) use ($customerMobile) {
                $q->where('mobile', 'like', "%{$customerMobile}%");
            });
        }
        if ($billNumber) {
            $query->where('bill_number', 'like', "%{$billNumber}%");
        }
        if ($hometownValue) {
            $query->whereHas('customer.hometownValue', function ($q) use ($hometownValue) {
                $q->where('value', 'like', "%{$hometownValue}%");
            });
        }

        // Sorting (optional for export, usually by date for clarity)
        $sortBy = $request->input('sort_by', 'next_payment');
        $sortOrder = $request->input('sort_order', 'asc');
        $installments = $query->orderBy($sortBy, $sortOrder)->get();

        $response = new StreamedResponse(function () use ($installments) {
            $handle = fopen('php://output', 'w');

            // CSV Header (match what is in collections view)
            fputcsv($handle, ['Bill Number', 'Customer', 'Mobile', 'Contact', 'Area', 'Total Price', 'Balance', 'Next Payment Date', 'Next Payment Amount']);

            // CSV Rows
            foreach ($installments as $bill) {
                fputcsv($handle, [
                    $bill->bill_number,
                    $bill->customer->name,
                    $bill->customer->mobile ?? 'N/A',
                    $bill->customer->mobile_2 ?? 'N/A',
                    $bill->customer->hometownValue->value ?? 'N/A', // Area
                    number_format($bill->total_price, 2),
                    number_format($bill->balance, 2),
                    $bill->next_bill,
                    number_format($bill->next_payment, 2), // Assuming `next_payment` exists
                ]);
            }

            fclose($handle);
        });

        $fileName = 'collections_' . now()->format('Y-m-d_H:i:s') . '.csv';

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
    public function printCollections(Request $request)
    {
        // Default date range: today
        $startDate = $request->input('start_date', today()->toDateString());
        $endDate = $request->input('end_date', today()->toDateString());

        // Base query for bills with pending next payments (match showCollectionReport)
        $query = Bill::whereBetween('next_bill', ['2025-04-30', $endDate])
            ->whereNotNull('next_bill')
            ->where('balance', '>', 0);

        // Filter by user's role
        if (auth()->user()->hasRole('Van User')) {
            $query->where('category', 1); // Only category 1 for Van User
        } elseif (auth()->user()->hasRole('Showroom User')) {
            $query->where('category', 0); // Only category 0 for Showroom User
        }

        // Apply search filters (match showCollectionReport)
        if ($request->filled('customer_name')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('customer_name') . '%');
            });
        }
        if ($request->filled('customer_nic')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('nic', 'like', '%' . $request->input('customer_nic') . '%');
            });
        }
        if ($request->filled('customer_mobile')) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('mobile', 'like', '%' . $request->input('customer_mobile') . '%');
            });
        }
        if ($request->filled('bill_number')) {
            $query->where('bill_number', 'like', '%' . $request->input('bill_number') . '%');
        }
        if ($request->filled('hometown_value')) {
            $query->whereHas('customer.hometownValue', function ($q) use ($request) {
                $q->where('value', 'like', '%' . $request->input('hometown_value') . '%');
            });
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', 'next_payment'); // Default sorting by next_payment
        $sortOrder = $request->input('sort_order', 'asc'); // Default sorting order is ascending

        // Apply the sorting and get all results (not paginated for print)
        $installments = $query->orderBy($sortBy, $sortOrder)->get();

        // Return the view with necessary data
        return view('reports.print-collections', compact('installments', 'startDate', 'endDate'));
    }

    public function showCollectedReport(Request $request)
    {
        $collections = Collection::with(['bill', 'user'])
            ->when($request->start_date, function ($query) use ($request) {
                $query->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($query) use ($request) {
                $query->where('date', '<=', $request->end_date);
            })
            ->when($request->customer_name, function ($query) use ($request) {
                $query->whereHas('bill.customer', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->customer_name . '%');
                });
            })
            ->when($request->bill_number, function ($query) use ($request) {
                $query->whereHas('bill', function ($q) use ($request) {
                    $q->where('bill_number', 'like', '%' . $request->bill_number . '%');
                });
            })
            // User role filter:
            ->when(auth()->user()->hasRole('Van User'), function ($query) {
                $query->whereHas('bill', function ($q) {
                    $q->where('category', 1); // Only category 1 for Van User
                });
            })
            ->when(auth()->user()->hasRole('Showroom User'), function ($query) {
                $query->whereHas('bill', function ($q) {
                    $q->where('category', 0); // Only category 0 for Showroom User
                });
            })
            ->paginate(10);

        // Get filter values for the view
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        return view('reports.collected', compact('collections', 'startDate', 'endDate'));
    }

    public function exportCollected(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $customerName = $request->input('customer_name');
        $billNumber = $request->input('bill_number');

        $query = Collection::with(['bill.customer.hometownValue', 'user'])
            ->when($startDate, function ($q) use ($startDate) {
                $q->where('date', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->where('date', '<=', $endDate);
            })
            ->when($customerName, function ($q) use ($customerName) {
                $q->whereHas('bill.customer', function ($q2) use ($customerName) {
                    $q2->where('name', 'like', "%{$customerName}%");
                });
            })
            ->when($billNumber, function ($q) use ($billNumber) {
                $q->whereHas('bill', function ($q2) use ($billNumber) {
                    $q2->where('bill_number', 'like', "%{$billNumber}%");
                });
            });

        // User role filter
        if (auth()->user()->hasRole('Van User')) {
            $query->whereHas('bill', function ($q) {
                $q->where('category', 1);
            });
        } elseif (auth()->user()->hasRole('Showroom User')) {
            $query->whereHas('bill', function ($q) {
                $q->where('category', 0);
            });
        }

        $collections = $query->orderBy('date', 'asc')->get();

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($collections) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            fputcsv($handle, ['Date', 'Bill Number', 'Customer', 'Mobile', 'Contact', 'Area', 'Collected By', 'Amount']);

            // CSV Rows
            foreach ($collections as $collection) {
                fputcsv($handle, [$collection->date, $collection->bill->bill_number ?? 'N/A', $collection->bill->customer->name ?? 'N/A', $collection->bill->customer->mobile ?? 'N/A', $collection->bill->customer->mobile_2 ?? 'N/A', $collection->bill->customer->hometownValue->value ?? 'N/A', $collection->user->name ?? 'N/A', number_format($collection->amount, 2)]);
            }

            fclose($handle);
        });

        $fileName = 'collected_' . now()->format('Y-m-d_H:i:s') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }

    public function printCollected(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $customerName = $request->input('customer_name');
        $billNumber = $request->input('bill_number');

        $query = Collection::with(['bill.customer.hometownValue', 'user'])
            ->when($startDate, function ($q) use ($startDate) {
                $q->where('date', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->where('date', '<=', $endDate);
            })
            ->when($customerName, function ($q) use ($customerName) {
                $q->whereHas('bill.customer', function ($q2) use ($customerName) {
                    $q2->where('name', 'like', "%{$customerName}%");
                });
            })
            ->when($billNumber, function ($q) use ($billNumber) {
                $q->whereHas('bill', function ($q2) use ($billNumber) {
                    $q2->where('bill_number', 'like', "%{$billNumber}%");
                });
            });

        // User role filter
        if (auth()->user()->hasRole('Van User')) {
            $query->whereHas('bill', function ($q) {
                $q->where('category', 1);
            });
        } elseif (auth()->user()->hasRole('Showroom User')) {
            $query->whereHas('bill', function ($q) {
                $q->where('category', 0);
            });
        }

        $collections = $query->orderBy('date', 'asc')->get();

        return view('reports.print-collected', compact('collections', 'startDate', 'endDate'));
    }

    public function showFinancialSummary(Request $request)
    {
        $startDate = now()->toDateString();
        $endDate = now()->toDateString();

        if ($request->hasAny(['start_date', 'end_date', 'user_id', 'type'])) {
            $startDate = $request->input('start_date') ?? now()->toDateString();
            $endDate = $request->input('end_date') ?? now()->toDateString();
        }
        $userId = $request->input('user_id');
        $type = $request->input('type');

        // --- Filtered Collections ---
        $collectionsQuery = Collection::query();
        if ($startDate) {
            $collectionsQuery->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $collectionsQuery->where('date', '<=', $endDate);
        }
        if ($userId) {
            $collectionsQuery->where('user_id', $userId);
        }
        if ($type) {
            $collectionsQuery->where('type', $type);
        }

        $totalCollections = $collectionsQuery->sum('payment');

        // --- Filtered Bills ---
        $billsQuery = Bill::query();
        if ($startDate) {
            $billsQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $billsQuery->whereDate('created_at', '<=', $endDate);
        }
        if ($userId) {
            $billsQuery->where('user_id', $userId);
        }
        if ($type) {
            $billsQuery->where('payment_type', $type);
        }

        $totalSales = $billsQuery->sum('total_price');
        $totalAdvance = $billsQuery->sum('advance_payment');
        $totalBalance = $billsQuery->sum('balance');
        $overdueAmount = (clone $billsQuery)->where('balance', '>', 0)->where('next_bill', '<', today())->sum('balance');

        // --- [NEW] Filtered Direct Bills ---
        $directBillsQuery = DirectBill::query();
        if ($startDate) {
            $directBillsQuery->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $directBillsQuery->whereDate('created_at', '<=', $endDate);
        }

        $totalDirectBills = $directBillsQuery->sum('final_amount');

        // --- [UPDATED] Total Calculation ---
        $total = $totalCollections + $totalAdvance + $totalDirectBills;

        $users = User::orderBy('name')->get();
        $types = Collection::select('type')->distinct()->pluck('type');

        // --- [UPDATED] Pass new variable to the view ---
        return view(
            'reports.financial-summary',
            compact(
                'totalSales',
                'totalAdvance',
                'totalBalance',
                'totalCollections',
                'overdueAmount',
                'totalDirectBills', // <-- Add this
                'users',
                'types',
                'startDate',
                'endDate',
                'userId',
                'type',
                'total',
            ),
        );
    }

    public function showFinancialList(Request $request)
    {
        // Collection Query
        $collectionQuery = Collection::with(['user', 'bill', 'bill.customer']);
        if ($request->filled('start_date')) {
            $collectionQuery->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $collectionQuery->where('date', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $collectionQuery->where('user_id', $request->user_id);
        }
        if ($request->filled('type')) {
            $collectionQuery->where('type', $request->type);
        }
        if ($request->filled('payment_method')) {
            $collectionQuery->where('payment_type', $request->payment_method);
        }
        if ($request->filled('bill')) {
            $collectionQuery->whereHas('bill', function ($q) use ($request) {
                $q->where('bill_number', 'like', '%' . $request->bill . '%');
            });
        }
        // Filter by bill's category (through relationship)
        if ($request->filled('category')) {
            $collectionQuery->whereHas('bill', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        $collections = $collectionQuery
            ->get()
            ->map(function ($item) {
                return [
                    'source' => 'collection',
                    'date' => $item->date,
                    'bill_number' => $item->bill->bill_number ?? null,
                    'bill_id' => $item->bill->id ?? null,
                    'customer_name' => $item->bill->customer->name ?? null,
                    'amount' => $item->payment,
                    'payment_method' => $item->type,

                    // 'user_name' => $item->user->name ?? null, // <-- CHANGED
                    'user' => $item->user, // <-- TO THIS

                    'category' => $item->bill->category,
                    'id' => $item->id,
                ];
            })
            ->toArray();

        // Bill Query
        $billQuery = Bill::with(['customer', 'user']);
        if ($request->filled('start_date')) {
            $billQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $billQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $billQuery->where('user_id', $request->user_id);
        }
        if ($request->filled('type')) {
            $billQuery->where('payment_type', $request->type);
        }
        if ($request->filled('bill')) {
            $billQuery->where('bill_number', 'like', '%' . $request->bill . '%');
        }
        if ($request->filled('category')) {
            $billQuery->where('category', $request->category);
        }

        $bills = $billQuery
            ->get()
            ->map(function ($item) {
                return [
                    'source' => 'bill',
                    'date' => $item->created_at->toDateString(),
                    'bill_number' => $item->bill_number,
                    'bill_id' => $item->id,
                    'customer_name' => $item->customer->name ?? null,
                    'amount' => $item->advance_payment,
                    'payment_method' => $item->payment_type,

                    // 'user_name' => $item->user->name ?? null, // <-- CHANGED
                    'user' => $item->user, // <-- TO THIS

                    'category' => $item->category,
                    'id' => $item->id,
                ];
            })
            ->toArray();

        // Direct Bill Query
        $directBillQuery = DirectBill::query();
        if ($request->filled('start_date')) {
            $directBillQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $directBillQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('bill')) {
            $directBillQuery->where('bill_number', 'like', '%' . $request->bill . '%');
        }
        // Note: Direct bills don't have user_id, type, or category filters

        $directBills = $directBillQuery
            ->get()
            ->map(function ($item) {
                return [
                    'source' => 'direct_bill',
                    'date' => $item->created_at->toDateString(),
                    'bill_number' => $item->bill_number,
                    'bill_id' => null,
                    'customer_name' => $item->customer_name,
                    'amount' => $item->final_amount,
                    'payment_method' => null, // Direct bills don't have payment method

                    // 'user_name' => null, // <-- CHANGED
                    'user' => $item->user ?? null, // <-- TO THIS

                    'category' => 0, // Direct bills don't have category
                    'id' => $item->id,
                ];
            })
            ->toArray();

        // Merge and sort all data based on source filter
        if ($request->filled('source')) {
            if ($request->source === 'bill') {
                $allData = $bills;
            } elseif ($request->source === 'collection') {
                $allData = $collections;
            } elseif ($request->source === 'direct_bill') {
                $allData = $directBills;
            } else {
                $allData = array_merge($collections, $bills, $directBills);
            }
        } else {
            $allData = array_merge($collections, $bills, $directBills);
        }
        usort($allData, fn($a, $b) => strcmp($b['date'], $a['date'])); // descending date order

        $users = User::all();
        $types = ['cash', 'card', 'online'];

        return view('reports.financial-list', compact('allData', 'users', 'types'));
    }

    public function exportFinancialList(Request $request)
    {
        // Collection Query
        $collectionQuery = Collection::with(['user', 'bill', 'bill.customer']);
        if ($request->filled('start_date')) {
            $collectionQuery->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $collectionQuery->where('date', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $collectionQuery->where('user_id', $request->user_id);
        }
        if ($request->filled('type')) {
            $collectionQuery->where('type', $request->type);
        }
        if ($request->filled('payment_method')) {
            $collectionQuery->where('payment_type', $request->payment_method);
        }
        if ($request->filled('bill')) {
            $collectionQuery->whereHas('bill', function ($q) use ($request) {
                $q->where('bill_number', 'like', '%' . $request->bill . '%');
            });
        }
        if ($request->filled('category')) {
            $collectionQuery->whereHas('bill', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        $collections = $collectionQuery
            ->get()
            ->map(function ($item) {
                return [
                    'source' => 'collection',
                    'date' => $item->date,
                    'bill_number' => $item->bill->bill_number ?? null,
                    'customer_name' => $item->bill->customer->name ?? null,
                    'amount' => $item->payment,
                    'payment_method' => $item->type,
                    'user_name' => $item->user->name ?? null,
                    'category' => $item->bill->category ?? null,
                    'id' => $item->id,
                ];
            })
            ->toArray();

        // Bill Query
        $billQuery = Bill::with(['customer', 'user']);
        if ($request->filled('start_date')) {
            $billQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $billQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $billQuery->where('user_id', $request->user_id);
        }
        if ($request->filled('type')) {
            $billQuery->where('payment_type', $request->type);
        }
        if ($request->filled('bill')) {
            $billQuery->where('bill_number', 'like', '%' . $request->bill . '%');
        }
        if ($request->filled('category')) {
            $billQuery->where('category', $request->category);
        }

        $bills = $billQuery
            ->get()
            ->map(function ($item) {
                return [
                    'source' => 'bill',
                    'date' => $item->created_at->toDateString(),
                    'bill_number' => $item->bill_number,
                    'customer_name' => $item->customer->name ?? null,
                    'amount' => $item->advance_payment,
                    'payment_method' => $item->payment_type,
                    'user_name' => $item->user->name ?? null,
                    'category' => $item->category ?? null,
                    'id' => $item->id,
                ];
            })
            ->toArray();

        // Direct Bill Query
        $directBillQuery = DirectBill::query();
        if ($request->filled('start_date')) {
            $directBillQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $directBillQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('bill')) {
            $directBillQuery->where('bill_number', 'like', '%' . $request->bill . '%');
        }

        $directBills = $directBillQuery
            ->get()
            ->map(function ($item) {
                return [
                    'source' => 'direct_bill',
                    'date' => $item->created_at->toDateString(),
                    'bill_number' => $item->bill_number,
                    'customer_name' => $item->customer_name,
                    'amount' => $item->final_amount,
                    'payment_method' => null,
                    'user_name' => null,
                    'category' => null,
                    'id' => $item->id,
                ];
            })
            ->toArray();

        // Merge and sort all data
        if ($request->filled('source')) {
            if ($request->source === 'bill') {
                $allData = $bills;
            } elseif ($request->source === 'collection') {
                $allData = $collections;
            } elseif ($request->source === 'direct_bill') {
                $allData = $directBills;
            } else {
                $allData = array_merge($collections, $bills, $directBills);
            }
        } else {
            $allData = array_merge($collections, $bills, $directBills);
        }
        usort($allData, fn($a, $b) => strcmp($b['date'], $a['date']));

        // --- Streamed CSV ---
        $response = new StreamedResponse(function () use ($allData) {
            $handle = fopen('php://output', 'w');
            // CSV Header
            fputcsv($handle, ['Type', 'Bill Number', 'Customer', 'Amount', 'Payment Method', 'Sale Type', 'Date', 'User']);
            // CSV Rows
            foreach ($allData as $row) {
                $saleType = $row['category'] === 0 || $row['category'] === '0' ? 'Showroom Sale' : ($row['category'] === 1 || $row['category'] === '1' ? 'Van Sale' : 'N/A');
                fputcsv($handle, [ucfirst($row['source'] === 'direct_bill' ? 'Direct Bill' : $row['source']), $row['bill_number'] ?? 'N/A', $row['customer_name'] ?? 'N/A', number_format($row['amount'], 2), ucfirst($row['payment_method'] ?? 'N/A'), $saleType, $row['date'] ?? 'N/A', $row['user_name'] ?? 'N/A']);
            }
            fclose($handle);
        });

        $fileName = 'financial_list_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        return $response;
    }
    public function printFinancialList(Request $request)
    {
        // Collection Query
        $collectionQuery = Collection::with(['user', 'bill', 'bill.customer']);
        if ($request->filled('start_date')) {
            $collectionQuery->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $collectionQuery->where('date', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $collectionQuery->where('user_id', $request->user_id);
        }
        if ($request->filled('type')) {
            $collectionQuery->where('type', $request->type);
        }
        if ($request->filled('payment_method')) {
            $collectionQuery->where('payment_type', $request->payment_method);
        }
        if ($request->filled('bill')) {
            $collectionQuery->whereHas('bill', function ($q) use ($request) {
                $q->where('bill_number', 'like', '%' . $request->bill . '%');
            });
        }
        if ($request->filled('category')) {
            $collectionQuery->whereHas('bill', function ($q) use ($request) {
                $q->where('category', $request->category);
            });
        }

        $collections = $collectionQuery
            ->get()
            ->map(function ($item) {
                return [
                    'source' => 'collection',
                    'date' => $item->date,
                    'bill_number' => $item->bill->bill_number ?? null,
                    'customer_name' => $item->bill->customer->name ?? null,
                    'amount' => $item->payment,
                    'payment_method' => $item->type,
                    'user_name' => $item->user->name ?? null,
                    'category' => $item->bill->category ?? null,
                    'id' => $item->id,
                ];
            })
            ->toArray();

        // Bill Query
        $billQuery = Bill::with(['customer', 'user']);
        if ($request->filled('start_date')) {
            $billQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $billQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $billQuery->where('user_id', $request->user_id);
        }
        if ($request->filled('type')) {
            $billQuery->where('payment_type', $request->type);
        }
        if ($request->filled('bill')) {
            $billQuery->where('bill_number', 'like', '%' . $request->bill . '%');
        }
        if ($request->filled('category')) {
            $billQuery->where('category', $request->category);
        }

        $bills = $billQuery
            ->get()
            ->map(function ($item) {
                return [
                    'source' => 'bill',
                    'date' => $item->created_at->toDateString(),
                    'bill_number' => $item->bill_number,
                    'customer_name' => $item->customer->name ?? null,
                    'amount' => $item->advance_payment,
                    'payment_method' => $item->payment_type,
                    'user_name' => $item->user->name ?? null,
                    'category' => $item->category ?? null,
                    'id' => $item->id,
                ];
            })
            ->toArray();

        // Direct Bill Query
        $directBillQuery = DirectBill::query();
        if ($request->filled('start_date')) {
            $directBillQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $directBillQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('bill')) {
            $directBillQuery->where('bill_number', 'like', '%' . $request->bill . '%');
        }

        $directBills = $directBillQuery
            ->get()
            ->map(function ($item) {
                return [
                    'source' => 'direct_bill',
                    'date' => $item->created_at->toDateString(),
                    'bill_number' => $item->bill_number,
                    'customer_name' => $item->customer_name,
                    'amount' => $item->final_amount,
                    'payment_method' => null,
                    'user_name' => null,
                    'category' => null,
                    'id' => $item->id,
                ];
            })
            ->toArray();

        // Merge and sort all data
        if ($request->filled('source')) {
            if ($request->source === 'bill') {
                $allData = $bills;
            } elseif ($request->source === 'collection') {
                $allData = $collections;
            } elseif ($request->source === 'direct_bill') {
                $allData = $directBills;
            } else {
                $allData = array_merge($collections, $bills, $directBills);
            }
        } else {
            $allData = array_merge($collections, $bills, $directBills);
        }
        usort($allData, fn($a, $b) => strcmp($b['date'], $a['date']));

        return view('reports.print-financial-list', compact('allData'));
    }

    public function allBills(Request $request)
    {
        // Prepare data for filters
        $users = User::orderBy('name')->get();
        $types = ['cash', 'card', 'online']; // adapt as needed
        $categories = [
            '' => '-- All Types --',
            '0' => 'Showroom Sale',
            '1' => 'Van Sale',
        ];

        // Query with filters
        $billQuery = Bill::with(['customer', 'user', 'items', 'collections.user', 'paymentSchedules'])->orderByDesc('created_at');

        if ($request->filled('start_date')) {
            $billQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $billQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $billQuery->where('user_id', $request->user_id);
        }
        if ($request->filled('type')) {
            $billQuery->where('payment_type', $request->type);
        }
        if ($request->filled('category')) {
            $billQuery->where('category', $request->category);
        }
        if ($request->filled('bill')) {
            $billQuery->where('bill_number', 'like', '%' . $request->bill . '%');
        }

        $bills = $billQuery->get();

        return view('reports.all-bills', compact('bills', 'users', 'types', 'categories'));
    }

    public function allBillsExport(Request $request)
    {
        $billQuery = \App\Models\Bill::with(['customer', 'user', 'items', 'collections.user', 'paymentSchedules'])->orderByDesc('created_at');

        if ($request->filled('start_date')) {
            $billQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $billQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $billQuery->where('user_id', $request->user_id);
        }
        if ($request->filled('type')) {
            $billQuery->where('payment_type', $request->type);
        }
        if ($request->filled('category')) {
            $billQuery->where('category', $request->category);
        }
        if ($request->filled('bill')) {
            $billQuery->where('bill_number', 'like', '%' . $request->bill . '%');
        }

        $bills = $billQuery->get();

        $today = now()->format('Y-m-d');
        $user = auth()->user()->name ?? 'user';
        $filename = 'all-bills-export-' . $today . '-' . str_replace(' ', '_', strtolower($user)) . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $columns = [
            'Sale Type',
            'Bill No',
            'Customer',
            'Total Price',
            'Advance Payment',
            'Balance',
            'Payment Method',
            'Installment',
            'Date',
            'User',
            'Items', // <--- Added
            'Payments Made', // <--- Added
            'Payment Schedule',
        ];

        $callback = function () use ($bills, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($bills as $bill) {
                // Items column
                $items = $bill->items
                    ->map(function ($item) {
                        return $item->item_name . ' (' . $item->item_quantity . ' x ' . number_format($item->item_price, 2) . ')';
                    })
                    ->implode('; ');

                // Payments Made column
                $payments = $bill->collections
                    ->map(function ($c) {
                        return number_format($c->payment, 2) . ' (' . ucfirst($c->type) . ') - ' . $c->date . ' by ' . ($c->user->name ?? '');
                    })
                    ->implode('; ');

                // Payment schedule column
                $schedules = $bill->paymentSchedules->pluck('payment_date')->implode('; ');

                fputcsv($file, [$bill->category === 0 || $bill->category === '0' ? 'Showroom Sale' : ($bill->category === 1 || $bill->category === '1' ? 'Van Sale' : '-'), $bill->bill_number, $bill->customer->name ?? '-', $bill->total_price, $bill->advance_payment, $bill->balance, ucfirst($bill->payment_type ?? '-'), number_format($bill->installment_payment, 2) . ' x ' . $bill->installments, $bill->created_at->format('Y-m-d'), $bill->user->name ?? '-', $items, $payments, $schedules]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function closedBills(Request $request)
    {
        // Prepare data for filters
        $users = User::orderBy('name')->get();
        $types = ['cash', 'card', 'online'];
        $categories = [
            '' => '-- All Types --',
            '0' => 'Showroom Sale',
            '1' => 'Van Sale',
        ];

        // Query with filters
        $billQuery = Bill::with(['customer', 'user', 'items', 'collections.user', 'paymentSchedules'])
            ->where('balance', 0) // Only closed bills
            ->orderByDesc('created_at');

        // Filter by user's role
        if (auth()->user()->hasRole('Van User')) {
            $billQuery->where('category', 1); // Only category 0 for Van User
        } elseif (auth()->user()->hasRole('Showroom User')) {
            $billQuery->where('category', 0); // Only category 1 for Showroom User
        }

        if ($request->filled('start_date')) {
            $billQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $billQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $billQuery->where('user_id', $request->user_id);
        }
        if ($request->filled('type')) {
            $billQuery->where('payment_type', $request->type);
        }
        if ($request->filled('category')) {
            $billQuery->where('category', $request->category);
        }
        if ($request->filled('bill_number')) {
            $billQuery->where('bill_number', 'like', '%' . $request->bill_number . '%');
        }
        if ($request->filled('customer_name')) {
            $billQuery->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }

        // Get summary statistics
        $totalAmount = $billQuery->sum('total_price');
        $bills = $billQuery->paginate(10);

        return view('reports.closed-bills', compact('bills', 'users', 'types', 'categories', 'totalAmount'));
    }

    public function closedBillsExport(Request $request)
    {
        $billQuery = Bill::with(['customer', 'user', 'items', 'collections.user', 'paymentSchedules'])
            ->where('balance', 0)
            ->orderByDesc('created_at');

        // Filter by user's role
        if (auth()->user()->hasRole('Van User')) {
            $billQuery->where('category', 1); // Only category 0 for Van User
        } elseif (auth()->user()->hasRole('Showroom User')) {
            $billQuery->where('category', 0); // Only category 1 for Showroom User
        }

        if ($request->filled('start_date')) {
            $billQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $billQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('user_id')) {
            $billQuery->where('user_id', $request->user_id);
        }
        if ($request->filled('type')) {
            $billQuery->where('payment_type', $request->type);
        }
        if ($request->filled('category')) {
            $billQuery->where('category', $request->category);
        }
        if ($request->filled('bill_number')) {
            $billQuery->where('bill_number', 'like', '%' . $request->bill_number . '%');
        }
        if ($request->filled('customer_name')) {
            $billQuery->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }

        $bills = $billQuery->get();

        $filename = 'closed-bills-' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($bills) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, ['Sale Type', 'Bill No', 'Customer', 'Total Amount', 'Payment Method', 'Date Closed', 'Created By', 'Items', 'Payment History']);

            foreach ($bills as $bill) {
                // Format items
                $items = $bill->items
                    ->map(function ($item) {
                        return $item->item_name . ' (' . $item->item_quantity . ' x ' . number_format($item->item_price, 2) . ')';
                    })
                    ->implode('; ');

                // Format payment history
                $payments = $bill->collections
                    ->map(function ($c) {
                        return number_format($c->payment, 2) . ' (' . ucfirst($c->type) . ') - ' . $c->date;
                    })
                    ->implode('; ');

                fputcsv($file, [$bill->category === 0 ? 'Showroom Sale' : 'Van Sale', $bill->bill_number, $bill->customer->name ?? 'N/A', number_format($bill->total_price, 2), ucfirst($bill->payment_type ?? 'N/A'), $bill->collections->sortByDesc('date')->first()->date ?? 'N/A', $bill->user->name ?? 'N/A', $items, $payments]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
