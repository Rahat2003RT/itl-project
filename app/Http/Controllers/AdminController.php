<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /////////////////////////////////////////////////////////////////////////////////////////////

    public function getAllUsers()
    {
        $users = DB::table('users')->paginate(5);
        return view('admin.users', ['users' => $users]);
    }

    public function updateUserRole(Request $request, $id)
    {
        $role = $request->input('role');
        DB::table('users')
            ->where('id', $id)
            ->update(['role' => $role]);

        return redirect()->back()->with('success', 'User role updated successfully.');
    }

    /////////////////////////////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////////////////////////

    public function getAllCategories()
    {
        $categories = Category::all();
        return view('admin.categories', compact('categories'));
    }

    public function addCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $request->id,
        ]);

        Category::create($request->all());

        return redirect()->route('admin.categories')->with('success', 'Category added successfully.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id,
        ]);

        if ($request->filled('parent_id')) {
            $parentCategory = Category::find($request->parent_id);
        
            // Проверяем, что у выбранной родительской категории нет собственных родителей
            if ($parentCategory->parent_id !== null) {
                return redirect()->back()->withErrors(['parent_id' => 'Cannot assign a child category to another child category.']);
            }
        
            // Проверяем, что сама привязываемая категория не является дочерней другой дочерней категории
            if (Category::where('parent_id', $category->id)->exists()) {
                return redirect()->back()->withErrors(['parent_id' => 'Cannot assign a parent category as a child of another category.']);
            }
        }
        

        $category->update($request->all());

        return redirect()->route('admin.categories')->with('success', 'Category updated successfully.');
    }

    public function destroyCategory(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories')->with('success', 'Category deleted successfully.');//пока не реализовал
    }

    /////////////////////////////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////////////////////////

    public function getAllBrands()
    {
        $brands = Brand::all();
        return view('admin.brands', compact('brands'));
    }

    public function addBrand(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Brand::create($request->all());

        return redirect()->route('admin.brands')->with('success', 'Brand added successfully.');
    }

    public function updateBrand(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $brand->update($request->all());

        return redirect()->route('admin.brands')->with('success', 'Brand updated successfully.');
    }

    public function destroyBrand(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('admin.brands')->with('success', 'Brand deleted successfully.');//пока не реализовал
    }

    /////////////////////////////////////////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////////////////////////////////////////////

    public function getAllProductsWithAllCategories()
    {
        $products = Product::with('categories', 'images', 'brand')->paginate(10);
        $categories = Category::all();
        $brands = Brand::all();
        
        return view('admin.products', compact('products', 'categories', 'brands'));
    }

    public function addProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::create($request->only('name', 'description', 'price', 'brand_id'));

        if ($request->has('categories')) {
            $product->categories()->sync($request->categories);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('product_images', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $path,
                ]);
            }
        }

        return redirect()->route('admin.products')->with('success', 'Product added successfully.');
    }

    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Update product details
        $product->update($request->only('name', 'description', 'price', 'brand_id'));
        
    
        // Update categories
        if ($request->has('categories')) {
            $product->categories()->sync($request->categories);
        }
    
        // Update images
        if ($request->hasFile('images')) {
            // Delete old images if new images are uploaded
            foreach ($product->images as $image) {
                // Delete the image file from storage
                Storage::disk('public')->delete($image->image_url);
                // Delete the image record from database
                $image->delete();
            }
    
            // Add new images
            foreach ($request->file('images') as $image) {
                $path = $image->store('product_images', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $path,
                ]);
            }
        }
    
        return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
    }
    

    public function destroyProduct(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully.');
    }

    public function removeProductImage($id)
    {
        try {
            $image = ProductImage::findOrFail($id);
            Storage::disk('public')->delete($image->image_url);
            $image->delete();
            return response()->json(['success' => true]);
        } catch (ModelNotFoundException $e) {
            Log::error("Product image with ID $id not found.");
            return response()->json(['error' => 'Image not found'], 404);
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////

}
