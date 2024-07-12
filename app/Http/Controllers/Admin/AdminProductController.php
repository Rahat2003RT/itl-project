<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use App\Models\Attribute;
use App\Models\ProductAttribute;

class AdminProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'images' => function ($query) {
            $query->orderBy('order', 'asc'); // Сортировка по возрастанию порядка (можно выбрать 'desc' для сортировки по убыванию)
        }])->paginate(10);
        
        
        $categories = Category::getChildCategories();
        $brands = Brand::all();
        
        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = Category::getChildCategories();
        $brands = Brand::all();
        return view('admin.products.create', compact('categories', 'brands'));
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
    
        return redirect()->route('admin.products.index')->with('success', 'Товар успешно добавлен.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:9192',
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

        return redirect()->route('admin.products.index')->with('success', 'Продукт успешно обновлен.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function manage(Product $product)
    {
        // Получить категории, связанные с продуктом
        $category = $product->category;

        // Получить атрибуты с их значениями, связанные с категорией продукта
        $attributes = Attribute::with('values')->where('category_id', $category->id)->get();

        // Инициализация массива для хранения значений атрибутов
        $attributeValues = [];
        
        foreach ($attributes as $attribute) {
            $attributeValues[$attribute->id] = $attribute->values;
        }
        
        // Передать продукт и его категории в представление
        return view('admin.products.manage', compact('product', 'category', 'attributes', 'attributeValues'));
    }

    public function manageUpdate(Request $request, $productId)
    {
        // Получение продукта
        $product = Product::findOrFail($productId);
    
        // Проход по всем атрибутам, которые были переданы из формы
        foreach ($request->input('attributes', []) as $attributeId => $attributeData) {
            $attributeValueId = $attributeData['attribute_value_id'];
    
            // Проверка, было ли передано значение атрибута
            if ($attributeValueId !== null) {
                // Обновляем существующую запись или создаем новую
                ProductAttribute::updateOrCreate(
                    ['product_id' => $product->id, 'attribute_id' => $attributeId],
                    ['attribute_value_id' => $attributeValueId]
                );
            } else {
                // Удаление записи, если передано null
                ProductAttribute::where('product_id', $product->id)
                    ->where('attribute_id', $attributeId)
                    ->delete();
            }
        }
    
        return redirect()->route('admin.products.index')->with('success', 'Product attributes updated successfully.');
    }
}
