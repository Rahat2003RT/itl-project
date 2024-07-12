<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->paginate();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());
        return redirect()->route('admin.categories.index')->with('success', 'Категория успешно добавлена.');
    }

    public function edit($id)
    {
        $category = Category::find($id);
        $categories = Category::all();
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $request->validated();

        if ($request->filled('parent_id')) {
            $newParent = Category::findOrFail($request->parent_id);

            if ($newParent->isDescendantOf($category)) {
                return redirect()->back()->withErrors(['parent_id' => 'Невозможно установить родительскую категорию как потомка самой себя.']);
            }
        }

        $category->update($request->only('name', 'parent_id'));
        return redirect()->route('admin.categories.index')->with('success', 'Категория успешно обновлена.');
    }

    public function destroy(Category $category)
    {
        // Удаляем категорию и все её дочерние категории рекурсивно
        $this->deleteCategoryAndChildren($category);
    
        return redirect()->route('admin.categories.index')->with('success', 'Категория и ее подкатегории успешно удалены.');
    }
    
    private function deleteCategoryAndChildren(Category $category)
    {
        // Удаляем все продукты в текущей категории
        $this->deleteProductsInCategory($category);
    
        // Удаляем все дочерние категории
        foreach ($category->children as $child) {
            $this->deleteCategoryAndChildren($child);
        }
    
        // Удаляем саму категорию
        $category->delete();
    }
    
    private function deleteProductsInCategory(Category $category)
    {
        // Предполагаем, что у вас есть связь products в модели Category
        foreach ($category->products as $product) {
            $product->delete();
        }
    }
}
