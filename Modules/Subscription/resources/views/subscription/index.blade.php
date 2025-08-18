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
    </style>
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Subscriptions', 'subTitle' => 'Subscription Management'])
    
    <div class="app-content">
        <div class="container-fluid">
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="stats-card">
                        <div class="text-center">
                            <div class="stats-number">{{ $stats['total'] ?? 0 }}</div>
                            <div>Total</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card bg-success">
                        <div class="text-center">
                            <div class="stats-number">{{ $stats['active'] ?? 0 }}</div>
                            <div>Active</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card bg-secondary">
                        <div class="text-center">
                            <div class="stats-number">{{ $stats['inactive'] ?? 0 }}</div>
                            <div>Inactive</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card bg-info">
                        <div class="text-center">
                            <div class="stats-number">{{ $stats['on_trial'] ?? 0 }}</div>
                            <div>On Trial</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card bg-warning">
                        <div class="text-center">
                            <div class="stats-number">{{ $stats['expired'] ?? 0 }}</div>
                            <div>Expired</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="stats-card bg-danger">
                        <div class="text-center">
                            <div class="stats-number">{{ $stats['canceled'] ?? 0 }}</div>
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
                            <div class="stats-number">${{ number_format($revenueStats['monthly_revenue'] ?? 0, 2) }}</div>
                            <div>Monthly Revenue</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card revenue-card">
                        <div class="text-center">
                            <div class="stats-number">${{ number_format($revenueStats['yearly_revenue'] ?? 0, 2) }}</div>
                            <div>Yearly Revenue</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card revenue-card">
                        <div class="text-center">
                            <div class="stats-number">${{ number_format($revenueStats['total_active_value'] ?? 0, 2) }}</div>
                            <div>Total Active Value</div>
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
                                    <i class="fas fa-credit-card me-2"></i>Subscriptions
                                </h5>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-gradient-warning btn-sm" id="showTrashed">
                                    <i class="fas fa-trash-restore me-1"></i>View Trashed
                                </button>
                                <button class="btn btn-gradient-success btn-sm" id="addSubscriptionBtn">
                                    <i class="fas fa-plus me-1"></i>Add Subscription
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
                                            <th width="15%">User</th>
                                            <th width="15%">Plan</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Stripe Status</th>
                                            <th width="12%">Trial Ends</th>
                                            <th width="12%">Ends At</th>
                                            <th width="10%">Card</th>
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
    @include('subscription::subscription.modal')

    @push('scripts')
        <script src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('backend/js/responsive.bootstrap.js') }}"></script>
        <script src="{{ asset('backend/js/module-crud.js') }}"></script>

        <script>
            $(document).ready(function() {
                // Initialize the module CRUD
                initModuleCrud({
                    moduleName: 'subscription',
                    tableId: 'datatable-responsive',
                    modalId: 'subscriptionModal',
                    formId: 'subscriptionForm',
                    createBtnId: 'addSubscriptionBtn',
                    trashedBtnId: 'showTrashed',
                    baseUrl: '/subscriptions',
                    fields: [
                        'id',
                        'user_id',
                        'plan_id',
                        'name',
                        'stripe_id',
                        'stripe_status',
                        'trial_ends_at',
                        'ends_at',
                        'canceled_at',
                        'cancellation_reason',
                        'status',
                        'is_active',
                        'card_brand',
                        'card_last_four',
                        'card_expiration'
                    ]
                });

                // $(document).on('click', '#subscriptionShowModal', function(e) {
                $(document).on('click', '.showBtn1', function(e) {
                    e.preventDefault();
                    const id = $(this).data('id');
                    console.log('click');
                    
                    
                    $.ajax({
                        url: `/subscriptions/show/${id}`,
                        method: 'GET',
                        success: function(response) {
                            console.log(response);
                            
                            if (response.success) {
                                const subscription = response.data;
                                
                                // Populate User Information
                                $('#subscriptionShowModal #user_name').text(subscription.user?.name || 'N/A');
                                $('#subscriptionShowModal #user_email').text(subscription.user?.email || 'N/A');
                                
                                // Populate Plan Information
                                $('#subscriptionShowModal #plan_name').text(subscription.plan?.name || 'Plan Deleted');
                                $('#subscriptionShowModal #plan_price').text(subscription.plan?.formatted_price || 'N/A');
                                $('#subscriptionShowModal #plan_interval').text(subscription.plan?.interval_display || 'N/A');
                                
                                // Populate Subscription Details
                                $('#subscriptionShowModal #subscription_name').text(subscription.name || '-');
                                $('#subscriptionShowModal #stripe_subscription_id').text(subscription.stripe_id || 'N/A');
                                $('#subscriptionShowModal #created_at').text(subscription.created_at || '-');
                                
                                // Status badges
                                const statusBadge = subscription.status === 'active' 
                                    ? '<span class="badge bg-success">Active</span>'
                                    : '<span class="badge bg-secondary">Inactive</span>';
                                $('#subscriptionShowModal #subscription_status_badge').html(statusBadge);
                                
                                // Stripe status badge
                                let stripeStatusBadge = '';
                                switch(subscription.stripe_status) {
                                    case 'active':
                                        stripeStatusBadge = '<span class="badge bg-success">Active</span>';
                                        break;
                                    case 'canceled':
                                        stripeStatusBadge = '<span class="badge bg-danger">Canceled</span>';
                                        break;
                                    case 'incomplete':
                                        stripeStatusBadge = '<span class="badge bg-warning">Incomplete</span>';
                                        break;
                                    case 'past_due':
                                        stripeStatusBadge = '<span class="badge bg-warning">Past Due</span>';
                                        break;
                                    case 'trialing':
                                        stripeStatusBadge = '<span class="badge bg-info">Trialing</span>';
                                        break;
                                    default:
                                        stripeStatusBadge = `<span class="badge bg-secondary">${subscription.stripe_status}</span>`;
                                }
                                $('#subscriptionShowModal #stripe_status_badge').html(stripeStatusBadge);
                                
                                // Payment Information
                                if (subscription.card_brand && subscription.card_last_four) {
                                    $('#subscriptionShowModal #payment_card_brand').text(subscription.card_brand.toUpperCase());
                                    $('#subscriptionShowModal #card_last_four_display').text('••••' + subscription.card_last_four);
                                } else {
                                    $('#subscriptionShowModal #payment_card_brand').text('N/A');
                                    $('#subscriptionShowModal #card_last_four_display').text('N/A');
                                }
                                $('#subscriptionShowModal #card_expiration_display').text(subscription.card_expiration || 'N/A');
                                
                                // Important Dates
                                $('#subscriptionShowModal #trial_ends_at_display').text(subscription.trial_ends_at_formatted || 'No Trial');
                                $('#subscriptionShowModal #ends_at_display').text(subscription.ends_at_formatted || 'No End Date');
                                $('#subscriptionShowModal #canceled_at_display').text(subscription.canceled_at_formatted || 'Not Canceled');
                                
                                // Cancellation reason
                                if (subscription.cancellation_reason) {
                                    $('#subscriptionShowModal #cancellation_reason_display').text(subscription.cancellation_reason);
                                    $('#subscriptionShowModal #cancellation_reason_section').show();
                                } else {
                                    $('#subscriptionShowModal #cancellation_reason_section').hide();
                                }
                                
                                // Show the modal
                                $('#subscriptionShowModal').modal('show');
                            } else {
                                toastr.error(response.message || 'Failed to load subscription details');
                            }
                        },
                        error: function() {
                            toastr.error('Failed to load subscription details');
                        }
                    });
                });

                // Handle cancel subscription
                $(document).on('click', '.cancelBtn', function() {
                    const id = $(this).data('id');
                    
                    Swal.fire({
                        title: 'Cancel Subscription?',
                        input: 'textarea',
                        inputLabel: 'Cancellation Reason (Optional)',
                        inputPlaceholder: 'Enter reason for cancellation...',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, cancel it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/subscriptions/cancel/${id}`,
                                type: 'POST',
                                data: {
                                    reason: result.value,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    Swal.fire('Canceled!', response.message, 'success');
                                    getData();
                                },
                                error: function() {
                                    Swal.fire('Error!', 'Failed to cancel subscription.', 'error');
                                }
                            });
                        }
                    });
                });

                // Handle reactivate subscription
                $(document).on('click', '.reactivateBtn', function() {
                    const id = $(this).data('id');
                    
                    Swal.fire({
                        title: 'Reactivate Subscription?',
                        text: 'This will reactivate the canceled subscription.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, reactivate it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/subscriptions/reactivate/${id}`,
                                type: 'POST',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    Swal.fire('Reactivated!', response.message, 'success');
                                    getData();
                                },
                                error: function() {
                                    Swal.fire('Error!', 'Failed to reactivate subscription.', 'error');
                                }
                            });
                        }
                    });
                });

                // Load plans based on user selection (if needed)
                $('#user_id').on('change', function() {
                    const userId = $(this).val();
                    if (userId) {
                        // You can load user-specific plans here if needed
                    }
                });
            });
        </script>
    @endpush
@endsection

