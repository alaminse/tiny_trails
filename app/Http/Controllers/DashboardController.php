<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ShiftBroadcast;
use Modules\RideAssignment\app\Models\Ride;
use Modules\UserRolePermission\app\Models\Driver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Cache TTL — 5 minutes.
     * Set to 0 to disable during development.
     */
    private const CACHE_TTL = 300;

    // ══════════════════════════════════════════════════════════════════
    // Main Dashboard
    // ══════════════════════════════════════════════════════════════════

    public function index()
    {
        $user = Auth::user();

        $data = match (true) {
            $user->hasRole('Super-Admin')   => $this->superAdminStats(),
            $user->hasRole('BOH-IT')        => $this->bohItStats(),
            $user->hasRole('BOH-Marketing') => $this->bohMarketingStats(),
            $user->hasRole('BOH-Sales')     => $this->bohSalesStats(),
            $user->hasRole('BOH-Support')   => $this->bohSupportStats(),
            default                         => $this->superAdminStats(),
        };

        return view('backend.dashboard', $data);
    }

    // ══════════════════════════════════════════════════════════════════
    // BOH Live Dashboard  (no cache — real-time)
    // Mirrors BohDashboardController logic, consolidated here
    // ══════════════════════════════════════════════════════════════════

    public function bohLive()
    {
        // ── Driver list with today's active rides ───────────────────
        $drivers = Driver::with(['user', 'vehicleType'])
            ->where('status', 'active')
            ->withCount([
                'rides as today_rides_count' => fn($q) => $q
                    ->whereDate('date', today())
                    ->whereNotIn('status', ['cancelled', 'completed']),
            ])
            ->get();

        $activeDrivers = $drivers->whereIn('availability_status', [
            'on_trip', 'available', 'ready_next_batch',
        ])->count();

        $delayedCount = $drivers->where('availability_status', 'delayed')->count();

        // ── Rides today (rides table) ───────────────────────────────
        $ridesToday     = Ride::whereDate('date', today())->count();
        $completedToday = Ride::whereDate('date', today())->where('status', 'completed')->count();
        $cancelledToday = Ride::whereDate('date', today())->where('status', 'cancelled')->count();
        $inProgressNow  = Ride::where('status', 'in_progress')->count();

        // ── Shift broadcasts (shift_broadcasts table) ───────────────
        $pendingBroadcasts = ShiftBroadcast::where('status', 'open')->count();
        $broadcastsToday   = ShiftBroadcast::whereDate('created_at', today())->count();

        // ── Shift acceptances pending ───────────────────────────────
        $pendingAcceptances = DB::table('shift_acceptances')
            ->where('status', 'pending')->count();

        // ── Driver on-trip > 40 min alert ───────────────────────────
        $delayedAlert  = null;
        $delayedDriver = Ride::with('driver.user')
            ->where('status', 'in_progress')
            ->where('updated_at', '<', now()->subMinutes(40))
            ->first();

        if ($delayedDriver) {
            $name         = optional(optional($delayedDriver->driver)->user)->first_name ?? 'Unknown';
            $delayedAlert = "Driver {$name} has been \"On Trip\" for over 40 mins — manual check recommended.";
        }

        // ── Attendance proxy ────────────────────────────────────────
        // No Attendance model — derived from driver_shifts + shift_drivers
        // + drivers.face_verified_at (same logic as AttendanceController)
        $presentToday = DB::table('driver_shifts as ds')
            ->join('shift_drivers as sd', 'sd.driver_shift_id', '=', 'ds.id')
            ->join('drivers as d', 'd.id', '=', 'sd.driver_id')
            ->whereDate('ds.date', today())
            ->whereNull('ds.deleted_at')
            ->whereDate('d.face_verified_at', today())
            ->distinct('sd.driver_id')
            ->count('sd.driver_id');

        $driversOnShiftToday = DB::table('driver_shifts as ds')
            ->join('shift_drivers as sd', 'sd.driver_shift_id', '=', 'ds.id')
            ->whereDate('ds.date', today())
            ->whereNull('ds.deleted_at')
            ->distinct('sd.driver_id')
            ->count('sd.driver_id');

        // ── Face verification pending ───────────────────────────────
        // drivers.is_verified = 0  OR  face_verified_until < now
        $pendingVerifications = Driver::whereNull('deleted_at')
            ->where(fn($q) => $q
                ->where('is_verified', 0)
                ->orWhere('face_verified_until', '<', now())
            )->count();

        // ── Pending timesheets ──────────────────────────────────────
        $pendingTimesheets = DB::table('timesheets')
            ->where('status', 'pending')->count();

        // ── Recent rides ────────────────────────────────────────────
        $recentRides = Ride::with('driver.user')
            ->latest()->limit(10)->get();

        return view('backend.boh-live-dashboard', compact(
            'drivers',
            'activeDrivers',
            'ridesToday',
            'completedToday',
            'cancelledToday',
            'inProgressNow',
            'pendingBroadcasts',
            'broadcastsToday',
            'pendingAcceptances',
            'delayedCount',
            'delayedAlert',
            'presentToday',
            'driversOnShiftToday',
            'pendingVerifications',
            'pendingTimesheets',
            'recentRides'
        ));
    }

    // ══════════════════════════════════════════════════════════════════
    // Role-scoped stat builders
    // ══════════════════════════════════════════════════════════════════

    // ── Super-Admin ───────────────────────────────────────────────────
    private function superAdminStats(): array
    {
        return Cache::remember('dashboard:super-admin', self::CACHE_TTL, function () {

            $parentRoleId = DB::table('roles')->where('name', 'parent')->value('id');

            // ── Users ───────────────────────────────────────────────
            $totalUsers  = DB::table('users')->whereNull('deleted_at')->count();
            $activeUsers = DB::table('users')->whereNull('deleted_at')->where('status', 'active')->count();

            // ── Parents ─────────────────────────────────────────────
            $totalParents  = DB::table('model_has_roles')->where('role_id', $parentRoleId)->count();
            $activeParents = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->where('model_has_roles.role_id', $parentRoleId)
                ->where('users.status', 'active')->whereNull('users.deleted_at')->count();
            $newParents30d = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->where('model_has_roles.role_id', $parentRoleId)
                ->where('users.created_at', '>=', now()->subDays(30))
                ->whereNull('users.deleted_at')->count();

            // ── Kids ─────────────────────────────────────────────────
            $totalKids  = DB::table('kids')->whereNull('deleted_at')->count();
            // "Active kids" = kids with an active subscription
            $activeKids = DB::table('subscriptions')->whereNull('deleted_at')
                ->where('status', 'active')->distinct('kid_id')->count('kid_id');
            $kidsWithoutSub = DB::table('kids')->whereNull('deleted_at')
                ->whereNotIn('id', DB::table('subscriptions')->whereNull('deleted_at')
                    ->where('status', 'active')->pluck('kid_id')
                )->count();

            // ── Drivers ─────────────────────────────────────────────
            $totalDrivers    = DB::table('drivers')->whereNull('deleted_at')->count();
            $activeDrivers   = DB::table('drivers')->whereNull('deleted_at')->where('status', 'active')->count();
            $verifiedDrivers = DB::table('drivers')->whereNull('deleted_at')
                ->where('is_verified', 1)->where('face_verified_until', '>=', now())->count();
            $newDrivers30d   = DB::table('drivers')->whereNull('deleted_at')
                ->where('created_at', '>=', now()->subDays(30))->count();

            // ── Plans ────────────────────────────────────────────────
            $totalPlans  = DB::table('plans')->whereNull('deleted_at')->count();
            $activePlans = DB::table('plans')->whereNull('deleted_at')->where('status', 'active')->count();

            // ── Subscriptions ────────────────────────────────────────
            $totalSubscriptions  = DB::table('subscriptions')->whereNull('deleted_at')->count();
            $activeSubscriptions = DB::table('subscriptions')->whereNull('deleted_at')->where('status', 'active')->count();
            $expiringSoon        = DB::table('subscriptions')->whereNull('deleted_at')
                ->where('status', 'active')->whereBetween('ends_at', [now(), now()->addDays(7)])->count();
            $trialActive         = DB::table('subscriptions')->whereNull('deleted_at')
                ->where('status', 'active')->where('trial_ends_at', '>=', now())->count();

            // ── Revenue (payway_transactions) ────────────────────────
            $monthlyRevenue = DB::table('payway_transactions')
                ->where('status', 'approved')->whereMonth('processed_at', now()->month)
                ->sum('amount');
            $totalRevenue   = DB::table('payway_transactions')
                ->where('status', 'approved')->sum('amount');

            // ── Rides ────────────────────────────────────────────────
            $totalRides     = DB::table('rides')->whereNull('deleted_at')->count();
            $completedRides = DB::table('rides')->whereNull('deleted_at')->where('status', 'completed')->count();
            $cancelledRides = DB::table('rides')->whereNull('deleted_at')->where('status', 'cancelled')->count();
            $inProgressNow  = DB::table('rides')->where('status', 'in_progress')->count();
            $todayRides     = DB::table('rides')->whereDate('date', today())->count();

            // ── Ride Assigns (ride_assigns table) ────────────────────
            $totalRideAssigns   = DB::table('ride_assigns')->whereNull('deleted_at')->count();
            $pendingAssignments = DB::table('ride_assigns')->whereNull('deleted_at')->where('status', 'pending')->count();

            // ── Driver Shifts ────────────────────────────────────────
            $shiftsToday      = DB::table('driver_shifts')->whereNull('deleted_at')->whereDate('date', today())->count();
            $shiftsThisWeek   = DB::table('driver_shifts')->whereNull('deleted_at')
                ->whereBetween('date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
                ->count();

            // ── Shift Broadcasts ─────────────────────────────────────
            $openBroadcasts     = DB::table('shift_broadcasts')->whereNull('deleted_at')->where('status', 'open')->count();
            $broadcastsToday    = DB::table('shift_broadcasts')->whereNull('deleted_at')->whereDate('created_at', today())->count();
            $pendingAcceptances = DB::table('shift_acceptances')->where('status', 'pending')->count();

            // ── Timesheets ───────────────────────────────────────────
            $pendingTimesheets = DB::table('timesheets')->where('status', 'pending')->count();
            $approvedThisMonth = DB::table('timesheets')->where('status', 'approved')
                ->whereMonth('date', now()->month)->count();

            // ── Attendance proxy (driver_shifts + face_verified_at) ──
            // Present = driver on shift today AND face_verified_at is today
            $presentToday = DB::table('driver_shifts as ds')
                ->join('shift_drivers as sd', 'sd.driver_shift_id', '=', 'ds.id')
                ->join('drivers as d', 'd.id', '=', 'sd.driver_id')
                ->whereDate('ds.date', today())->whereNull('ds.deleted_at')
                ->whereDate('d.face_verified_at', today())
                ->distinct('sd.driver_id')->count('sd.driver_id');

            // ── Driver Wages ─────────────────────────────────────────
            $activeWages  = DB::table('driver_wages')->whereNull('deleted_at')->where('status', 'active')->count();
            $pendingWages = DB::table('driver_wages')->whereNull('deleted_at')->where('status', 'pending')->count();

            // ── Kid Wages ────────────────────────────────────────────
            $activeKidWages = DB::table('kid_wages')->whereNull('deleted_at')->where('status', 'active')->count();

            // ── Vehicle & Pickup Types ───────────────────────────────
            $totalVehicleTypes = DB::table('vehicle_types')->whereNull('deleted_at')->count();
            $totalPickupTypes  = DB::table('pickup_types')->whereNull('deleted_at')->count();

            // ── Face Verification ────────────────────────────────────
            $pendingVerifications = DB::table('drivers')->whereNull('deleted_at')
                ->where(fn($q) => $q->where('is_verified', 0)->orWhere('face_verified_until', '<', now()))->count();

            // ── Location ─────────────────────────────────────────────
            $totalCountries = DB::table('countries')->whereNull('deleted_at')->count();
            $totalStates    = DB::table('states')->whereNull('deleted_at')->count();
            $totalCities    = DB::table('cities')->whereNull('deleted_at')->count();

            // ── Charts ───────────────────────────────────────────────
            $charts = $this->buildCharts();

            // ── Recent records ───────────────────────────────────────
            $recentParents = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->where('model_has_roles.role_id', $parentRoleId)
                ->whereNull('users.deleted_at')->orderByDesc('users.created_at')->limit(10)
                ->select('users.id','users.first_name','users.last_name','users.email','users.phone','users.status','users.created_at')
                ->get();

            $recentSubscriptions = DB::table('subscriptions')
                ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->join('users', 'subscriptions.user_id', '=', 'users.id')
                ->whereNull('subscriptions.deleted_at')->orderByDesc('subscriptions.created_at')->limit(5)
                ->select('subscriptions.id','subscriptions.status','subscriptions.ends_at','subscriptions.created_at',
                         'plans.name as plan_name','users.first_name','users.last_name')
                ->get();

            $recentRides = DB::table('rides')
                ->join('drivers', 'rides.driver_id', '=', 'drivers.id')
                ->join('users as dusers', 'drivers.user_id', '=', 'dusers.id')
                ->join('users as parents', 'rides.parent_id', '=', 'parents.id')
                ->whereNull('rides.deleted_at')->orderByDesc('rides.created_at')->limit(5)
                ->select('rides.id','rides.status','rides.date','rides.ride_type',
                         'dusers.first_name as driver_first','dusers.last_name as driver_last',
                         'parents.first_name as parent_first','parents.last_name as parent_last')
                ->get();

            return array_merge(compact(
                'totalUsers','activeUsers',
                'totalParents','activeParents','newParents30d',
                'totalKids','activeKids','kidsWithoutSub',
                'totalDrivers','activeDrivers','verifiedDrivers','newDrivers30d',
                'totalPlans','activePlans',
                'totalSubscriptions','activeSubscriptions','expiringSoon','trialActive',
                'monthlyRevenue','totalRevenue',
                'totalRides','completedRides','cancelledRides','inProgressNow','todayRides',
                'totalRideAssigns','pendingAssignments',
                'shiftsToday','shiftsThisWeek',
                'openBroadcasts','broadcastsToday','pendingAcceptances',
                'pendingTimesheets','approvedThisMonth','presentToday',
                'activeWages','pendingWages','activeKidWages',
                'totalVehicleTypes','totalPickupTypes',
                'pendingVerifications',
                'totalCountries','totalStates','totalCities',
                'recentParents','recentSubscriptions','recentRides'
            ), $charts);
        });
    }

    // ── BOH-IT ────────────────────────────────────────────────────────
    private function bohItStats(): array
    {
        return Cache::remember('dashboard:boh-it', self::CACHE_TTL, function () {

            $parentRoleId = DB::table('roles')->where('name', 'parent')->value('id');

            return array_merge([
                'totalParents'         => DB::table('model_has_roles')->where('role_id', $parentRoleId)->count(),
                'totalKids'            => DB::table('kids')->whereNull('deleted_at')->count(),
                'totalDrivers'         => DB::table('drivers')->whereNull('deleted_at')->count(),
                'activeDrivers'        => DB::table('drivers')->whereNull('deleted_at')->where('status','active')->count(),
                'totalUsers'           => DB::table('users')->whereNull('deleted_at')->count(),
                'activeSubscriptions'  => DB::table('subscriptions')->whereNull('deleted_at')->where('status','active')->count(),
                'expiringSoon'         => DB::table('subscriptions')->whereNull('deleted_at')
                                            ->where('status','active')->whereBetween('ends_at',[now(),now()->addDays(7)])->count(),
                'totalRides'           => DB::table('rides')->whereNull('deleted_at')->count(),
                'todayRides'           => DB::table('rides')->whereDate('date',today())->count(),
                'pendingAssignments'   => DB::table('ride_assigns')->whereNull('deleted_at')->where('status','pending')->count(),
                'pendingTimesheets'    => DB::table('timesheets')->where('status','pending')->count(),
                'openBroadcasts'       => DB::table('shift_broadcasts')->whereNull('deleted_at')->where('status','open')->count(),
                'pendingVerifications' => DB::table('drivers')->whereNull('deleted_at')
                                            ->where(fn($q)=>$q->where('is_verified',0)->orWhere('face_verified_until','<',now()))->count(),
                'presentToday'         => DB::table('driver_shifts as ds')
                                            ->join('shift_drivers as sd','sd.driver_shift_id','=','ds.id')
                                            ->join('drivers as d','d.id','=','sd.driver_id')
                                            ->whereDate('ds.date',today())->whereNull('ds.deleted_at')
                                            ->whereDate('d.face_verified_at',today())
                                            ->distinct('sd.driver_id')->count('sd.driver_id'),
                'totalVehicleTypes'    => DB::table('vehicle_types')->whereNull('deleted_at')->count(),
                'totalPickupTypes'     => DB::table('pickup_types')->whereNull('deleted_at')->count(),
                'totalCountries'       => DB::table('countries')->whereNull('deleted_at')->count(),
                'totalStates'          => DB::table('states')->whereNull('deleted_at')->count(),
                'totalCities'          => DB::table('cities')->whereNull('deleted_at')->count(),
                'recentSubscriptions'  => DB::table('subscriptions')
                                            ->join('plans','subscriptions.plan_id','=','plans.id')
                                            ->join('users','subscriptions.user_id','=','users.id')
                                            ->whereNull('subscriptions.deleted_at')
                                            ->orderByDesc('subscriptions.created_at')->limit(5)
                                            ->select('subscriptions.id','subscriptions.status','plans.name as plan_name',
                                                     'users.first_name','users.last_name')->get(),
                'recentRides'          => DB::table('rides')->whereNull('rides.deleted_at')
                                            ->orderByDesc('rides.created_at')->limit(5)
                                            ->select('rides.id','rides.status','rides.date','rides.ride_type')->get(),
            ], $this->buildCharts());
        });
    }

    // ── BOH-Marketing ─────────────────────────────────────────────────
    private function bohMarketingStats(): array
    {
        return Cache::remember('dashboard:boh-marketing', self::CACHE_TTL, function () {
            return [
                'totalPlans'           => DB::table('plans')->whereNull('deleted_at')->count(),
                'activePlans'          => DB::table('plans')->whereNull('deleted_at')->where('status','active')->count(),
                'totalSubscriptions'   => DB::table('subscriptions')->whereNull('deleted_at')->count(),
                'activeSubscriptions'  => DB::table('subscriptions')->whereNull('deleted_at')->where('status','active')->count(),
                'expiringSoon'         => DB::table('subscriptions')->whereNull('deleted_at')
                                            ->where('status','active')->whereBetween('ends_at',[now(),now()->addDays(7)])->count(),
                'newSubscriptions30d'  => DB::table('subscriptions')->whereNull('deleted_at')
                                            ->where('created_at','>=',now()->subDays(30))->count(),
                // Revenue from payway_transactions — actual charged amounts
                'monthlyRevenue'       => DB::table('payway_transactions')
                                            ->where('status','approved')->whereMonth('processed_at',now()->month)
                                            ->sum('amount'),
                'totalRevenue'         => DB::table('payway_transactions')
                                            ->where('status','approved')->sum('amount'),
                'totalRides'           => DB::table('rides')->whereNull('deleted_at')->count(),
                'completedRides'       => DB::table('rides')->whereNull('deleted_at')->where('status','completed')->count(),
                'totalDrivers'         => DB::table('drivers')->whereNull('deleted_at')->count(),
                'activeDrivers'        => DB::table('drivers')->whereNull('deleted_at')->where('status','active')->count(),
                'totalVehicleTypes'    => DB::table('vehicle_types')->whereNull('deleted_at')->count(),
                'totalPickupTypes'     => DB::table('pickup_types')->whereNull('deleted_at')->count(),
                'subscriptionTrend'    => $this->subscriptionTrendChart(),
                'planDistribution'     => $this->planDistributionChart(),
                'revenueTrend'         => $this->revenueTrendChart(),
                'recentSubscriptions'  => DB::table('subscriptions')
                                            ->join('plans','subscriptions.plan_id','=','plans.id')
                                            ->whereNull('subscriptions.deleted_at')
                                            ->orderByDesc('subscriptions.created_at')->limit(10)
                                            ->select('subscriptions.id','subscriptions.status',
                                                     'subscriptions.ends_at','subscriptions.created_at',
                                                     'plans.name as plan_name','plans.sell_price')
                                            ->get(),
            ];
        });
    }

    // ── BOH-Sales ─────────────────────────────────────────────────────
    private function bohSalesStats(): array
    {
        return Cache::remember('dashboard:boh-sales', self::CACHE_TTL, function () {

            $parentRoleId = DB::table('roles')->where('name', 'parent')->value('id');

            return [
                'totalParents'        => DB::table('model_has_roles')->where('role_id', $parentRoleId)->count(),
                'newParents30d'       => DB::table('users')
                                            ->join('model_has_roles','users.id','=','model_has_roles.model_id')
                                            ->where('model_has_roles.role_id', $parentRoleId)
                                            ->where('users.created_at','>=',now()->subDays(30))
                                            ->whereNull('users.deleted_at')->count(),
                'newParents7d'        => DB::table('users')
                                            ->join('model_has_roles','users.id','=','model_has_roles.model_id')
                                            ->where('model_has_roles.role_id', $parentRoleId)
                                            ->where('users.created_at','>=',now()->subDays(7))
                                            ->whereNull('users.deleted_at')->count(),
                'totalKids'           => DB::table('kids')->whereNull('deleted_at')->count(),
                // Kids with NO active subscription (subscription_id not in active subs)
                'kidsWithoutSub'      => DB::table('kids')->whereNull('deleted_at')
                                            ->whereNotIn('id', DB::table('subscriptions')
                                                ->whereNull('deleted_at')->where('status','active')->pluck('kid_id')
                                            )->count(),
                'totalSubscriptions'  => DB::table('subscriptions')->whereNull('deleted_at')->count(),
                'activeSubscriptions' => DB::table('subscriptions')->whereNull('deleted_at')->where('status','active')->count(),
                'expiringSoon'        => DB::table('subscriptions')->whereNull('deleted_at')
                                            ->where('status','active')->whereBetween('ends_at',[now(),now()->addDays(7)])->count(),
                // ride_assigns.status = 'pending' means assigned but not yet confirmed
                'pendingAssignments'  => DB::table('ride_assigns')->whereNull('deleted_at')->where('status','pending')->count(),
                'todayRides'          => DB::table('rides')->whereDate('date',today())->count(),
                'totalPickupTypes'    => DB::table('pickup_types')->whereNull('deleted_at')->where('status','active')->count(),
                'registrationTrend'   => $this->registrationTrendChart(),
                'onboardingFunnel'    => $this->onboardingFunnelData(),
                'recentParents'       => DB::table('users')
                                            ->join('model_has_roles','users.id','=','model_has_roles.model_id')
                                            ->where('model_has_roles.role_id', $parentRoleId)
                                            ->whereNull('users.deleted_at')->orderByDesc('users.created_at')->limit(10)
                                            ->select('users.id','users.first_name','users.last_name',
                                                     'users.email','users.phone','users.status','users.created_at')
                                            ->get(),
                'recentKids'          => DB::table('kids')
                                            ->join('users','kids.user_id','=','users.id')
                                            ->whereNull('kids.deleted_at')->orderByDesc('kids.created_at')->limit(5)
                                            ->select('kids.id','kids.first_name','kids.last_name','kids.dob','kids.created_at',
                                                     'users.first_name as parent_first','users.last_name as parent_last')
                                            ->get(),
                'recentSubscriptions' => DB::table('subscriptions')
                                            ->join('plans','subscriptions.plan_id','=','plans.id')
                                            ->join('users','subscriptions.user_id','=','users.id')
                                            ->whereNull('subscriptions.deleted_at')
                                            ->orderByDesc('subscriptions.created_at')->limit(5)
                                            ->select('subscriptions.id','subscriptions.status',
                                                     'plans.name as plan_name','users.first_name','users.last_name')
                                            ->get(),
            ];
        });
    }

    // ── BOH-Support ───────────────────────────────────────────────────
    private function bohSupportStats(): array
    {
        return Cache::remember('dashboard:boh-support', self::CACHE_TTL, function () {

            $parentRoleId = DB::table('roles')->where('name', 'parent')->value('id');

            return [
                'totalKids'            => DB::table('kids')->whereNull('deleted_at')->count(),
                'totalParents'         => DB::table('model_has_roles')->where('role_id', $parentRoleId)->count(),
                'totalDrivers'         => DB::table('drivers')->whereNull('deleted_at')->count(),
                'inProgressNow'        => DB::table('rides')->where('status','in_progress')->count(),
                'todayRides'           => DB::table('rides')->whereDate('date',today())->count(),
                'pendingAssignments'   => DB::table('ride_assigns')->whereNull('deleted_at')->where('status','pending')->count(),
                'activeSubscriptions'  => DB::table('subscriptions')->whereNull('deleted_at')->where('status','active')->count(),
                'expiringSoon'         => DB::table('subscriptions')->whereNull('deleted_at')
                                            ->where('status','active')->whereBetween('ends_at',[now(),now()->addDays(7)])->count(),
                'pendingTimesheets'    => DB::table('timesheets')->where('status','pending')->count(),
                // Attendance proxy — present = on shift today AND face_verified today
                'presentToday'         => DB::table('driver_shifts as ds')
                                            ->join('shift_drivers as sd','sd.driver_shift_id','=','ds.id')
                                            ->join('drivers as d','d.id','=','sd.driver_id')
                                            ->whereDate('ds.date',today())->whereNull('ds.deleted_at')
                                            ->whereDate('d.face_verified_at',today())
                                            ->distinct('sd.driver_id')->count('sd.driver_id'),
                'absentToday'          => DB::table('driver_shifts as ds')
                                            ->join('shift_drivers as sd','sd.driver_shift_id','=','ds.id')
                                            ->join('drivers as d','d.id','=','sd.driver_id')
                                            ->whereDate('ds.date',today())->whereNull('ds.deleted_at')
                                            ->where(fn($q) => $q
                                                ->whereNull('d.face_verified_at')
                                                ->orWhereDate('d.face_verified_at','<>',today())
                                            )
                                            ->distinct('sd.driver_id')->count('sd.driver_id'),
                'pendingVerifications' => DB::table('drivers')->whereNull('deleted_at')
                                            ->where(fn($q)=>$q->where('is_verified',0)->orWhere('face_verified_until','<',now()))->count(),
                'totalPickupTypes'     => DB::table('pickup_types')->whereNull('deleted_at')->count(),
                'recentRides'          => DB::table('rides')
                                            ->join('drivers','rides.driver_id','=','drivers.id')
                                            ->join('users as dusers','drivers.user_id','=','dusers.id')
                                            ->join('users as parents','rides.parent_id','=','parents.id')
                                            ->whereNull('rides.deleted_at')->orderByDesc('rides.created_at')->limit(10)
                                            ->select('rides.id','rides.status','rides.date','rides.ride_type',
                                                     'dusers.first_name as driver_first','dusers.last_name as driver_last',
                                                     'parents.first_name as parent_first','parents.last_name as parent_last')
                                            ->get(),
                'recentParents'        => DB::table('users')
                                            ->join('model_has_roles','users.id','=','model_has_roles.model_id')
                                            ->where('model_has_roles.role_id', $parentRoleId)
                                            ->whereNull('users.deleted_at')->orderByDesc('users.created_at')->limit(5)
                                            ->select('users.id','users.first_name','users.last_name',
                                                     'users.email','users.phone','users.status')
                                            ->get(),
            ];
        });
    }

    // ══════════════════════════════════════════════════════════════════
    // Chart builders — all use raw DB queries against actual schema
    // ══════════════════════════════════════════════════════════════════

    private function buildCharts(): array
    {
        return [
            'registrationTrend' => $this->registrationTrendChart(),
            'genderStats'       => $this->genderStatsChart(),
            'countryStats'      => $this->countryStatsChart(),
            'subscriptionTrend' => $this->subscriptionTrendChart(),
            'planDistribution'  => $this->planDistributionChart(),
            'rideTrend'         => $this->rideTrendChart(),
            'revenueTrend'      => $this->revenueTrendChart(),
            'driverShiftStats'  => $this->driverShiftStatsChart(),
        ];
    }

    // Parent registrations last 6 months
    private function registrationTrendChart()
    {
        $parentRoleId = DB::table('roles')->where('name', 'parent')->value('id');
        return DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', $parentRoleId)
            ->where('users.created_at', '>=', now()->subMonths(6))
            ->whereNull('users.deleted_at')
            ->select(DB::raw('DATE_FORMAT(users.created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')->orderBy('month')->get();
    }

    // users.gender enum
    private function genderStatsChart()
    {
        $parentRoleId = DB::table('roles')->where('name', 'parent')->value('id');
        return DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('model_has_roles.role_id', $parentRoleId)
            ->whereNotNull('users.gender')->whereNull('users.deleted_at')
            ->select('users.gender', DB::raw('COUNT(*) as count'))
            ->groupBy('users.gender')->get();
    }

    // Top 5 countries — users.country_id → countries.id
    private function countryStatsChart()
    {
        $parentRoleId = DB::table('roles')->where('name', 'parent')->value('id');
        return DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('countries', 'users.country_id', '=', 'countries.id')
            ->where('model_has_roles.role_id', $parentRoleId)
            ->whereNull('users.deleted_at')
            ->select('countries.name as country', DB::raw('COUNT(*) as count'))
            ->groupBy('countries.name')->orderByDesc('count')->limit(5)->get();
    }

    // Subscription growth last 6 months
    private function subscriptionTrendChart()
    {
        return DB::table('subscriptions')->whereNull('deleted_at')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')->orderBy('month')->get();
    }

    // Active subscriptions by plan
    private function planDistributionChart()
    {
        return DB::table('subscriptions')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->whereNull('subscriptions.deleted_at')->where('subscriptions.status', 'active')
            ->select('plans.name as plan', DB::raw('COUNT(*) as count'))
            ->groupBy('plans.name')->orderByDesc('count')->get();
    }

    // Ride trend last 30 days — rides.date (date column, not timestamp)
    private function rideTrendChart()
    {
        return DB::table('rides')->whereNull('deleted_at')
            ->where('date', '>=', now()->subDays(30)->toDateString())
            ->select(
                'date',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(CASE WHEN status="completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN status="cancelled" THEN 1 ELSE 0 END) as cancelled')
            )
            ->groupBy('date')->orderBy('date')->get();
    }

    // Revenue from payway_transactions.amount where status=approved
    private function revenueTrendChart()
    {
        return DB::table('payway_transactions')
            ->where('status', 'approved')
            ->where('processed_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(processed_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as revenue'),
                DB::raw('SUM(principal_amount) as principal'),
                DB::raw('SUM(surcharge_amount) as surcharge')
            )
            ->groupBy('month')->orderBy('month')->get();
    }

    // Driver shift capacity vs booked this week
    private function driverShiftStatsChart()
    {
        return DB::table('driver_shifts')->whereNull('deleted_at')
            ->whereBetween('date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
            ->select(
                'date',
                DB::raw('COUNT(*) as total_shifts'),
                DB::raw('SUM(max_seats) as capacity'),
                DB::raw('SUM(booked_seats) as booked'),
                DB::raw('SUM(instant_seats) as instant'),
                DB::raw('SUM(CASE WHEN status="completed" THEN 1 ELSE 0 END) as completed')
            )
            ->groupBy('date')->orderBy('date')->get();
    }

    // Onboarding funnel for BOH-Sales
    // Tracks: registered → has kids → kids have subscription → kids have ride assigned
    private function onboardingFunnelData(): array
    {
        $parentRoleId = DB::table('roles')->where('name', 'parent')->value('id');

        return [
            ['stage' => 'Registered Parents',   'count' => DB::table('model_has_roles')->where('role_id', $parentRoleId)->count()],
            ['stage' => 'Parents with Kids',     'count' => DB::table('kids')->whereNull('deleted_at')->distinct('user_id')->count('user_id')],
            ['stage' => 'Kids with Active Sub',  'count' => DB::table('subscriptions')->whereNull('deleted_at')->where('status','active')->distinct('kid_id')->count('kid_id')],
            // ride_assigns links to subscription which has kid_id
            ['stage' => 'Kids with Ride Assign', 'count' => DB::table('ride_assigns')
                ->whereNull('ride_assigns.deleted_at')
                ->join('subscriptions','ride_assigns.subscription_id','=','subscriptions.id')
                ->whereNull('subscriptions.deleted_at')
                ->distinct('subscriptions.kid_id')->count('subscriptions.kid_id')],
        ];
    }

    // ══════════════════════════════════════════════════════════════════
    // Cache helpers
    // ══════════════════════════════════════════════════════════════════

    /**
     * Bust dashboard cache for one role or all roles.
     *
     * Call from model observers after writes:
     *   DashboardController::bustCache();             // all
     *   DashboardController::bustCache('boh-sales');  // one
     */
    public static function bustCache(?string $role = null): void
    {
        $keys = ['super-admin', 'boh-it', 'boh-marketing', 'boh-sales', 'boh-support'];
        foreach ($role ? [$role] : $keys as $key) {
            Cache::forget("dashboard:{$key}");
        }
    }
}
