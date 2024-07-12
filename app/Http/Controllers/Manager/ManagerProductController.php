<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Http\Request;

class ManagerProductController extends Controller
{
    public function index()
    {
        $collections = Collection::with('user')->get();
        return view('manager.collections.index', compact('collections'));
    }

    public function create()
    {
        return view('manager.collections.create');
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

        return redirect()->route('manager.collections.index')->with('success', 'Collection created successfully.');
    }

    public function show(Collection $collection)
    {
        // Загружаем связанные продукты
        $products = $collection->products()->with('images')->get();

        return view('manager.collections.show', compact('collection', 'products'));
    }

    public function edit(Collection $collection)
    {
        return view('manager.collections.edit', compact('collection'));
    }

    public function update(Request $request, Collection $collection)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $collection->update($request->only('name', 'description'));

        return redirect()->route('manager.collections.index')->with('success', 'Collection updated successfully.');
    }

    public function manage(Collection $collection)
    {
        // Загружаем продукты, принадлежащие текущему пользователю
        $products = Product::where('created_by', auth()->id())->get();
        return view('manager.collections.manage', compact('collection', 'products'));
    }

    public function manageUpdate(Request $request, Collection $collection)
    {
        $productIds = $request->input('products', []);
    
        // Ограничиваем продукты только теми, которые принадлежат текущему пользователю
        $validProductIds = Product::where('created_by', auth()->id())
            ->whereIn('id', $productIds)
            ->pluck('id')
            ->toArray();
    
        $collection->products()->sync($validProductIds);
    
        return redirect()->route('manager.collections.index')->with('success', 'Collection products updated successfully.');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();
        return redirect()->route('manager.collections.index')->with('success', 'Collection deleted successfully.');
    }
}
