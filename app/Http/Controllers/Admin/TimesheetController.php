<?php
// app/Http/Controllers/Admin/TimesheetController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\UserRolePermission\app\Models\Driver;

class TimesheetController extends Controller
{
    public function index(Request $request)
    {
        $query = Timesheet::with('driver.user', 'ride', 'approvedBy')
            ->latest('date');

        // Filters
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->to);
        }

        $timesheets = $query->paginate(20);

        $drivers       = Driver::with('user')->where('status', 'active')->get();
        $pendingCount  = Timesheet::where('status', 'pending')->count();
        $approvedCount = Timesheet::where('status', 'approved')
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        $rejectedCount = Timesheet::where('status', 'rejected')->count();
        $totalHours    = Timesheet::where('status', 'approved')
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('hours_worked');

        return view('backend.timesheets.index', compact(
            'timesheets',
            'drivers',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalHours'
        ));
    }

    public function show(Timesheet $timesheet)
    {
        $timesheet->load('driver.user', 'ride', 'approvedBy');

        return view('backend.timesheets.show', compact('timesheet'));
    }

    public function approve(Timesheet $timesheet)
    {
        abort_if($timesheet->status !== 'pending', 403, 'Only pending timesheets can be approved.');

        $timesheet->approve(Auth::id());

        return redirect()
            ->route('admin.timesheets.index')
            ->with('success', 'Timesheet approved and queued for payroll.');
    }

    public function reject(Request $request, Timesheet $timesheet)
    {
        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        abort_if($timesheet->status !== 'pending', 403, 'Only pending timesheets can be rejected.');

        $timesheet->reject(Auth::id(), $request->notes);

        return redirect()
            ->route('admin.timesheets.index')
            ->with('success', 'Timesheet rejected. Driver has been notified.');
    }
}
