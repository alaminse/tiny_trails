@extends('backend.app')
@section('title', 'Plans Management')
@section('css')
    <link href="{{ asset('backend/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/image-preview.css') }}" rel="stylesheet">
    <style>
        .stats-card {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
        }
        .plan-features {
            max-width: 200px;
        }
        .plan-features ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .plan-features li {
            background: #f8f9fa;
            padding: 2px 8px;
            margin: 2px 0;
            border-radius: 3px;
            font-size: 0.8rem;
        }
    </style>
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Plans', 'subTitle' => 'Subscription Plans Management'])
    
    <div class="app-content">
        <div class="container-fluid">
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="stats-number" id="total-plans">{{ $stats['total'] ?? 0 }}</div>
                                <div>Total Plans</div>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-layer-group fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card bg-success">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="stats-number" id="active-plans">{{ $stats['active'] ?? 0 }}</div>
                                <div>Active Plans</div>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-check-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card bg-warning">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="stats-number" id="inactive-plans">{{ $stats['inactive'] ?? 0 }}</div>
                                <div>Inactive Plans</div>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-pause-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card bg-info">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <div class="stats-number" id="total-subscriptions">{{ $stats['subscriptions'] ?? 0 }}</div>
                                <div>Total Subscriptions</div>
                            </div>
                            <div class="ms-3">
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title">
                                <h5 class="mb-0">
                                    <i class="fas fa-layer-group me-2"></i>Subscription Plans
                                </h5>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-gradient-warning btn-sm" id="showTrashed">
                                    <i class="fas fa-trash-restore me-1"></i>View Trashed
                                </button>
                                <button class="btn btn-gradient-success btn-sm" id="addPlanBtn">
                                    <i class="fas fa-plus me-1"></i>Add New Plan
                                </button>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap"
                                    cellspacing="0" width="100%">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="20%">Plan Name</th>
                                            <th width="15%">Price</th>
                                            <th width="8%">Currency</th>
                                            <th width="12%">Billing Cycle</th>
                                            <th width="15%">Pickup Type</th>
                                            <th width="10%">Status</th>
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
        </div>
    </div>

    <!-- Modals -->
    @include('subscription::plan.modal')
    @include('subscription::plan.show_modal')

    @push('scripts')
        <script src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('backend/js/responsive.bootstrap.js') }}"></script>
        <script src="{{ asset('backend/js/module-crud.js') }}"></script>
        <script src="{{ asset('backend/js/image-preview.js') }}"></script>

        <script>
            $(document).ready(function() {
                // Initialize the module CRUD
                 const plansCrud = initModuleCrud({
                    moduleName: 'plan',
                    tableId: 'datatable-responsive',
                    modalId: 'planModal',
                    userShowModal: 'planShowModal',
                    formId: 'planForm',
                    createBtnId: 'addPlanBtn',
                    trashedBtnId: 'showTrashed',
                    baseUrl: '/plans',
                    fields: [
                        'id',
                        'pickup_type_id',
                        'name',
                        'slug',
                        'description',
                        'price',
                        'sell_price',
                        'currency',
                        'interval',
                        'interval_count',
                        'features',
                        'status',
                        'is_active',
                        'sort_order'
                    ]
                });

                // Handle edit button click
                $(document).on("click", `.editBtn`, function (e) {
                    modalOpen();
                });

                // Handle add button click
                $('#addPlanBtn').on('click', function() {
                    $('#planModal').modal('show');
                    modalOpen();
                });

                // Handle show modal data population
                $(document).on('click', '.showBtn', function() {
                    const id = $(this).data('id');
                    
                    $.ajax({
                        url: `admin/plans/show/${id}`,
                        method: 'GET',
                        success: function(response) {
                            const plan = response.data;
                            
                            // Populate basic info
                            $('#planShowModal #name').text(plan.name || '-');
                            $('#planShowModal #slug').text(plan.slug || '-');
                            $('#planShowModal #description').text(plan.description || 'No description provided');
                            $('#planShowModal #pickup_type_name').text(plan.pickup_type?.name || 'N/A');
                            
                            // Populate pricing info
                            $('#planShowModal #price').text(plan.formatted_price || '-');
                            $('#planShowModal #sell_price').text(plan.formatted_sell_price || '-');
                            $('#planShowModal #currency').text(plan.currency || '-');
                            $('#planShowModal #interval_display').text(plan.interval_display || '-');
                            
                            // Status badge
                            const statusBadge = plan.status === 'active' 
                                ? '<span class="badge bg-success">Active</span>'
                                : '<span class="badge bg-secondary">Inactive</span>';
                            $('#planShowModal #status_badge').html(statusBadge);
                            
                            // Features
                            let featuresHtml = '';
                            if (plan.features && plan.features.length > 0) {
                                featuresHtml = '<ul class="list-unstyled">';
                                plan.features.forEach(feature => {
                                    featuresHtml += `<li><i class="fas fa-check text-success me-2"></i>${feature}</li>`;
                                });
                                featuresHtml += '</ul>';
                            } else {
                                featuresHtml = '<p class="text-muted">No features listed</p>';
                            }
                            $('#planShowModal #features_list').html(featuresHtml);
                            
                            // Statistics
                            $('#planShowModal #total_subscriptions').text(plan.subscriptions_count || '0');
                            $('#planShowModal #active_subscriptions').text(plan.active_subscriptions_count || '0');
                            $('#planShowModal #sort_order').text(plan.sort_order || '0');
                            
                            $('#planShowModal').modal('show');
                        },
                        error: function() {
                            toastr.error('Failed to load plan details');
                        }
                    });
                });

                // Handle duplicate button
                $(document).on('click', '.duplicateBtn', function() {
                    const id = $(this).data('id');
                    
                    Swal.fire({
                        title: 'Duplicate Plan?',
                        text: 'This will create a copy of this plan.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, duplicate it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/plans/duplicate/${id}`,
                                type: 'POST',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    Swal.fire('Duplicated!', response.message, 'success');
                                    // Reload the data table
                                    // getData();
                                    plansCrud.getData();
                                },
                                error: function() {
                                    Swal.fire('Error!', 'Failed to duplicate the plan.', 'error');
                                }
                            });
                        }
                    });
                });

                // Update statistics periodically
                function updateStats() {
                    $.get('/admin/plans/stats', function(data) {
                        if (data.success) {
                            $('#total-plans').text(data.data.total || 0);
                            $('#active-plans').text(data.data.active || 0);
                            $('#inactive-plans').text(data.data.inactive || 0);
                            $('#total-subscriptions').text(data.data.subscriptions || 0);
                        }
                    });
                }

                // Update stats every 30 seconds
                setInterval(updateStats, 30000);
                
                // Search functionality
                $('#searchInput').on('keyup', function() {
                    const searchTerm = $(this).val();
                    if (searchTerm.length >= 3 || searchTerm.length === 0) {
                        searchPlans(searchTerm);
                    }
                });

                function searchPlans(term) {
                    $.ajax({
                        url: 'admin/plans/search',
                        method: 'GET',
                        data: { term: term },
                        success: function(response) {
                            if (response.success) {
                                const html = generateTableRows(response.data.data);
                                $('#datatable-responsive tbody').html(html);
                            }
                        }
                    });
                }

                function generateTableRows(plans) {
                    let html = '';
                    plans.forEach((plan, index) => {
                        const statusBadge = plan.status === 'active' 
                            ? '<span class="badge bg-success">Active</span>'
                            : '<span class="badge bg-secondary">Inactive</span>';
                        
                        const pickupTypeBadge = plan.pickup_type 
                            ? `<span class="badge bg-info">${plan.pickup_type.name}</span>`
                            : '<span class="text-muted">N/A</span>';

                        html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>
                                    <div>
                                        <strong>${plan.name}</strong><br>
                                        <small class="text-muted">${plan.slug}</small>
                                    </div>
                                </td>
                                <td>
                                    ${plan.price !== plan.sell_price ? 
                                        `<span class="text-muted text-decoration-line-through">${plan.formatted_price}</span><br>` : ''}
                                    <strong class="text-primary">${plan.formatted_sell_price}</strong>
                                </td>
                                <td>${plan.currency}</td>
                                <td><span class="badge">${plan.interval_display}</span></td>
                                <td>${pickupTypeBadge}</td>
                                <td>${statusBadge}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-gradient-primary btn-sm editBtn" data-id="${plan.id}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-gradient-info btn-sm showBtn" data-id="${plan.id}" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-gradient-warning btn-sm duplicateBtn" data-id="${plan.id}" title="Duplicate">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <button class="btn btn-gradient-danger btn-sm deleteBtn" data-id="${plan.id}" title="Move to Trash">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    return html;
                }
            });
        </script>
    @endpush
@endsection