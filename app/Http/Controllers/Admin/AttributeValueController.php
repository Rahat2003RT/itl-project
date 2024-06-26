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

    public function store(Request $request, $attribute_id)
    {
        $request->validate([
            'value' => ['required', 'max:255'],
        ]);

        $attribute = Attribute::findOrFail($attribute_id);
        $attribute->values()->create([
            'attribute_id' => $attribute_id,
            'value' => $request->value,
        ]);

        return redirect()->back()->with('success', 'Attribute value added successfully.');
    }

    public function destroy($value_id)
    {
        $value = AttributeValue::findOrFail($value_id);
        $value->delete();

        return redirect()->back()->with('success', 'Attribute value deleted successfully.');
    }
}
