<?php

// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total counts
        $totalParents = User::role('parent')->count();
        $totalChildren = DB::table('kids')->count();
        $activeParents = User::role('parent')->where('status', 'active')->count();
        $newRegistrations = User::role('parent')->whereDate('created_at', '>=', now()->subDays(30))->count();

        // Monthly registrations for chart
        $monthlyRegistrations = User::role('parent')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Gender distribution
        $genderStats = User::role('parent')
            ->select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->get();

        // Country wise distribution
        $countryStats = User::role('parent')
            ->select('countries.name as country', DB::raw('count(*) as count'))
            ->join('countries', 'users.country_id', '=', 'countries.id')
            ->groupBy('countries.name')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // Recent registrations
        $recentParents = User::role('parent')
            ->with(['country', 'state', 'city'])
            ->latest()
            ->limit(10)
            ->get();

        return view('backend.dashboard', compact(
            'totalParents',
            'totalChildren',
            'activeParents',
            'newRegistrations',
            'monthlyRegistrations',
            'genderStats',
            'countryStats',
            'recentParents'
        ));
    }
}
