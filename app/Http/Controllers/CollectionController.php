<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    public function index()
    {
        $collections = Collection::with('user')->get();
        return view('admin.collections.index', compact('collections'));
    }

    public function create()
    {
        return view('admin.collections.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $collection = new Collection();
        $collection->name = $request->name;
        $collection->description = $request->description;
        $collection->user_id = auth()->id(); // Assuming you want to set the creator of the collection
        $collection->save();

        return redirect()->route('admin.collections.index')->with('success', 'Collection created successfully.');
    }

    public function show(Collection $collection)
    {
        // Загружаем связанные продукты
        $products = $collection->products()->with('images')->get();

        return view('admin.collections.show', compact('collection', 'products'));
    }

    public function edit(Collection $collection)
    {
        return view('admin.collections.edit', compact('collection'));
    }

    public function update(Request $request, Collection $collection)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $collection->update($request->only('name', 'description'));

        return redirect()->route('admin.collections.index')->with('success', 'Collection updated successfully.');
    }

    public function manage(Collection $collection)
    {
        $products = Product::all();
        return view('admin.collections.manage', compact('collection', 'products'));
    }

    public function manageUpdate(Request $request, Collection $collection)
    {
        $productIds = $request->input('products', []);
        $collection->products()->sync($productIds);

        return redirect()->route('admin.collections.index')->with('success', 'Collection products updated successfully.');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();
        return redirect()->route('admin.collections.index')->with('success', 'Collection deleted successfully.');
    }
}
