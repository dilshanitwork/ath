<?php
namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Traits\Loggable;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    use Loggable;
    public function __construct()
    {
        // Restrict access to specific permissions
        $this->middleware('can:view attributes')->only(['index', 'show']);
        $this->middleware('can:create attributes')->only(['create', 'store']);
        $this->middleware('can:edit attributes')->only(['edit', 'update']);
        $this->middleware('can:delete attributes')->only(['destroy']);
    }
    public function index()
    {
        $attributes = Attribute::all();
        return view('attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('attributes.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Attribute::create($request->all());
        $this->logAction('Created a new Attribute - ' . $request->name);
        return redirect()->route('attributes.index')->with('success', 'Attribute created successfully.');
    }

    public function edit(Attribute $attribute)
    {
        return view('attributes.edit', compact('attribute'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $attribute->update($request->all());
        $this->logAction('Updated Attribute ID: ' . $attribute->name);
        return redirect()->route('attributes.index')->with('success', 'Attribute updated successfully.');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        $this->logAction('Deleted Attribute ID: ' . $attribute->name);
        return redirect()->route('attributes.index')->with('success', 'Attribute deleted successfully.');
    }
}
