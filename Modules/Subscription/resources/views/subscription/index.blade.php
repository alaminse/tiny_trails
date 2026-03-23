{{-- subscription/index.blade.php --}}
@extends('backend.app')
@section('title', 'Subscriptions Management')
@section('css')
    <link href="{{ asset('backend/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --v50:  #f5f3ff;
            --v100: #ede9fe;
            --v200: #ddd6fe;
            --v300: #c4b5fd;
            --v400: #a78bfa;
            --v500: #8b5cf6;
            --v600: #7c3aed;
            --v700: #6d28d9;
            --v800: #5b21b6;
            --v900: #4c1d95;
            --indigo-400: #818cf8;
            --indigo-500: #6366f1;
            --indigo-600: #4f46e5;
            --slate-50:  #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-400: #94a3b8;
            --slate-600: #475569;
            --slate-800: #1e293b;
            --emerald-500: #10b981;
            --amber-500:   #f59e0b;
            --red-500:     #ef4444;
            --shadow-v:    0 8px 32px rgba(109,40,217,0.18);
            --shadow-soft: 0 2px 16px rgba(15,23,42,0.07);
            --radius-lg:   14px;
            --radius-xl:   20px;
            --radius-pill: 999px;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f0eeff; color: var(--slate-800); }

        /* Hero */
        .page-hero {
            background: linear-gradient(135deg, #7c3aed 0%, #6366f1 50%, #4f46e5 100%);
            border-radius: var(--radius-xl); padding: 30px 34px;
            margin-bottom: 26px; position: relative; overflow: hidden;
            box-shadow: var(--shadow-v);
        }
        .page-hero::before {
            content: ''; position: absolute; top: -80px; right: -50px;
            width: 260px; height: 260px; border-radius: 50%;
            background: rgba(255,255,255,0.06); pointer-events: none;
        }
        .page-hero::after {
            content: ''; position: absolute; bottom: -50px; left: 160px;
            width: 160px; height: 160px; border-radius: 50%;
            background: rgba(255,255,255,0.04); pointer-events: none;
        }
        .page-hero-inner {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 16px; position: relative; z-index: 1;
        }
        .page-hero-title {
            font-family: 'Sora', sans-serif;
            font-size: clamp(1.4rem, 3vw, 2rem); font-weight: 800;
            color: #fff; margin: 0 0 4px; letter-spacing: -0.03em;
        }
        .page-hero-sub { font-size: 0.875rem; color: rgba(255,255,255,0.72); margin: 0; }
        .hero-actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-hero {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px; border-radius: var(--radius-pill);
            font-size: 0.8rem; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;
            border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; white-space: nowrap;
        }
        .btn-ghost { background: rgba(255,255,255,0.15); color: #fff; border: 1.5px solid rgba(255,255,255,0.25); backdrop-filter: blur(8px); }
        .btn-ghost:hover { background: rgba(255,255,255,0.25); color: #fff; transform: translateY(-1px); }
        .btn-solid-white { background: #fff; color: var(--v700); }
        .btn-solid-white:hover { background: var(--v50); color: var(--v800); transform: translateY(-1px); }

        /* Stats */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 14px; margin-bottom: 22px;
        }
        .stat-card {
            background: #fff; border-radius: var(--radius-lg); padding: 20px 16px;
            display: flex; flex-direction: column; align-items: center; text-align: center;
            box-shadow: var(--shadow-soft); border: 1.5px solid transparent; transition: all 0.22s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-v); border-color: var(--v200); }
        .stat-icon {
            width: 46px; height: 46px; border-radius: 13px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; margin-bottom: 10px;
        }
        .si-total    { background: var(--v50);       color: var(--v600); }
        .si-active   { background: #ecfdf5;           color: var(--emerald-500); }
        .si-inactive { background: var(--slate-100);  color: var(--slate-400); }
        .si-expired  { background: #fffbeb;           color: var(--amber-500); }
        .si-canceled { background: #fef2f2;           color: var(--red-500); }
        .stat-number { font-family: 'Sora', sans-serif; font-size: 1.9rem; font-weight: 800; color: var(--slate-800); line-height: 1; margin-bottom: 4px; }
        .stat-label  { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--slate-400); }

        /* Revenue */
        .revenue-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 14px; margin-bottom: 22px;
        }
        .revenue-card {
            border-radius: var(--radius-lg); padding: 22px 20px;
            color: #fff; display: flex; align-items: center; gap: 16px;
            box-shadow: var(--shadow-v); transition: transform 0.2s;
        }
        .revenue-card:hover { transform: translateY(-2px); }
        .rc-1 { background: linear-gradient(135deg, var(--v600), var(--indigo-500)); }
        .rc-2 { background: linear-gradient(135deg, var(--indigo-600), var(--v500)); }
        .rc-3 { background: linear-gradient(135deg, var(--v800), var(--indigo-600)); }
        .revenue-icon { width: 52px; height: 52px; flex-shrink: 0; background: rgba(255,255,255,0.18); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .revenue-value { font-family: 'Sora', sans-serif; font-size: 1.55rem; font-weight: 800; line-height: 1; }
        .revenue-label { font-size: 0.76rem; opacity: 0.8; margin-top: 4px; font-weight: 500; }

        /* Filters */
        .filter-panel { background: #fff; border-radius: var(--radius-lg); padding: 20px 22px; margin-bottom: 20px; box-shadow: var(--shadow-soft); border: 1.5px solid var(--v100); }
        .filter-title { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--v600); margin-bottom: 14px; display: flex; align-items: center; gap: 7px; }
        .filter-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(155px, 1fr)); gap: 12px; }
        .filter-label { font-size: 0.76rem; font-weight: 700; color: var(--slate-600); margin-bottom: 5px; display: block; }
        .filter-control {
            width: 100%; padding: 9px 12px; border: 1.5px solid var(--slate-200); border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.83rem; color: var(--slate-800);
            background: var(--slate-50); transition: border-color 0.18s, box-shadow 0.18s;
            -webkit-appearance: none; appearance: none;
        }
        .filter-control:focus { outline: none; border-color: var(--v400); box-shadow: 0 0 0 3px rgba(139,92,246,0.14); background: #fff; }
        .filter-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin-top: 14px; }
        .btn-apply {
            padding: 9px 20px; border-radius: var(--radius-pill);
            background: linear-gradient(135deg, var(--v600), var(--indigo-500));
            color: #fff; border: none; cursor: pointer;
            font-size: 0.8rem; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;
            display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;
        }
        .btn-apply:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(109,40,217,0.3); }
        .btn-clear {
            padding: 9px 20px; border-radius: var(--radius-pill); background: var(--slate-100); color: var(--slate-600);
            border: none; cursor: pointer; font-size: 0.8rem; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;
            display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;
        }
        .btn-clear:hover { background: var(--slate-200); }
        .filter-hint { font-size: 0.73rem; color: var(--slate-400); margin-left: auto; }

        /* Table Card */
        .table-card { background: #fff; border-radius: var(--radius-xl); box-shadow: var(--shadow-soft); border: 1.5px solid var(--v100); overflow: hidden; margin-bottom: 28px; }
        .table-card-header {
            padding: 18px 22px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
            border-bottom: 1.5px solid var(--v50); background: linear-gradient(to right, var(--v50) 0%, #fff 60%);
        }
        .table-card-title { font-family: 'Sora', sans-serif; font-size: 1.1rem; font-weight: 700; color: var(--slate-800); margin: 0; }
        .table-card-sub   { font-size: 0.76rem; color: var(--slate-400); margin-top: 2px; }
        .header-actions   { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-hdr {
            padding: 7px 15px; border-radius: var(--radius-pill);
            font-size: 0.77rem; font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif;
            border: 1.5px solid transparent; cursor: pointer;
            display: inline-flex; align-items: center; gap: 5px;
            transition: all 0.18s; text-decoration: none; white-space: nowrap;
        }
        .btn-export  { background: #fff8e1; color: #b45309; border-color: #fde68a; }
        .btn-export:hover  { background: #fde68a; }
        .btn-trashed { background: var(--v50); color: var(--v700); border-color: var(--v200); }
        .btn-trashed:hover { background: var(--v100); }
        .btn-add     { background: linear-gradient(135deg, var(--v600), var(--indigo-500)); color: #fff; border-color: transparent; }
        .btn-add:hover     { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(109,40,217,0.28); color: #fff; }

        /* DataTable */
        #datatable-responsive thead th {
            background: var(--v50) !important; color: var(--v700) !important;
            font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em;
            border-bottom: 2px solid var(--v100) !important; padding: 12px 14px !important; white-space: nowrap;
        }
        #datatable-responsive tbody td { padding: 13px 14px !important; vertical-align: middle !important; border-bottom: 1px solid var(--slate-100) !important; font-size: 0.83rem; }
        #datatable-responsive tbody tr:hover td { background: var(--v50) !important; }
        #datatable-responsive tbody tr:last-child td { border-bottom: none !important; }
        .dataTables_wrapper { padding: 16px 20px 10px; }
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input { border: 1.5px solid var(--slate-200); border-radius: 10px; padding: 6px 10px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.82rem; }
        .dataTables_wrapper .dataTables_filter input:focus { outline: none; border-color: var(--v400); box-shadow: 0 0 0 3px rgba(139,92,246,0.12); }
        .dataTables_wrapper .dataTables_info { font-size: 0.78rem; color: var(--slate-400); }
        .dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 8px !important; margin: 0 2px; font-size: 0.8rem !important; font-family: 'Plus Jakarta Sans', sans-serif !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background: var(--v600) !important; border-color: var(--v600) !important; color: #fff !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: var(--v50) !important; border-color: var(--v200) !important; color: var(--v700) !important; }

        /* Badges */
        .badge-pill { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: var(--radius-pill); font-size: 0.7rem; font-weight: 700; white-space: nowrap; }
        .bp-active   { background: #ecfdf5; color: #059669; }
        .bp-inactive { background: var(--slate-100); color: var(--slate-600); }
        .bp-canceled { background: #fef2f2; color: #dc2626; }
        .bp-past_due { background: #fffbeb; color: #d97706; }
        .bp-trialing { background: #eff6ff; color: #2563eb; }
        .bp-pending  { background: var(--v50); color: var(--v600); }
        .badge-dot   { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
        .bd-active   { background: #059669; }
        .bd-inactive { background: var(--slate-400); }
        .bd-canceled { background: #dc2626; }
        .bd-past_due { background: #d97706; }
        .bd-trialing { background: #2563eb; }
        .bd-pending  { background: var(--v500); }

        /* Avatar */
        .user-avatar { width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0; background: linear-gradient(135deg, var(--v500), var(--indigo-400)); color: #fff; font-weight: 800; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; }
        .user-email  { font-size: 0.72rem; color: var(--slate-400); }

        /* Action buttons */
        .action-btn  { width: 30px; height: 30px; border-radius: 8px; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 0.76rem; transition: all 0.18s; }
        .ab-view:hover, .ab-edit:hover, .ab-cancel:hover, .ab-pay:hover, .ab-react:hover, .ab-delete:hover { transform: scale(1.12); }
        .ab-view   { background: #eff6ff; color: #2563eb; } .ab-view:hover   { background: #dbeafe; }
        .ab-edit   { background: #fffbeb; color: #d97706; } .ab-edit:hover   { background: #fef3c7; }
        .ab-cancel { background: #fef2f2; color: #dc2626; } .ab-cancel:hover { background: #fee2e2; }
        .ab-pay    { background: #ecfdf5; color: #059669; } .ab-pay:hover    { background: #d1fae5; }
        .ab-react  { background: #ecfdf5; color: #059669; } .ab-react:hover  { background: #d1fae5; }
        .ab-delete { background: var(--v50); color: var(--v700); } .ab-delete:hover { background: var(--v100); }
        .action-wrap { display: flex; gap: 4px; flex-wrap: wrap; }

        /* Connection */
        .payway-connection-status { position: fixed; top: 20px; right: 20px; z-index: 1050; min-width: 260px; max-width: 320px; }
        .alert { border-radius: 12px; font-size: 0.83rem; }
        .alert-success { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
        .alert-danger  { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
        .alert-info    { background: var(--v50); border-color: var(--v200); color: var(--v700); }

        /* Spinner */
        #table-loading { padding: 20px; }
        .spinner-ring { width: 30px; height: 30px; border: 3px solid var(--v100); border-top-color: var(--v500); border-radius: 50%; animation: spin 0.75s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Responsive */
        @media (max-width: 768px) {
            .page-hero { padding: 20px 16px; }
            .stats-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; }
            .stat-card  { padding: 14px 10px; }
            .stat-number{ font-size: 1.5rem; }
            .revenue-grid { grid-template-columns: 1fr; gap: 10px; }
            .table-card-header { flex-direction: column; align-items: flex-start; }
            .filter-row { grid-template-columns: 1fr 1fr; }
            .filter-hint{ display: none; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .filter-row { grid-template-columns: 1fr; }
            .page-hero-title { font-size: 1.25rem; }
            .hero-actions .btn-hero { padding: 7px 12px; font-size: 0.75rem; }
        }
    </style>
@endsection

@section('content')

    <div id="payway-connection-status" class="payway-connection-status" style="display:none;"></div>

@can('list-subscription')
    <div class="app-content">
        <div class="container-fluid py-3">

            <!-- Hero -->
            <div class="page-hero mb-4">
                <div class="page-hero-inner">
                    <div>
                        <h1 class="page-hero-title"><i class="fas fa-layer-group me-2" style="opacity:.85;"></i>Subscriptions</h1>
                        <p class="page-hero-sub">Manage PayWay subscriptions &amp; billing</p>
                    </div>
                    <div class="hero-actions">
                        <button class="btn-hero btn-ghost" id="testPayWayBtn"><i class="fas fa-wifi"></i> Test Connection</button>
                        <button class="btn-hero btn-ghost" id="refreshDataBtn"><i class="fas fa-sync-alt"></i> Refresh</button>
                        @can('create-subscription')
                        <a href="{{ route('admin.subscriptions.create') }}" class="btn-hero btn-solid-white"><i class="fas fa-plus"></i> New Subscription</a>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon si-total"><i class="fas fa-users"></i></div>
                    <div class="stat-number" id="total-subscriptions">{{ $stats['total'] ?? 0 }}</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-active"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-number" id="active-subscriptions">{{ $stats['active'] ?? 0 }}</div>
                    <div class="stat-label">Active</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-inactive"><i class="fas fa-pause-circle"></i></div>
                    <div class="stat-number" id="inactive-subscriptions">{{ $stats['inactive'] ?? 0 }}</div>
                    <div class="stat-label">Inactive</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-expired"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-number" id="expired-subscriptions">{{ $stats['expired'] ?? 0 }}</div>
                    <div class="stat-label">Expired</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon si-canceled"><i class="fas fa-times-circle"></i></div>
                    <div class="stat-number" id="canceled-subscriptions">{{ $stats['canceled'] ?? 0 }}</div>
                    <div class="stat-label">Canceled</div>
                </div>
            </div>

            <!-- Revenue -->
            <div class="revenue-grid">
                <div class="revenue-card rc-1">
                    <div class="revenue-icon"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <div class="revenue-value">${{ number_format($revenueStats['monthly_revenue'] ?? 0, 2) }}</div>
                        <div class="revenue-label">Monthly Revenue</div>
                    </div>
                </div>
                <div class="revenue-card rc-2">
                    <div class="revenue-icon"><i class="fas fa-chart-bar"></i></div>
                    <div>
                        <div class="revenue-value">${{ number_format($revenueStats['yearly_revenue'] ?? 0, 2) }}</div>
                        <div class="revenue-label">Yearly Revenue</div>
                    </div>
                </div>
                <div class="revenue-card rc-3">
                    <div class="revenue-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div>
                        <div class="revenue-value">${{ number_format($revenueStats['total_active_value'] ?? 0, 2) }}</div>
                        <div class="revenue-label">Total Active Value</div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-panel">
                <div class="filter-title"><i class="fas fa-sliders-h"></i> Advanced Filters</div>
                <div class="filter-row">
                    <div>
                        <label class="filter-label" for="filter_status">Status</label>
                        <select id="filter_status" class="filter-control">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="filter-label" for="filter_payway_status">PayWay Status</label>
                        <select id="filter_payway_status" class="filter-control">
                            <option value="">All PayWay Statuses</option>
                            <option value="active">Active</option>
                            <option value="canceled">Canceled</option>
                            <option value="past_due">Past Due</option>
                            <option value="trialing">Trialing</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div>
                        <label class="filter-label" for="filter_trial">Trial Status</label>
                        <select id="filter_trial" class="filter-control">
                            <option value="">All Trial Statuses</option>
                            <option value="on_trial">On Trial</option>
                            <option value="trial_ended">Trial Ended</option>
                        </select>
                    </div>
                    <div>
                        <label class="filter-label" for="filter_search">Search</label>
                        <input type="text" id="filter_search" class="filter-control" placeholder="Search subscriptions…">
                    </div>
                </div>
                <div class="filter-actions">
                    <button id="applyFilters" class="btn-apply"><i class="fas fa-search"></i> Apply Filters</button>
                    <button id="clearFilters" class="btn-clear"><i class="fas fa-times"></i> Clear</button>
                    <span class="filter-hint"><i class="fas fa-keyboard me-1"></i>Enter to search</span>
                </div>
            </div>

            <!-- Table -->
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h5 class="table-card-title"><i class="fas fa-credit-card me-2" style="color:var(--v400);"></i>Subscriptions</h5>
                        <div class="table-card-sub">Manage PayWay subscriptions and payments</div>
                    </div>
                    <div class="header-actions">
                        <button class="btn-hdr btn-export" id="exportBtn"><i class="fas fa-download"></i> Export</button>
                        @can('delete-subscription')
                        <button class="btn-hdr btn-trashed" id="showTrashed"><i class="fas fa-trash-restore"></i> Trashed</button>
                        @endcan
                        @can('create-subscription')
                        <a href="{{ route('admin.subscriptions.create') }}" class="btn-hdr btn-add"><i class="fas fa-plus"></i> Add</a>
                        @endcan
                    </div>
                </div>
                <div>
                    <div id="table-loading" class="text-center" style="display:none;">
                        <div class="d-flex align-items-center justify-content-center gap-3 py-3">
                            <div class="spinner-ring"></div>
                            <span style="color:var(--slate-400);font-size:.83rem;">Loading subscriptions…</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="datatable-responsive" class="table table-borderless dt-responsive nowrap w-100" cellspacing="0">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="18%">User</th>
                                    <th width="15%">Plan</th>
                                    <th width="11%">Status</th>
                                    <th width="12%">PayWay</th>
                                    <th width="12%">Next Billing</th>
                                    <th width="14%">Payment Method</th>
                                    <th width="13%">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endcan

    @push('scripts')
        <script src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('backend/js/responsive.bootstrap.js') }}"></script>
        <script src="https://api.payway.com.au/rest/v1/payway.js"></script>

        <script>
        $(document).ready(function () {
            let canDelete   = {{ auth()->user()->can('delete-subscription')   ? 'true' : 'false' }};
            let canView     = {{ auth()->user()->can('view-subscription')     ? 'true' : 'false' }};
            let canEdit     = {{ auth()->user()->can('edit-subscription')     ? 'true' : 'false' }};
            let canCancel   = {{ auth()->user()->can('delete-subscription')   ? 'true' : 'false' }};
            let canUnassign = {{ auth()->user()->can('unassign-subscription') ? 'true' : 'false' }};

            const routes = {
                data: '/admin/subscriptions/data/get', stats: '/admin/subscriptions/stats',
                expiring: '/admin/subscriptions/expiring', paymentIssues: '/admin/subscriptions/payment-issues',
                recentActivity: '/admin/subscriptions/recent-activity', processPayment: '/admin/subscriptions/process-payment',
                cancel: '/admin/subscriptions/cancel', reactivate: '/admin/subscriptions/reactivate',
                export: '/admin/subscriptions/export', payway: { testConnection: '/admin/payway/test-connection' }
            };

            let subscriptionTable, filters = {};

            function statusBadge(status) {
                const cls = { active:'bp-active', inactive:'bp-inactive', canceled:'bp-canceled', past_due:'bp-past_due', trialing:'bp-trialing', pending:'bp-pending' };
                const dot = { active:'bd-active', inactive:'bd-inactive', canceled:'bd-canceled', past_due:'bd-past_due', trialing:'bd-trialing', pending:'bd-pending' };
                const label = (status||'pending').replace(/_/g,' ');
                return `<span class="badge-pill ${cls[status]||'bp-pending'}"><span class="badge-dot ${dot[status]||'bd-pending'}"></span>${label}</span>`;
            }

            function initializeDataTable() {
                subscriptionTable = $('#datatable-responsive').DataTable({
                    processing: true, serverSide: true, responsive: true,
                    ajax: {
                        url: routes.data,
                        data: function (d) { return $.extend({}, d, filters); },
                        beforeSend: function () { $('#table-loading').show(); },
                        complete:   function () { $('#table-loading').hide(); }
                    },
                    columns: [
                        { data: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'user', name: 'user.name', render: function (data) {
                            if (!data) return '<span style="color:var(--slate-400);font-size:.8rem;">Unknown</span>';
                            return `<div class="d-flex align-items-center gap-2"><div class="user-avatar">${data.name?data.name.charAt(0).toUpperCase():'U'}</div><div><div class="user-email">${data.email||''}</div></div></div>`;
                        }},
                        { data: 'plan', name: 'plan.name', render: function (data) {
                            if (!data) return '<span class="badge-pill bp-inactive">Plan Deleted</span>';
                            return `<div><strong style="font-size:.83rem;">${data.name}</strong><div style="font-size:.74rem;color:#059669;font-weight:700;">$${parseFloat(data.price||0).toFixed(2)}</div></div>`;
                        }},
                        { data: 'status',       name: 'status',       render: function (d) { return statusBadge(d); } },
                        { data: 'payway_status', name: 'payway_status',render: function (d) { return statusBadge(d); } },
                        { data: 'ends_at', name: 'ends_at', render: function (data) {
                            if (!data) return '<span style="color:var(--slate-400);font-size:.8rem;">—</span>';
                            return `<span style="font-size:.81rem;">${new Date(data).toLocaleDateString()}</span>`;
                        }},
                        { data: null, name: 'payment_method', orderable: false, searchable: false, render: function (data, type, row) {
                            if (!row.card_brand || !row.card_last_four) return '<span style="color:var(--slate-400);font-size:.8rem;">No card</span>';
                            return `<div class="d-flex align-items-center gap-2"><i class="fab fa-cc-${(row.card_brand||'').toLowerCase()}" style="font-size:1.15rem;color:var(--v500);"></i><div><div style="font-size:.81rem;font-weight:600;">••••${row.card_last_four}</div><div style="font-size:.72rem;color:var(--slate-400);">${row.card_expiration||''}</div></div></div>`;
                        }},
                        { data: null, name: 'actions', orderable: false, searchable: false, render: function (data, type, row) {
                            let b = '';
                            if (canView)   b += `<button class="action-btn ab-view viewBtn" data-id="${row.id}" title="View"><i class="fas fa-eye"></i></button>`;
                            if (canEdit)   b += `<button class="action-btn ab-edit editBtn" data-id="${row.id}" title="Edit"><i class="fas fa-edit"></i></button>`;
                            if (canCancel && row.status === 'active' && !row.canceled_at) {
                                b += `<button class="action-btn ab-cancel cancelBtn" data-id="${row.id}" title="Cancel"><i class="fas fa-ban"></i></button>`;
                                b += `<button class="action-btn ab-pay processPaymentBtn" data-id="${row.id}" title="Process Payment"><i class="fas fa-credit-card"></i></button>`;
                            }
                            if (canCancel && row.canceled_at) b += `<button class="action-btn ab-react reactivateBtn" data-id="${row.id}" title="Reactivate"><i class="fas fa-redo"></i></button>`;
                            if (canDelete) b += `<button class="action-btn ab-delete deleteBtn" data-id="${row.id}" title="Delete"><i class="fas fa-trash"></i></button>`;
                            return `<div class="action-wrap">${b}</div>`;
                        }}
                    ],
                    order: [[0, 'desc']], pageLength: 25, lengthMenu: [[10,25,50,100],[10,25,50,100]],
                    language: { processing:'<i class="fas fa-spinner fa-spin"></i>', emptyTable:'No subscriptions found', zeroRecords:'No matches', search:'Search:', lengthMenu:'Show _MENU_', info:'_START_–_END_ of _TOTAL_', infoEmpty:'No subscriptions', infoFiltered:'(from _MAX_)' },
                    drawCallback: function () { $('[title]').tooltip({ trigger: 'hover' }); }
                });
            }

            function applyFilters() {
                filters = { status: $('#filter_status').val(), payway_status: $('#filter_payway_status').val(), trial_status: $('#filter_trial').val(), search: $('#filter_search').val() };
                subscriptionTable.ajax.reload();
            }
            function clearFilters() {
                filters = {}; $('#filter_status, #filter_payway_status, #filter_trial').val(''); $('#filter_search').val('');
                subscriptionTable.ajax.reload();
            }
            $('#applyFilters').on('click', applyFilters);
            $('#clearFilters').on('click', clearFilters);
            $('#filter_search').on('keypress', function (e) { if (e.which === 13) applyFilters(); });

            $('#testPayWayBtn').on('click', testPayWayConnection);
            $('#refreshDataBtn').on('click', function () { subscriptionTable.ajax.reload(); showToast('info', 'Refreshing data…'); });
            $(document).on('click', '.viewBtn',           function () { window.location.href = `/admin/subscriptions/${$(this).data('id')}`; });
            $(document).on('click', '.editBtn',           function () { window.location.href = `/admin/subscriptions/${$(this).data('id')}/edit`; });
            $(document).on('click', '.cancelBtn',         function () { cancelSubscription($(this).data('id')); });
            $(document).on('click', '.reactivateBtn',     function () { reactivateSubscription($(this).data('id')); });
            $(document).on('click', '.processPaymentBtn', function () { processPayment($(this).data('id')); });
            $(document).on('click', '.deleteBtn',         function () { deleteSubscription($(this).data('id')); });
            $('#exportBtn').on('click', showExportDialog);

            async function testPayWayConnection() {
                const div = $('#payway-connection-status');
                div.show().html(`<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Testing PayWay…</div>`);
                try {
                    const res = await fetch(routes.payway.testConnection); const result = await res.json();
                    if (result.success) {
                        div.html(`<div class="alert alert-success alert-dismissible"><i class="fas fa-check-circle me-2"></i>Connected! <small>${result.data.clientName||''}</small><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
                    } else {
                        div.html(`<div class="alert alert-danger alert-dismissible"><i class="fas fa-exclamation-triangle me-2"></i>${result.message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
                    }
                    setTimeout(() => div.fadeOut(), 10000);
                } catch (err) {
                    div.html(`<div class="alert alert-danger alert-dismissible"><i class="fas fa-exclamation-triangle me-2"></i>${err.message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
                    setTimeout(() => div.fadeOut(), 10000);
                }
            }

            function swalPost(url, payload, successCb) {
                return fetch(url, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}, body:JSON.stringify(payload) })
                    .then(r => r.json()).then(data => { data.success ? Swal.fire('Done!', data.message,'success') : Swal.fire('Error!',data.message,'error'); if(data.success && successCb) successCb(); })
                    .catch(() => Swal.fire('Error!','Request failed','error'));
            }

            function cancelSubscription(id) {
                Swal.fire({ title:'Cancel Subscription?', confirmButtonColor:'#7c3aed', confirmButtonText:'Cancel Subscription', showCancelButton:true,
                    html:`<textarea id="cr" class="form-control mb-3" placeholder="Reason (optional)" rows="3"></textarea><div class="form-check"><input class="form-check-input" type="checkbox" id="ci"><label class="form-check-label" for="ci" style="font-size:.83rem;">Cancel immediately</label></div>`,
                    preConfirm:()=>({reason:document.getElementById('cr').value,cancel_immediately:document.getElementById('ci').checked})
                }).then(r => { if(r.isConfirmed) swalPost(`${routes.cancel}/${id}`,r.value,()=>subscriptionTable.ajax.reload()); });
            }

            function reactivateSubscription(id) {
                Swal.fire({ title:'Reactivate?', text:'Reactivates the canceled subscription.', icon:'question', confirmButtonColor:'#7c3aed', confirmButtonText:'Reactivate', showCancelButton:true })
                    .then(r => { if(r.isConfirmed) fetch(`${routes.reactivate}/${id}`,{method:'POST',headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}}).then(res=>res.json()).then(data=>{data.success?Swal.fire('Reactivated!',data.message,'success'):Swal.fire('Error!',data.message,'error');if(data.success)subscriptionTable.ajax.reload();}); });
            }

            function processPayment(id) {
                Swal.fire({ title:'Process Payment', confirmButtonColor:'#7c3aed', confirmButtonText:'Process', showCancelButton:true,
                    html:`<label class="form-label" style="font-size:.84rem;font-weight:600;">Amount</label><input type="number" id="pa" class="form-control" placeholder="0.00" step="0.01" min="0.01">`,
                    preConfirm:()=>{ const a=document.getElementById('pa').value; if(!a||a<=0){Swal.showValidationMessage('Enter a valid amount');return false;} return{amount:a}; }
                }).then(r => { if(r.isConfirmed) swalPost(`${routes.processPayment}/${id}`,r.value,()=>subscriptionTable.ajax.reload()); });
            }

            function deleteSubscription(id) {
                Swal.fire({ title:'Delete?', text:'Moves to trash.', icon:'warning', confirmButtonColor:'#7c3aed', confirmButtonText:'Delete', showCancelButton:true })
                    .then(r => { if(r.isConfirmed) fetch(`/admin/subscriptions/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')}}).then(res=>res.json()).then(data=>{data.success?Swal.fire('Deleted!',data.message,'success'):Swal.fire('Error!',data.message,'error');if(data.success)subscriptionTable.ajax.reload();}); });
            }

            function showExportDialog() {
                Swal.fire({ title:'Export Subscriptions', confirmButtonColor:'#7c3aed', confirmButtonText:'Export', showCancelButton:true,
                    html:`<div class="mb-3"><label class="form-label" style="font-size:.84rem;font-weight:600;">Format</label><select id="ef" class="form-control"><option value="csv">CSV</option><option value="excel">Excel</option><option value="pdf">PDF</option></select></div>
                          <div class="mb-3"><label class="form-label" style="font-size:.84rem;font-weight:600;">Date Range</label><select id="edr" class="form-control"><option value="all">All Time</option><option value="today">Today</option><option value="week">This Week</option><option value="month">This Month</option><option value="year">This Year</option><option value="custom">Custom Range</option></select></div>
                          <div id="cdr" style="display:none;"><div class="row"><div class="col-6"><label class="form-label" style="font-size:.8rem;">From</label><input type="date" id="edf" class="form-control"></div><div class="col-6"><label class="form-label" style="font-size:.8rem;">To</label><input type="date" id="edt" class="form-control"></div></div></div>`,
                    didOpen:()=>{ document.getElementById('edr').addEventListener('change',function(){document.getElementById('cdr').style.display=this.value==='custom'?'block':'none';}); },
                    preConfirm:()=>{ const f=document.getElementById('ef').value,d=document.getElementById('edr').value; let p={format:f,range:d}; if(d==='custom'){const fr=document.getElementById('edf').value,to=document.getElementById('edt').value;if(!fr||!to){Swal.showValidationMessage('Select both dates');return false;}p.date_from=fr;p.date_to=to;} return p; }
                }).then(r=>{ if(r.isConfirmed) window.open(`${routes.export}?${new URLSearchParams(r.value).toString()}`,'_blank'); });
            }

            function showToast(type, message) {
                const toast = $(`<div class="toast position-fixed top-0 end-0 m-3" style="z-index:9999;border-radius:12px;overflow:hidden;min-width:230px;">
                    <div class="toast-header" style="font-family:'Plus Jakarta Sans',sans-serif;font-size:.81rem;border-bottom:1px solid var(--v100);">
                        <i class="fas fa-${type==='success'?'check-circle':type==='error'?'exclamation-triangle':'info-circle'} me-2" style="color:${type==='success'?'#059669':type==='error'?'#dc2626':'#7c3aed'};"></i>
                        <strong class="me-auto" style="font-size:.8rem;">Notification</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body" style="background:linear-gradient(135deg,#7c3aed,#6366f1);color:#fff;font-size:.8rem;">${message}</div>
                </div>`);
                $('body').append(toast);
                new bootstrap.Toast(toast[0]).show();
                setTimeout(() => toast.remove(), 5000);
            }

            $(document).on('keydown', function (e) {
                if (e.ctrlKey && e.altKey) {
                    if (e.key==='r'){e.preventDefault();subscriptionTable.ajax.reload();showToast('info','Refreshing…');}
                    if (e.key==='n'){e.preventDefault();window.location.href='/admin/subscriptions/create';}
                    if (e.key==='t'){e.preventDefault();testPayWayConnection();}
                    if (e.key==='e'){e.preventDefault();showExportDialog();}
                }
            });

            initializeDataTable();
            $('[title]').tooltip({ trigger: 'hover' });
            showToast('success', 'Subscription management loaded');
        });
        </script>
    @endpush
@endsection
