<?php
namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Traits\Loggable;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    use Loggable;
    public function index()
    {
        $attributeValues = AttributeValue::with('attribute')->get();
        return view('attribute_values.index', compact('attributeValues'));
    }

    public function create()
    {
        $attributes = Attribute::all();
        return view('attribute_values.create', compact('attributes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string|max:255',
        ]);
        AttributeValue::create($request->all());
        $this->logAction('Created a new Attribute Value - ' . $request->value);
        return redirect()->route('attribute-values.index')->with('success', 'Attribute value created successfully.');
    }

    public function edit(AttributeValue $attributeValue)
    {
        $attributes = Attribute::all();
        return view('attribute_values.edit', compact('attributeValue', 'attributes'));
    }

    public function update(Request $request, AttributeValue $attributeValue)
    {
        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string|max:255',
        ]);
        $attributeValue->update($request->all());
        $this->logAction('Updated Attribute Value ID: ' . $attributeValue->value);
        return redirect()->route('attribute-values.index')->with('success', 'Attribute value updated successfully.');
    }

    public function destroy(AttributeValue $attributeValue)
    {
        $attributeValue->delete();
        $this->logAction('Deleted Attribute Value ID: ' . $attributeValue->value);
        return redirect()->route('attribute-values.index')->with('success', 'Attribute value deleted successfully.');
    }
}
