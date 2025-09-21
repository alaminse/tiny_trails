{{-- subscription/index.blade.php --}}
@extends('backend.app')
@section('title', 'Subscriptions Management')
@section('css')
    <link href="{{ asset('backend/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
    <style>
        .stats-card {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
        }
        .subscription-status {
            font-weight: bold;
        }
        .revenue-card {
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .payway-status-badge {
            font-size: 0.75rem;
        }
        .action-buttons .btn {
            margin: 1px;
        }
        .filter-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .subscription-card {
            border-left: 4px solid #28a745;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        .subscription-card:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .subscription-card.inactive {
            border-left-color: #dc3545;
        }
        .subscription-card.trial {
            border-left-color: #17a2b8;
        }
        .subscription-card.expired {
            border-left-color: #ffc107;
        }
        .dashboard-widget {
            height: 400px;
            overflow-y: auto;
        }
        .payway-connection-status {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 250px;
        }
        .quick-actions {
            background: linear-gradient(45deg, #36d1dc 0%, #5b86e5 100%);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .metric-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        .status-active { background-color: #28a745; }
        .status-inactive { background-color: #6c757d; }
        .status-trial { background-color: #17a2b8; }
        .status-expired { background-color: #ffc107; }
        .status-canceled { background-color: #dc3545; }
    </style>
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Subscriptions', 'subTitle' => 'Subscription Management with PayWay'])

    <!-- PayWay Connection Status (Fixed Position) -->
    <div id="payway-connection-status" class="payway-connection-status" style="display: none;"></div>

    <div class="app-content">
        <div class="container-fluid">

            <!-- Quick Actions Bar -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="quick-actions">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                                <small>Manage subscriptions efficiently</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-light btn-sm" id="testPayWayBtn" title="Test PayWay Connection">
                                    <i class="fas fa-wifi me-1"></i>Test Connection
                                </button>
                                <button class="btn btn-light btn-sm" id="refreshDataBtn" title="Refresh All Data">
                                    <i class="fas fa-sync-alt me-1"></i>Refresh
                                </button>
                                <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-plus me-1"></i>New Subscription
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="stats-card">
                        <div class="text-center">
                            <div class="metric-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stats-number" id="total-subscriptions">{{ $stats['total'] ?? 0 }}</div>
                            <div>Total</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card bg-success">
                        <div class="text-center">
                            <div class="metric-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stats-number" id="active-subscriptions">{{ $stats['active'] ?? 0 }}</div>
                            <div>Active</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card bg-secondary">
                        <div class="text-center">
                            <div class="metric-icon">
                                <i class="fas fa-pause-circle"></i>
                            </div>
                            <div class="stats-number" id="inactive-subscriptions">{{ $stats['inactive'] ?? 0 }}</div>
                            <div>Inactive</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card bg-info">
                        <div class="text-center">
                            <div class="metric-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stats-number" id="trial-subscriptions">{{ $stats['on_trial'] ?? 0 }}</div>
                            <div>On Trial</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card bg-warning">
                        <div class="text-center">
                            <div class="metric-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="stats-number" id="expired-subscriptions">{{ $stats['expired'] ?? 0 }}</div>
                            <div>Expired</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card bg-danger">
                        <div class="text-center">
                            <div class="metric-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="stats-number" id="canceled-subscriptions">{{ $stats['canceled'] ?? 0 }}</div>
                            <div>Canceled</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="stats-card revenue-card">
                        <div class="text-center">
                            <div class="metric-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stats-number">${{ number_format($revenueStats['monthly_revenue'] ?? 0, 2) }}</div>
                            <div>Monthly Revenue</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card revenue-card">
                        <div class="text-center">
                            <div class="metric-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="stats-number">${{ number_format($revenueStats['yearly_revenue'] ?? 0, 2) }}</div>
                            <div>Yearly Revenue</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card revenue-card">
                        <div class="text-center">
                            <div class="metric-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stats-number">${{ number_format($revenueStats['total_active_value'] ?? 0, 2) }}</div>
                            <div>Total Active Value</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="filter-section">
                        <h6 class="mb-3"><i class="fas fa-filter me-2"></i>Advanced Filters</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="filter_status" class="form-label">Status</label>
                                <select id="filter_status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_payway_status" class="form-label">PayWay Status</label>
                                <select id="filter_payway_status" class="form-control">
                                    <option value="">All PayWay Statuses</option>
                                    <option value="active">Active</option>
                                    <option value="canceled">Canceled</option>
                                    <option value="past_due">Past Due</option>
                                    <option value="trialing">Trialing</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_trial" class="form-label">Trial Status</label>
                                <select id="filter_trial" class="form-control">
                                    <option value="">All Trial Statuses</option>
                                    <option value="on_trial">On Trial</option>
                                    <option value="trial_ended">Trial Ended</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filter_search" class="form-label">Search</label>
                                <input type="text" id="filter_search" class="form-control" placeholder="Search subscriptions...">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button id="applyFilters" class="btn btn-primary btn-sm me-2">
                                    <i class="fas fa-search me-1"></i>Apply Filters
                                </button>
                                <button id="clearFilters" class="btn btn-secondary btn-sm me-2">
                                    <i class="fas fa-times me-1"></i>Clear Filters
                                </button>
                                <div class="float-end">
                                    <small class="text-muted">
                                        <i class="fas fa-keyboard me-1"></i>
                                        Press Enter in search to apply filters
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card me-2"></i>Subscriptions Management
                                </h5>
                                <small class="text-muted">Manage PayWay subscriptions and payments</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-gradient-info btn-sm" id="exportBtn">
                                    <i class="fas fa-download me-1"></i>Export
                                </button>
                                <button class="btn btn-gradient-warning btn-sm" id="showTrashed">
                                    <i class="fas fa-trash-restore me-1"></i>View Trashed
                                </button>
                                <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-gradient-success btn-sm">
                                    <i class="fas fa-plus me-1"></i>Add Subscription
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Loading indicator -->
                            <div id="table-loading" class="text-center" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading subscriptions...</p>
                            </div>

                            <div class="table-responsive">
                                <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap"
                                    cellspacing="0" width="100%">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="15%">User</th>
                                            <th width="15%">Plan</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">PayWay Status</th>
                                            <th width="12%">Trial Ends</th>
                                            <th width="12%">Next Billing</th>
                                            <th width="10%">Payment Method</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Widgets -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>Expiring Soon
                                <span class="badge bg-dark ms-2" id="expiring-count">0</span>
                            </h6>
                        </div>
                        <div class="card-body dashboard-widget">
                            <div id="expiring-subscriptions">
                                <div class="text-center text-muted loading-skeleton" style="height: 60px; border-radius: 5px;">
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Loading...
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn btn-warning btn-sm" onclick="loadExpiringSubscriptions()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>Payment Issues
                                <span class="badge bg-light text-dark ms-2" id="issues-count">0</span>
                            </h6>
                        </div>
                        <div class="card-body dashboard-widget">
                            <div id="payment-issues">
                                <div class="text-center text-muted loading-skeleton" style="height: 60px; border-radius: 5px;">
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Loading...
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn btn-danger btn-sm" onclick="loadPaymentIssues()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-clock me-2"></i>Recent Activity
                                <span class="badge bg-light text-dark ms-2" id="activity-count">0</span>
                            </h6>
                        </div>
                        <div class="card-body dashboard-widget">
                            <div id="recent-activity">
                                <div class="text-center text-muted loading-skeleton" style="height: 60px; border-radius: 5px;">
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Loading...
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn btn-info btn-sm" onclick="loadRecentActivity()">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PayWay Integration Status -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-cogs me-2"></i>PayWay Integration Status
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="payway-status">
                                        <div class="d-flex align-items-center">
                                            <div class="status-indicator status-inactive" id="connection-indicator"></div>
                                            <span id="connection-text">Checking connection...</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button class="btn btn-outline-primary btn-sm me-2" id="testStepByStepBtn">
                                        <i class="fas fa-list-ol me-1"></i>Step-by-Step Test
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" id="debugConfigBtn">
                                        <i class="fas fa-bug me-1"></i>Debug Config
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('backend/js/responsive.bootstrap.js') }}"></script>

        <!-- PayWay JavaScript SDK -->
        <script src="https://api.payway.com.au/rest/v1/payway.js"></script>

        <script>
            $(document).ready(function() {
                // API Routes Configuration
                const routes = {
                    data: '/admin/subscriptions/data/get',
                    stats: '/admin/subscriptions/stats',
                    expiring: '/admin/subscriptions/expiring',
                    paymentIssues: '/admin/subscriptions/payment-issues',
                    recentActivity: '/admin/subscriptions/recent-activity',
                    processPayment: '/admin/subscriptions/process-payment',
                    cancel: '/admin/subscriptions/cancel',
                    reactivate: '/admin/subscriptions/reactivate',
                    export: '/admin/subscriptions/export',
                    payway: {
                        testConnection: '/admin/payway/test-connection',
                        testStepByStep: '/admin/payway/test-step-by-step',
                        debugConfig: '/admin/payway/debug-config'
                    }
                };

                // Global variables
                let subscriptionTable;
                let filters = {};
                let refreshInterval;

                // Initialize DataTable
                function initializeDataTable() {
                    subscriptionTable = $('#datatable-responsive').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: {
                            url: routes.data,
                            data: function(d) {
                                return $.extend({}, d, filters);
                            },
                            beforeSend: function() {
                                $('#table-loading').show();
                            },
                            complete: function() {
                                $('#table-loading').hide();
                            }
                        },
                        columns: [
                            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                            {
                                data: 'user',
                                name: 'user.name',
                                render: function(data, type, row) {
                                    if (!data) return '<span class="text-muted">Unknown User</span>';
                                    return `
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title rounded-circle bg-primary text-white">
                                                    ${data.name ? data.name.charAt(0).toUpperCase() : 'U'}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">${data.name || 'Unknown User'}</h6>
                                                <small class="text-muted">${data.email || ''}</small>
                                            </div>
                                        </div>
                                    `;
                                }
                            },
                            {
                                data: 'plan',
                                name: 'plan.name',
                                render: function(data, type, row) {
                                    if (!data) return '<span class="badge bg-secondary">Plan Deleted</span>';
                                    return `
                                        <div>
                                            <strong>${data.name}</strong>
                                            <br><small class="text-success">$${parseFloat(data.price || 0).toFixed(2)}</small>
                                        </div>
                                    `;
                                }
                            },
                            {
                                data: 'status',
                                name: 'status',
                                render: function(data, type, row) {
                                    const statusColors = {
                                        'active': 'success',
                                        'inactive': 'secondary',
                                        'canceled': 'danger'
                                    };
                                    const indicatorClass = `status-${data}`;
                                    return `
                                        <div class="d-flex align-items-center">
                                            <div class="status-indicator ${indicatorClass}"></div>
                                            <span class="badge bg-${statusColors[data] || 'secondary'}">${data}</span>
                                        </div>
                                    `;
                                }
                            },
                            {
                                data: 'payway_status',
                                name: 'payway_status',
                                render: function(data, type, row) {
                                    const statusColors = {
                                        'active': 'success',
                                        'canceled': 'danger',
                                        'past_due': 'warning',
                                        'trialing': 'info',
                                        'pending': 'secondary'
                                    };
                                    return `<span class="badge bg-${statusColors[data] || 'secondary'} payway-status-badge">${data}</span>`;
                                }
                            },
                            {
                                data: 'trial_ends_at',
                                name: 'trial_ends_at',
                                render: function(data, type, row) {
                                    if (!data) return '<span class="text-muted">No trial</span>';
                                    const date = new Date(data);
                                    const now = new Date();
                                    const isExpired = date < now;
                                    const daysLeft = Math.ceil((date - now) / (1000 * 60 * 60 * 24));

                                    return `
                                        <span class="badge bg-${isExpired ? 'warning' : 'info'}">
                                            ${date.toLocaleDateString()}
                                            ${!isExpired ? `<br><small>${daysLeft} days left</small>` : '<br><small>Expired</small>'}
                                        </span>
                                    `;
                                }
                            },
                            {
                                data: 'ends_at',
                                name: 'ends_at',
                                render: function(data, type, row) {
                                    if (!data) return '<span class="text-muted">No billing date</span>';
                                    const date = new Date(data);
                                    return `<small>${date.toLocaleDateString()}</small>`;
                                }
                            },
                            {
                                data: null,
                                name: 'payment_method',
                                render: function(data, type, row) {
                                    if (!row.card_brand || !row.card_last_four) {
                                        return '<span class="text-muted">No card</span>';
                                    }
                                    return `
                                        <div class="d-flex align-items-center">
                                            <i class="fab fa-cc-${row.card_brand.toLowerCase()} me-1"></i>
                                            <span>****${row.card_last_four}</span>
                                            <br><small class="text-muted">${row.card_expiration || ''}</small>
                                        </div>
                                    `;
                                }
                            },
                            {
                                data: null,
                                name: 'actions',
                                orderable: false,
                                searchable: false,
                                render: function(data, type, row) {
                                    let buttons = '';

                                    // View button
                                    buttons += `<button class="btn btn-sm btn-info viewBtn me-1" data-id="${row.id}" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>`;

                                    // Edit button
                                    buttons += `<button class="btn btn-sm btn-warning editBtn me-1" data-id="${row.id}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>`;

                                    // Action buttons based on status
                                    if (row.status === 'active' && !row.canceled_at) {
                                        buttons += `<button class="btn btn-sm btn-danger cancelBtn me-1" data-id="${row.id}" title="Cancel">
                                            <i class="fas fa-ban"></i>
                                        </button>`;

                                        buttons += `<button class="btn btn-sm btn-success processPaymentBtn me-1" data-id="${row.id}" title="Process Payment">
                                            <i class="fas fa-credit-card"></i>
                                        </button>`;
                                    }

                                    if (row.canceled_at) {
                                        buttons += `<button class="btn btn-sm btn-success reactivateBtn me-1" data-id="${row.id}" title="Reactivate">
                                            <i class="fas fa-undo"></i>
                                        </button>`;
                                    }

                                    // Delete button
                                    buttons += `<button class="btn btn-sm btn-danger deleteBtn" data-id="${row.id}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>`;

                                    return `<div class="action-buttons">${buttons}</div>`;
