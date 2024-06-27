<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::paginate();
        return view('admin.brands.index', ['brands' => $brands]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            Brand::create($request->all());

            return redirect()->route('admin.brands.index')->with('success', 'Бренд успешно добавлен.');
        } catch (\Exception $e) {
            return redirect()->route('admin.brands.index')->with('error', 'Не удалось добавить бренд: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $brand->update($request->all());

            return redirect()->route('admin.brands.index')->with('success', 'Бренд успешно обновлен.');
        } catch (\Exception $e) {
            return redirect()->route('admin.brands.index')->with('error', 'Не удалось обновить бренд: ' . $e->getMessage());
        }
    }

    public function destroy(Brand $brand)
    {
        try {
            $brand->delete();

            return redirect()->route('admin.brands.index')->with('success', 'Бренд успешно удален.');
        } catch (\Exception $e) {
            return redirect()->route('admin.brands.index')->with('error', 'Не удалось удалить бренд: ' . $e->getMessage());
        }
    }
}
