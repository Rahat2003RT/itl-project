<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PickupPoint;

class PickupPointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pickupPoints = PickupPoint::all();
        return view('admin.pickup_points.index', compact('pickupPoints'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pickup_points.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        PickupPoint::create($request->all());

        return redirect()->route('admin.pickup-points.index')
                         ->with('success', 'Pickup point created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PickupPoint $pickupPoint)
    {
        return view('admin.pickup_points.edit', compact('pickupPoint'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PickupPoint $pickupPoint)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $pickupPoint->update($request->all());

        return redirect()->route('admin.pickup-points.index')
                         ->with('success', 'Pickup point updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PickupPoint $pickupPoint)
    {
        $pickupPoint->delete();

        return redirect()->route('admin.pickup-points.index')
                         ->with('success', 'Pickup point deleted successfully.');
    }
}
