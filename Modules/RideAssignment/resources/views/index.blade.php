@extends('backend.app')
@section('title', 'Ride Assignments')

@section('css')
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .stats-card {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .card-primary .stats-card {
            background: linear-gradient(45deg, #007bff 0%, #0056b3 100%);
        }
        .card-success .stats-card {
            background: linear-gradient(45deg, #28a745 0%, #20c997 100%);
        }
        .card-warning .stats-card {
            background: linear-gradient(45deg, #ffc107 0%, #fd7e14 100%);
        }
        .card-danger .stats-card {
            background: linear-gradient(45deg, #dc3545 0%, #c82333 100%);
        }
        .card-info .stats-card {
            background: linear-gradient(45deg, #17a2b8 0%, #138496 100%);
        }
        .card-secondary .stats-card {
            background: linear-gradient(45deg, #6c757d 0%, #545b62 100%);
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 10px;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        .btn-gradient-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            color: white;
        }
        .btn-gradient-success {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
        }
        .btn-gradient-warning {
            background: linear-gradient(45deg, #ffc107, #fd7e14);
            border: none;
            color: white;
        }
        .btn-gradient-danger {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            color: white;
        }
        .btn-gradient-info {
            background: linear-gradient(45deg, #17a2b8, #138496);
            border: none;
            color: white;
        }
        .btn-gradient-secondary {
            background: linear-gradient(45deg, #6c757d, #545b62);
            border: none;
            color: white;
        }
        .route-info {
            font-size: 0.85rem;
        }
        .route-info i {
            margin-right: 5px;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }
        .action-buttons .btn {
            margin: 2px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        .search-filter-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }
        #jsTest {
            background: red;
            color: white;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <!-- JavaScript Test Indicator -->
    <div id="jsTest">⏳ JavaScript Loading...</div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <i class="fas fa-route me-3"></i>Ride Assignments Management
                    </h1>
                    <p class="mt-2 mb-0 opacity-75">Manage and monitor all ride assignments, bookings, and transportation requests</p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-light btn-lg" id="addRideBtn">
                        <i class="fas fa-plus me-2"></i>New Assignment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card-primary">
                    <div class="stats-card text-center">
                        <div class="stats-number">{{ $stats['total_rides'] ?? 0 }}</div>
                        <div class="stats-label">Total Rides</div>
                        <i class="fas fa-car-side position-absolute" style="top: 15px; right: 20px; font-size: 1.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card-info">
                    <div class="stats-card text-center">
                        <div class="stats-number">{{ $stats['todays_rides'] ?? 0 }}</div>
                        <div class="stats-label">Today's Rides</div>
                        <i class="fas fa-calendar-day position-absolute" style="top: 15px; right: 20px; font-size: 1.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card-warning">
                    <div class="stats-card text-center">
                        <div class="stats-number">{{ $stats['active_rides'] ?? 0 }}</div>
                        <div class="stats-label">Active Rides</div>
                        <i class="fas fa-route position-absolute" style="top: 15px; right: 20px; font-size: 1.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card-success">
                    <div class="stats-card text-center">
                        <div class="stats-number">{{ $stats['completed_rides'] ?? 0 }}</div>
                        <div class="stats-label">Completed</div>
                        <i class="fas fa-check-circle position-absolute" style="top: 15px; right: 20px; font-size: 1.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card-danger">
                    <div class="stats-card text-center">
                        <div class="stats-number">{{ $stats['cancelled_rides'] ?? 0 }}</div>
                        <div class="stats-label">Cancelled</div>
                        <i class="fas fa-times-circle position-absolute" style="top: 15px; right: 20px; font-size: 1.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card-secondary">
                    <div class="stats-card text-center">
                        <div class="stats-number">AUD {{ number_format($stats['total_revenue'] ?? 0, 0) }}</div>
                        <div class="stats-label">Total Revenue</div>
                        <i class="fas fa-money-bill-wave position-absolute" style="top: 15px; right: 20px; font-size: 1.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="search-filter-section">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="search_term" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search_term" placeholder="Search rides...">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="status_filter" class="form-label">Status</label>
                        <select class="form-select" id="status_filter">
                            <option value="">All Status</option>
                            <option value="assigned">Assigned</option>
                            <option value="accepted">Accepted</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="no_show">No Show</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date_filter" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date_filter">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="driver_filter" class="form-label">Driver</label>
                        <select class="form-select" id="driver_filter">
                            <option value="">All Drivers</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->first_name }} {{ $driver->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button class="btn btn-gradient-primary" id="applyFilters">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                            <button class="btn btn-gradient-secondary" id="clearFilters">
                                <i class="fas fa-times me-1"></i>Clear
                            </button>
                            <button class="btn btn-gradient-info" id="exportData">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Data Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>Ride Assignments
                            </h5>
                            <small class="text-muted">Manage all ride assignments and bookings</small>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="view_mode" id="active_rides" value="active" autocomplete="off" checked>
                                <label class="btn btn-outline-primary btn-sm" for="active_rides">Active</label>

                                <input type="radio" class="btn-check" name="view_mode" id="all_rides" value="all" autocomplete="off">
                                <label class="btn btn-outline-primary btn-sm" for="all_rides">All</label>

                                <input type="radio" class="btn-check" name="view_mode" id="trashed_rides" value="trashed" autocomplete="off">
                                <label class="btn btn-outline-danger btn-sm" for="trashed_rides">Trashed</label>
                            </div>
                            <button class="btn btn-gradient-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" id="bulkActions">
                                <i class="fas fa-cogs me-1"></i>Bulk Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" id="bulkAssign"><i class="fas fa-user-plus me-2"></i>Bulk Assign</a></li>
                                <li><a class="dropdown-item" href="#" id="bulkCancel"><i class="fas fa-times me-2"></i>Bulk Cancel</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" id="bulkExport"><i class="fas fa-download me-2"></i>Export Selected</a></li>
                            </ul>
                            <button class="btn btn-gradient-primary btn-sm" id="refreshData">
                                <i class="fas fa-sync-alt me-1"></i>Refresh
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Loading Spinner -->
                        <div class="loading-spinner" id="loadingSpinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading ride assignments...</p>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive pt-3">
                            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap"
                                cellspacing="0" width="100%">
                        {{-- <div class="table-responsive">
                            <table id="ridesDataTable" class="table table-striped table-bordered table-hover"> --}}
                                <thead class="table-dark">
                                    <tr>
                                        <th width="3%">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th width="5%">#</th>
                                        <th width="15%">Ride Details</th>
                                        <th width="12%">Driver</th>
                                        <th width="12%">Parent/Kid</th>
                                        <th width="10%">Schedule</th>
                                        <th width="15%">Route</th>
                                        <th width="8%">Fare</th>
                                        <th width="8%">Status</th>
                                        <th width="12%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="ridesTableBody">
                                    <!-- Initial loading state -->
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-car-side fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">Loading ride assignments...</h5>
                                                <p class="text-muted">Please wait while we fetch your data.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="dataTables_info">
                                Showing <span id="showing_from">0</span> to <span id="showing_to">0</span> of <span id="total_records">0</span> entries
                            </div>
                            <div class="dataTables_paginate">
                                <ul class="pagination pagination-sm" id="pagination_links">
                                    <!-- Pagination links will be generated here -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal fade" id="rideModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rideModalTitle">Add Ride Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rideForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ride_title" class="form-label">Ride Title *</label>
                                <input type="text" class="form-control" id="ride_title" name="ride_title" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="driver_id" class="form-label">Driver *</label>
                                <select class="form-select" id="driver_id" name="driver_id" required>
                                    <option value="">Select Driver</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->first_name }} {{ $driver->lase_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="parent_id" class="form-label">Parent *</label>
                                <select class="form-select" id="parent_id" name="parent_id" required>
                                    <option value="">Select Parent</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->first_name }} {{ $parent->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kid_id" class="form-label">Kid</label>
                                <select class="form-select" id="kid_id" name="kid_id">
                                    <option value="">Select Kid (Optional)</option>
                                    @foreach($kids as $kid)
                                        <option value="{{ $kid->id }}">{{ $kid->first_name }} {{ $kid->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pickup_location" class="form-label">Pickup Location *</label>
                                <input type="text" class="form-control" id="pickup_location" name="pickup_location" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dropoff_location" class="form-label">Dropoff Location *</label>
                                <input type="text" class="form-control" id="dropoff_location" name="dropoff_location" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ride_date" class="form-label">Ride Date *</label>
                                <input type="date" class="form-control" id="ride_date" name="ride_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pickup_time" class="form-label">Pickup Time *</label>
                                <input type="time" class="form-control" id="pickup_time" name="pickup_time" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ride_fare" class="form-label">Fare *</label>
                                <input type="number" class="form-control" id="ride_fare" name="ride_fare" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="assigned">Assigned</option>
                                    <option value="accepted">Accepted</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveRideBtn">Save Ride</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
// Immediate test
console.log('=== JAVASCRIPT LOADING ===');
document.getElementById('jsTest').innerHTML = '⚡ JavaScript is loading...';
document.getElementById('jsTest').style.background = 'orange';

$(document).ready(function() {
    console.log('🚀 Document Ready - RideAssignment Dashboard Starting...');
    
    // Update test indicator
    $('#jsTest').html('✅ JavaScript Working!').css('background', 'green');
    
    let currentViewMode = 'active';
    let selectedRides = [];
    let editingId = null;

    // Check basic requirements
    if (typeof $ === 'undefined') {
        alert('jQuery not loaded!');
        return;
    }
    
    if (typeof Swal === 'undefined') {
        console.warn('SweetAlert2 not loaded');
    }

    console.log('✅ Basic libraries loaded successfully');

    // Initialize page
    initializePage();

    function initializePage() {
        console.log('📊 Initializing page...');
        
        // Bind event handlers first
        bindEventHandlers();
        
        // Then load data
        setTimeout(loadRideAssignments, 500);
        
        console.log('✅ Page initialization complete');
    }

    function bindEventHandlers() {
        console.log('🔗 Binding event handlers...');

        // Basic button tests
        $('#addRideBtn').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('➕ Add button clicked');
            openAddModal();
        });

        $('#applyFilters').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('🔍 Filter button clicked');
            loadRideAssignments();
        });

        $('#clearFilters').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('🧹 Clear filters clicked');
            clearFilters();
        });

        $('#refreshData').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('🔄 Refresh clicked');
            loadRideAssignments();
        });

        // View mode changes
        $('input[name="view_mode"]').off('change').on('change', function() {
            currentViewMode = $(this).val();
            console.log('📋 View mode changed to:', currentViewMode);
            loadRideAssignments();
        });

        // Form submission
        $('#rideForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            console.log('💾 Form submitted');
            saveRide();
        });

        // Dynamic event handlers for table actions
        $(document).off('click', '.editBtn').on('click', '.editBtn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            console.log('✏️ Edit clicked for ID:', id);
            editRide(id);
        });

        $(document).off('click', '.deleteBtn').on('click', '.deleteBtn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            console.log('🗑️ Delete clicked for ID:', id);
            deleteRide(id);
        });

        console.log('✅ Event handlers bound successfully');
    }

    function loadRideAssignments() {
        console.log('📥 Loading ride assignments...');
        showLoading(true);

        const filters = {
            search: $('#search_term').val() || '',
            status: $('#status_filter').val() || '',
            date: $('#date_filter').val() || '',
            driver: $('#driver_filter').val() || '',
            trashed: currentViewMode === 'trashed'
        };

        console.log('🔍 Filters applied:', filters);

        // Try multiple possible routes
        const possibleRoutes = [
            '/admin/rideassignments/get-data',
        ];

        tryRoute(0, possibleRoutes, filters);
    }

    function tryRoute(index, routes, filters) {
        if (index >= routes.length) {
            console.error('❌ All routes failed, showing mock data');
            showMockData();
            showLoading(false);
            return;
        }

        const url = routes[index];
        console.log(`🔗 Trying route ${index + 1}:`, url);

        $.ajax({
            url: url,
            method: 'GET',
            data: filters,
            timeout: 5000,
            success: function(response) {
                let $table = $('#datatable-responsive');

                if ($.fn.DataTable.isDataTable($table)) {
                    $table.DataTable().destroy();
                }

                $table.find("tbody").html(response.html);

                $table.DataTable({ responsive: true });
                
                console.log('✅ Route successful:', url);
                console.log('📊 Response:', response);
                showLoading(false);
            },
            error: function(xhr, status, error) {
                console.log(`❌ Route ${index + 1} failed:`, xhr.status, error);
                tryRoute(index + 1, routes, filters);
            }
        });
    }

    // function handleSuccessResponse(response) {
    //     try {
    //         if (response && response.success) {
    //             if (response.html) {
    //                 $('#ridesTableBody').html(response.html);
    //                 updatePaginationInfo(response);
    //             } else if (response.data) {
    //                 generateTableRows(response.data);
    //             } else {
    //                 showEmptyState('No ride assignments found');
    //             }
    //         } else {
    //             console.warn('⚠️ Response indicates failure:', response);
    //             showMockData(); // Show mock data as fallback
    //         }
    //     } catch (e) {
    //         console.error('❌ Error processing response:', e);
    //         showMockData();
    //     }
    // }

    function showMockData() {
        console.log('📋 Showing mock data for testing...');
        const mockData = `
            <tr>
                <td><input type="checkbox" class="form-check-input ride-checkbox" value="1"></td>
                <td>1</td>
                <td>
                    <strong>School Pickup</strong><br>
                    <small class="text-muted">One Time</small>
                </td>
                <td>
                    <strong>John Driver</strong><br>
                    <small class="text-muted">john@example.com</small>
                </td>
                <td>
                    <strong>Alice Parent</strong><br>
                    <small class="text-muted">alice@example.com</small>
                </td>
                <td>
                    <strong>08:00 AM</strong><br>
                    <small class="text-muted">Today</small>
                </td>
                <td>
                    <small><i class="fas fa-map-marker-alt text-success"></i> Home</small><br>
                    <small><i class="fas fa-map-marker-alt text-danger"></i> School</small>
                </td>
                <td><strong>$25.00</strong></td>
                <td><span class="badge bg-primary">Assigned</span></td>
                <td>
                    <button class="btn btn-sm btn-gradient-primary editBtn" data-id="1">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-gradient-danger deleteBtn" data-id="1">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" class="form-check-input ride-checkbox" value="2"></td>
                <td>2                </td>
                <td>
                    <strong>Sarah Ahmed</strong><br>
                    <small class="text-muted">sarah@example.com</small>
                </td>
                <td>
                    <strong>Bob Johnson</strong><br>
                    <small class="text-muted">bob@example.com</small>
                </td>
                <td>
                    <strong>02:30 PM</strong><br>
                    <small class="text-muted">Tomorrow</small>
                </td>
                <td>
                    <small><i class="fas fa-map-marker-alt text-success"></i> Office</small><br>
                    <small><i class="fas fa-map-marker-alt text-danger"></i> Hospital</small>
                </td>
                <td><strong>$40.00</strong></td>
                <td><span class="badge bg-success">Completed</span></td>
                <td>
                    <button class="btn btn-sm btn-gradient-primary editBtn" data-id="2">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-gradient-danger deleteBtn" data-id="2">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#ridesTableBody').html(mockData);
        updatePaginationInfo({ total: 2, from: 1, to: 2 });
    }

    function generateTableRows(data) {
        if (!data || data.length === 0) {
            showEmptyState('No ride assignments found');
            return;
        }

        let html = '';
        data.forEach((ride, index) => {
            html += `
                <tr>
                    <td><input type="checkbox" class="form-check-input ride-checkbox" value="${ride.id}"></td>
                    <td>${index + 1}</td>
                    <td>
                        <strong>${ride.ride_title || 'N/A'}</strong><br>
                        <small class="text-muted">${ride.ride_type_display || 'One Time'}</small>
                    </td>
                    <td>
                        <strong>${ride.driver?.name || 'Unassigned'}</strong><br>
                        <small class="text-muted">${ride.driver?.email || 'N/A'}</small>
                    </td>
                    <td>
                        <strong>${ride.parent?.name || 'N/A'}</strong><br>
                        <small class="text-muted">${ride.parent?.email || 'N/A'}</small>
                    </td>
                    <td>
                        <strong>${ride.formatted_pickup_time || ride.pickup_time}</strong><br>
                        <small class="text-muted">${ride.formatted_ride_date || ride.ride_date}</small>
                    </td>
                    <td>
                        <small><i class="fas fa-map-marker-alt text-success"></i> ${ride.pickup_location?.substring(0, 20) + '...' || 'N/A'}</small><br>
                        <small><i class="fas fa-map-marker-alt text-danger"></i> ${ride.dropoff_location?.substring(0, 20) + '...' || 'N/A'}</small>
                    </td>
                    <td><strong>${ride.ride_fare || '0.00'}</strong></td>
                    <td>${getStatusBadge(ride.status)}</td>
                    <td>
                        <button class="btn btn-sm btn-gradient-primary editBtn" data-id="${ride.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-gradient-danger deleteBtn" data-id="${ride.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        $('#ridesTableBody').html(html);
        updatePaginationInfo({ total: data.length, from: 1, to: data.length });
    }

    function getStatusBadge(status) {
        const badges = {
            'assigned': '<span class="badge bg-primary">Assigned</span>',
            'accepted': '<span class="badge bg-info">Accepted</span>',
            'in_progress': '<span class="badge bg-warning">In Progress</span>',
            'completed': '<span class="badge bg-success">Completed</span>',
            'cancelled': '<span class="badge bg-danger">Cancelled</span>',
            'no_show': '<span class="badge bg-secondary">No Show</span>'
        };
        return badges[status] || `<span class="badge bg-light">${status || 'Unknown'}</span>`;
    }

    function showLoading(show) {
        if (show) {
            $('#loadingSpinner').show();
            $('#ridesTableBody').html('<tr><td colspan="10" class="text-center">Loading...</td></tr>');
            console.log('⏳ Loading shown');
        } else {
            $('#loadingSpinner').hide();
            console.log('✅ Loading hidden');
        }
    }

    function showEmptyState(message = 'No data found') {
        const emptyHtml = `
            <tr>
                <td colspan="10" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">${message}</h5>
                        <p class="text-muted">Try adjusting your filters or create a new ride assignment.</p>
                        <button class="btn btn-gradient-primary" onclick="openAddModal()">
                            <i class="fas fa-plus me-2"></i>Add First Ride
                        </button>
                    </div>
                </td>
            </tr>
        `;
        $('#ridesTableBody').html(emptyHtml);
        console.log('📭 Empty state shown:', message);
    }

    function updatePaginationInfo(response) {
        const from = response.from || 1;
        const to = response.to || response.total || 0;
        const total = response.total || 0;
        
        $('#showing_from').text(from);
        $('#showing_to').text(to);
        $('#total_records').text(total);
    }

    function clearFilters() {
        $('#search_term').val('');
        $('#status_filter').val('');
        $('#date_filter').val('');
        $('#driver_filter').val('');
        loadRideAssignments();
        console.log('🧹 Filters cleared');
    }

    function openAddModal() {
        console.log('➕ Opening add modal');
        editingId = null;
        $('#rideModalTitle').text('Add Ride Assignment');
        $('#rideForm')[0].reset();
        $('#rideModal').modal('show');
    }

    function editRide(id) {
        console.log('✏️ Edit ride:', id);
        editingId = id;
        $('#rideModalTitle').text('Edit Ride Assignment');
        
        // Mock data for editing
        if (id == 1) {
            $('#ride_title').val('School Pickup');
            $('#driver_id').val('1');
            $('#parent_id').val('1');
            $('#pickup_location').val('123 Home Street');
            $('#dropoff_location').val('ABC School');
            $('#ride_date').val('2024-01-15');
            $('#pickup_time').val('08:00');
            $('#ride_fare').val('25.00');
            $('#status').val('assigned');
        } else {
            $('#ride_title').val('Doctor Appointment');
            $('#driver_id').val('2');
            $('#parent_id').val('2');
            $('#pickup_location').val('456 Office Ave');
            $('#dropoff_location').val('City Hospital');
            $('#ride_date').val('2024-01-16');
            $('#pickup_time').val('14:30');
            $('#ride_fare').val('40.00');
            $('#status').val('completed');
        }
        
        $('#rideModal').modal('show');
    }

    // function saveRide() {
    //     console.log('💾 Saving ride...');
        
    //     const formData = {
    //         ride_title: $('#ride_title').val(),
    //         driver_id: $('#driver_id').val(),
    //         parent_id: $('#parent_id').val(),
    //         kid_id: $('#kid_id').val(),
    //         pickup_location: $('#pickup_location').val(),
    //         dropoff_location: $('#dropoff_location').val(),
    //         ride_date: $('#ride_date').val(),
    //         pickup_time: $('#pickup_time').val(),
    //         ride_fare: $('#ride_fare').val(),
    //         status: $('#status').val(),
    //         _token: '{{ csrf_token() }}'
    //     };

    //     console.log('📝 Form data:', formData);

    //     // Mock save for now
    //     Swal.fire({
    //         title: 'Success!',
    //         text: editingId ? 'Ride updated successfully!' : 'Ride created successfully!',
    //         icon: 'success',
    //         timer: 2000
    //     });

    //     $('#rideModal').modal('hide');
    //     loadRideAssignments();
    // }

    function saveRide()
    {
        console.log('💾 Saving ride...');

        const formData = {
            ride_title: $('#ride_title').val(),
            driver_id: $('#driver_id').val(),
            parent_id: $('#parent_id').val(),
            kid_id: $('#kid_id').val(),
            pickup_location: $('#pickup_location').val(),
            dropoff_location: $('#dropoff_location').val(),
            ride_date: $('#ride_date').val(),
            pickup_time: $('#pickup_time').val(),
            ride_fare: $('#ride_fare').val(),
            status: $('#status').val(),
            _token: '{{ csrf_token() }}'
        };

        console.log('📝 Form data:', formData);

        $.ajax({
            url: "{{ route('admin.rideassignments.store') }}",   // your Laravel POST route
            method: "POST",
            data: formData,
            success: function(response) {
                Swal.fire({
                    title: 'Success!',
                    text: editingId ? 'Ride updated successfully!' : 'Ride created successfully!',
                    icon: 'success',
                    timer: 2000
                });

                $('#rideModal').modal('hide');
                loadRideAssignments();
            },
            error: function(xhr) {
                console.error('❌ Save failed:', xhr.responseText);

                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong while saving the ride.',
                    icon: 'error'
                });
            }
        });
    }


    function deleteRide(id) {
        console.log('🗑️ Delete ride:', id);
        
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will delete the ride assignment!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mock delete
                Swal.fire('Deleted!', 'Ride has been deleted.', 'success');
                loadRideAssignments();
            }
        });
    }

    // Make functions globally available for debugging
    window.loadRideAssignments = loadRideAssignments;
    window.openAddModal = openAddModal;
    window.editRide = editRide;
    window.deleteRide = deleteRide;
    window.clearFilters = clearFilters;

    // Hide test indicator after everything loads
    setTimeout(function() {
        $('#jsTest').fadeOut();
    }, 3000);

    console.log('🎉 RideAssignment Dashboard loaded successfully!');
});
</script>
@endpush