<?php
// app/Http/Controllers/Admin/VehicleTypeController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Modules\UserRolePermission\app\Models\Driver;

class VehicleTypeController extends Controller
{
    public function index()
    {
        $vehicleTypes = VehicleType::withCount('drivers')
            ->latest()
            ->get();

        $drivers = Driver::with('user', 'vehicleType')
            ->where('status', 'active')
            ->get();

        return view('backend.vehicle-types.index', compact('vehicleTypes', 'drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100|unique:vehicle_types,name',
            'max_capacity' => 'required|integer|min:1|max:50',
            'description'  => 'nullable|string|max:255',
            'status'       => 'required|in:active,inactive',
        ]);

        VehicleType::create($validated);

        return redirect()
            ->route('admin.vehicle.types.index')
            ->with('success', 'Vehicle type created.');
    }

    public function update(Request $request, VehicleType $vehicleType)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100|unique:vehicle_types,name,' . $vehicleType->id,
            'max_capacity' => 'required|integer|min:1|max:50',
            'description'  => 'nullable|string|max:255',
            'status'       => 'required|in:active,inactive',
        ]);

        $vehicleType->update($validated);

        return redirect()
            ->route('admin.vehicle.types.index')
            ->with('success', 'Vehicle type updated.');
    }

    public function assignToDriver(Request $request)
    {
        $request->validate([
            'driver_id'       => 'required|exists:drivers,id',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
        ]);

        Driver::where('id', $request->driver_id)
            ->update(['vehicle_type_id' => $request->vehicle_type_id]);

        return redirect()
            ->route('admin.vehicle.types.index')
            ->with('success', 'Vehicle type assigned to driver.');
    }

    public function destroy(VehicleType $vehicleType)
    {
        // Unassign drivers before deleting
        Driver::where('vehicle_type_id', $vehicleType->id)
            ->update(['vehicle_type_id' => null]);

        $vehicleType->delete();

        return redirect()
            ->route('admin.vehicle.types.index')
            ->with('success', 'Vehicle type deleted.');
    }
}
