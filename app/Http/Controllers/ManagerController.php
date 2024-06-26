<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;


class ManagerController extends Controller
{
    public function dashboard()
    {
        return view('manager.dashboard');
    }

    public function index()
    {
        $products = Product::where('created_by', auth()->id())->with('categories', 'images', 'brand')->paginate(10);
        $categories = Category::getChildCategories();
        $brands = Brand::all();
        
        return view('manager.products.index', compact('products', 'categories', 'brands'));
    }

    public function store(Request $request)
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

        $product->created_by = auth()->id();
    
        $product->save();

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

        return redirect()->route('manager.products.index')->with('success', 'Product added successfully.');
    }

    public function update(Request $request, Product $product)
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

        $product->created_by = auth()->id();
    
        $product->save();
    
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
    
            foreach ($request->file('images') as $image) {
                // Генерируем уникальное имя файла
                $fileName = uniqid() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('product_images', $fileName, 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $path,
                ]);
            }
        }
    
        return redirect()->route('manager.products.index')->with('success', 'Product updated successfully.');
    }
    

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('manager.products.index')->with('success', 'Product deleted successfully.');
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
}
