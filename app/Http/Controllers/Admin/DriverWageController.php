<?php
// app/Http/Controllers/Admin/DriverWageController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverWage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\UserRolePermission\app\Models\Driver;

class DriverWageController extends Controller
{
    public function index()
    {
        $wages = DriverWage::with('driver.user', 'createdBy')
            ->latest()
            ->paginate(20);

        $drivers = Driver::with('user')
            ->where('status', 'active')
            ->get();

        return view('backend.driver-wages.index', compact('wages', 'drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'driver_id'      => 'required|exists:drivers,id',
            'rate_type'      => 'required|in:daily,hourly',
            'rate_amount'    => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to'   => 'nullable|date|after:effective_from',
            'status'         => 'required|in:active,inactive',
            'notes'          => 'nullable|string|max:500',
        ]);

        // Deactivate any existing active wage for same driver+type overlap
        DriverWage::where('driver_id', $validated['driver_id'])
            ->where('status', 'active')
            ->whereNull('effective_to')
            ->update(['effective_to' => now()->toDateString(), 'status' => 'inactive']);

        DriverWage::create([
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.driver.wages.index')
            ->with('success', 'Driver wage saved successfully.');
    }

    public function update(Request $request, DriverWage $wage)
    {
        $validated = $request->validate([
            'driver_id'      => 'required|exists:drivers,id',
            'rate_type'      => 'required|in:daily,hourly',
            'rate_amount'    => 'required|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to'   => 'nullable|date|after:effective_from',
            'status'         => 'required|in:active,inactive',
            'notes'          => 'nullable|string|max:500',
        ]);

        $wage->update($validated);

        return redirect()
            ->route('admin.driver.wages.index')
            ->with('success', 'Driver wage updated.');
    }

    public function destroy(DriverWage $wage)
    {
        $wage->delete();

        return redirect()
            ->route('admin.driver.wages.index')
            ->with('success', 'Wage record deleted.');
    }
}
