<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Log;
use App\Models\Attribute;
use App\Models\DirectBill;
use App\Traits\Loggable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    use Loggable;
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('can:view customers')->only(['index', 'show', 'suggestions']);
        $this->middleware('can:create customers')->only(['create', 'store']);
        $this->middleware('can:edit customers')->only(['edit', 'update']);
        $this->middleware('can:delete customers')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Customer::query();

        $query->orderBy('created_at', 'desc');

        // Filter by user's role
        if (auth()->user()->hasRole('Van User')) {
            $query->where('category', 1); // Only category 0 for Van User
        } elseif (auth()->user()->hasRole('Showroom User')) {
            $query->where('category', 0); // Only category 1 for Showroom User
        }

        if ($request->filled('search')) {
            $query
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('nic', 'like', '%' . $request->search . '%')
                ->orWhere('mobile', 'like', '%' . $request->search . '%')
                ->orWhere('mobile_2', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->input('per_page', 10);

        $customers = $query->paginate($perPage);
        return view('customers.index', compact('customers'));
    }

    public function suggestions(Request $request)
    {
        $query = $request->input('query');

        // Fetch suggestions from the database
        $suggestions = Customer::where('name', 'like', '%' . $query . '%')
            ->orWhere('mobile', 'like', '%' . $query . '%')
            ->limit(10) // Limit the number of suggestions
            ->get(['id', 'name', 'mobile']); // Return only necessary fields

        return response()->json($suggestions);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $homeTownAttribute = Attribute::where('name', 'Home Town')->first();
        $hometowns = $homeTownAttribute ? $homeTownAttribute->values : collect();
        $isShowroomUser = auth()->user()->hasRole('Showroom User');
        $isVanUser = auth()->user()->hasRole('Van User');

        return view('customers.create', compact('hometowns', 'isShowroomUser', 'isVanUser'));
    }

    /**
     * Store a newly created resource in storage.Name, address, email, mobile, NIC, gender, hometown, photo
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|max:255',
            'address' => 'nullable',
            'email' => 'nullable|email|unique:customers,email', // Ensure email is unique in the customers table
            'mobile' => 'nullable|max:15',
            'nic' => 'nullable|unique:customers,nic', // Ensure NIC is unique in the customers table
            'gender' => 'required|in:male,female,other', // Assuming gender can be male, female, or other
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Validate photo as an image file
            'credit_limit' => 'nullable',
        ]);

        // Determine the category
        $category = null;
        if (auth()->user()->hasRole('Showroom User')) {
            $category = 0; // Showroom Sale
        } elseif (auth()->user()->hasRole('Van User')) {
            $category = 1; // Van Sale
        } else {
            $request->validate([
                'category' => 'required|in:0,1',
            ]);
            $category = $request->category;
        }

        $credits_limit = null;
        if ($request->filled('credit_limit')) {
            $credits_limit = $request->credit_limit;
        } else {
            $credits_limit = 0;
        }

        // Create a new customer with the request data
        $customer = Customer::create(
            array_merge(
                $request->except('photo'), // Exclude 'photo' from mass assignment
                ['category' => $category], // Explicitly set 'category'
                ['credit_limit' => $credits_limit], // Explicitly set 'credit_limit'
            ),
        );

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Get the original file name
            $originalName = $request->file('photo')->getClientOriginalName();
            $extension = $request->file('photo')->getClientOriginalExtension();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME); // Get the file name without extension

            // Define the directory to store the file
            $directory = 'photos';

            // Check if the file already exists and generate a unique name
            $counter = 1;
            while (Storage::disk('public')->exists($directory . '/' . $originalName)) {
                $originalName = $fileName . '-' . $counter . '.' . $extension;
                $counter++;
            }

            // Store the file in the 'public' folder with the unique name
            $photoPath = $request->file('photo')->storeAs($directory, $originalName, 'public');

            // Save the photo path to the database
            $customer->photo = $photoPath;
            $customer->save();
        }

        $this->logAction('Created a new Customer - ' . $request->name);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        // Load the customer's bills list for the view (if you need the collection / list)
        $customer->load([
            'directBills' => function ($q) {
                $q->orderBy('created_at', 'desc');
            },
        ]);

        // If your DirectBill model uses customer_id as FK:
        // $totalPaid = $customer->directBills()->sum('paid');
        // $totalBalance = $customer->directBills()->sum('balance');

        // If your DirectBill records are linked by customer_name (as in your screenshot),
        // use direct DB sums to avoid loading all rows twice:
        $customerName = $customer->name;
        $totalPaid = (float) DirectBill::where('customer_name', $customerName)->sum('paid');
        $totalBalance = (float) DirectBill::where('customer_name', $customerName)->sum('balance');

        return view('customers.show', compact('customer', 'totalPaid', 'totalBalance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $homeTownAttribute = Attribute::where('name', 'Home Town')->first();
        $hometowns = $homeTownAttribute ? $homeTownAttribute->values : collect();

        return view('customers.edit', compact('customer', 'hometowns'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|max:255',
            'address' => 'required',
            'email' => 'nullable|email|unique:customers,email,' . $customer->id, // Ensure email is unique, excluding the current customer
            'mobile' => 'required|max:15',
            'nic' => 'nullable|unique:customers,nic,' . $customer->id, // Ensure NIC is unique, excluding the current customer
            'credit_limit' => 'nullable',
            'gender' => 'required|in:male,female,other', // Assuming gender can be male, female, or other
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // Validate photo as an image file
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete the old photo if it exists
            if ($customer->photo && Storage::disk('public')->exists($customer->photo)) {
                Storage::disk('public')->delete($customer->photo);
            }

            // Get the original file name
            $originalName = $request->file('photo')->getClientOriginalName();
            $extension = $request->file('photo')->getClientOriginalExtension();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME); // Get the file name without extension

            // Define the directory to store the file
            $directory = 'photos';

            // Check if the file already exists and generate a unique name
            $counter = 1;
            while (Storage::disk('public')->exists($directory . '/' . $originalName)) {
                $originalName = $fileName . '-' . $counter . '.' . $extension;
                $counter++;
            }

            // Store the file in the 'public' folder with the unique name
            $photoPath = $request->file('photo')->storeAs($directory, $originalName, 'public');

            // Save the photo path to the database
            $customer->photo = $photoPath;
        }

        // Update the customer with the request data (excluding 'photo')
        $customer->update($request->except('photo'));

        $this->logAction('Updated customer ID: ' . $customer->name);

        // Redirect to the show blade with the updated customer's ID
        return redirect()->route('customers.show', $customer->id)->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //

        if ($customer->photo) {
            Storage::disk('public')->delete($customer->photo);
        }

        $customer->delete();

        $this->logAction('Deleted customer ID: ' . $customer->name);

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
