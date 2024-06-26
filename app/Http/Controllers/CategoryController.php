<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->paginate();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());
        return redirect()->route('admin.categories.index')->with('success', 'Category added successfully.');
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $request->validated();

        if ($request->filled('parent_id')) {
            $newParent = Category::findOrFail($request->parent_id);

            if ($newParent->isDescendantOf($category)) {
                return redirect()->back()->withErrors(['parent_id' => 'Cannot set parent category as descendant of itself.']);
            }
        }

        $category->update($request->only('name', 'parent_id'));
        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        // Удаляем категорию и все её дочерние категории рекурсивно
        $this->deleteCategoryAndChildren($category);
    
        return redirect()->route('admin.categories.index')->with('success', 'Category and its subcategories deleted successfully.');
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
