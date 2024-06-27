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
            'category' => ['required', 'exists:categories,id'], // Валидация существования категории
        ]);
    
        Attribute::create([
            'name' => $request->name,
            'type' => $request->type,
            'category_id' => $request->category,
        ]);
    
        return redirect()->route('admin.attributes.index')->with('success', 'Attribute added successfully.');
    }
    
    
}
