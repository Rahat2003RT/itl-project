<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\Category;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::all();
        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        $categories = Category::whereNotNull('parent_id')->get();
        return view('admin.attributes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'type' => ['required', 'string', 'in:Общая характеристика,Дополнительная характеристика,Техническая характеристика'],
            'category' => ['required', 'exists:categories,id'],
        ]);
    
        Attribute::create([
            'name' => $request->name,
            'type' => $request->type,
            'category_id' => $request->category,
        ]);
    
        return redirect()->route('admin.attributes.index')->with('success', 'Атрибут успешно добавлен.');
    }

    public function edit(Attribute $attribute)
    {
        $categories = Category::whereNotNull('parent_id')->get();
        return view('admin.attributes.edit', compact('attribute', 'categories'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => ['required', 'max:255'],
            'type' => ['required', 'string', 'in:Общая характеристика,Дополнительная характеристика,Техническая характеристика'],
            'category' => ['required', 'exists:categories,id'],
        ]);

        $attribute->update([
            'name' => $request->name,
            'type' => $request->type,
            'category_id' => $request->category,
        ]);

        return redirect()->route('admin.attributes.index')->with('success', 'Атрибут успешно обновлен.');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();

        return redirect()->route('admin.attributes.index')->with('success', 'Атрибут успешно удален.');
    }
}
