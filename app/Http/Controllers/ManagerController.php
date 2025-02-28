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
        $products = Product::where('created_by', auth()->id())->with(['categories','brand', 'images'  => function ($query) {
            $query->orderBy('order', 'asc');
        }])->paginate(10);
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
            'category' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_order' => 'nullable|string',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category,
            'brand_id' => $request->brand_id,
            'created_by' => auth()->id(),
        ]);
    

        // Handle images
        if ($request->hasFile('images')) {
            $order = explode(',', $request->image_order);
            $files = $request->file('images');
    
            foreach ($order as $index) {
                if (isset($files[$index])) {
                    $file = $files[$index];
                    $path = $file->store('product_images', 'public');

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image_url = $path;
                    $productImage->order = $index; // Set the correct order
                    $productImage->save();
                }
            }
        }

        return redirect()->route('manager.products.index')->with('success', 'Product added successfully.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id); // Получаем продукт по идентификатору
        // Здесь можете передать категории, бренды и другие данные, необходимые для формы редактирования

        $categories = Category::getChildCategories();
        $brands = Brand::all();
        
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'image_order' => 'nullable|string',
            'image_id' => 'nullable|string',
        ]);

        // Обновляем основные данные продукта
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category, 
            'brand_id' => $request->brand_id,
        ]);

        $order = explode(',', $request->image_order);
        $ids = explode(',', $request->image_id);

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $imagesToDelete = $product->images;

            // Проходимся по каждому изображению
            foreach ($imagesToDelete as $imageToDelete) {
                // Удаляем файл изображения с диска
                Storage::disk('public')->delete($imageToDelete->image_url);
                
                // Удаляем запись изображения из базы данных
                $imageToDelete->delete();
            }
            $position = 1;

            foreach ($order as $index) {
                if (isset($files[$index])) {
                    $file = $files[$index];
                    $path = $file->store('product_images', 'public');

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image_url = $path;
                    $productImage->order = $index; // Set the correct order
                    $productImage->save();
                }
            }
    
        } else{
            // Обработка только изображений (без новых файлов)
            $existingImageIds = $product->images->pluck('id')->toArray();
            $newOrder = [];
            $position = 1;

            foreach ($ids as $index => $id) {
                // Существующие изображения
                $imageId = (int)$id;
                if (in_array($imageId, $existingImageIds)) {
                    $image = $product->images()->find($imageId);
                    if ($image) {
                        $image->update(['order' => $position]); // Используем позицию для существующих изображений
                    }
                }
                $newOrder[] = $imageId;
                $position++;
            }

            // Удаляем изображения, которые не в новом порядке
            $imagesToDelete = array_diff($existingImageIds, $newOrder);
            foreach ($imagesToDelete as $imageIdToDelete) {
                $imageToDelete = $product->images()->find($imageIdToDelete);
                if ($imageToDelete) {
                    Storage::disk('public')->delete($imageToDelete->image_url); // Удаляем файл изображения
                    $imageToDelete->delete(); // Удаляем запись изображения из базы данных
                }
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
