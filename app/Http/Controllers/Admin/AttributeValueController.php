<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    public function index(Attribute $attribute)
    {
        $attribute->load('values');
        $category = Category::find($attribute->category_id);
        $attribute_values = AttributeValue::where('attribute_id', $attribute->id)->get();
        return view('admin.attribute_values.index', compact('attribute', 'category', 'attribute_values'));
    }

    public function store(Request $request, $attribute)
    {
        $request->validate([
            'value' => ['required', 'max:255'],
        ]);
    
        $attribute = Attribute::findOrFail($attribute);
        $attribute->values()->create([
            'attribute_id' => $attribute,
            'value' => $request->value,
        ]);
    
        return redirect()->back()->with('success', 'Attribute value added successfully.');
    }

    public function update(Request $request, $attribute_value_id)
    {
        $request->validate([
            'value' => ['required', 'max:255'],
        ]);

        $attribute_value = AttributeValue::findOrFail($attribute_value_id);
        $attribute_value->update([
            'value' => $request->value,
        ]);

        return redirect()->back()->with('success', 'Attribute value updated successfully.');
    }
    

    public function destroy(AttributeValue $attribute_value)
    {
        $attribute_value->delete();

        return redirect()->back()->with('success', 'Attribute value deleted successfully.');
    }
}
