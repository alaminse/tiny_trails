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
    @include('subscription::subscription.show_modal')

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
                    userShowModal: 'subscriptionShowModal',
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

{{-- subscription/table-rows.blade.php --}}
@foreach ($data as $index => $subscription)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>
        <div>
            <strong>{{ $subscription->user->name ?? 'N/A' }}</strong><br>
            <small class="text-muted">{{ $subscription->user->email ?? 'N/A' }}</small>
        </div>
    </td>
    <td>
        @if($subscription->plan)
            <div>
                <strong>{{ $subscription->plan->name }}</strong><br>
                <small class="text-muted">{{ $subscription->plan->formatted_sell_price }}/{{ $subscription->plan->interval_display }}</small>
            </div>
        @else
            <span class="text-muted">Plan Deleted</span>
        @endif
    </td>
    <td>
        @if ($subscription->status == 'active')
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-secondary">Inactive</span>
        @endif
    </td>
    <td>
        @switch($subscription->stripe_status)
            @case('active')
                <span class="badge bg-success">Active</span>
                @break
            @case('canceled')
                <span class="badge bg-danger">Canceled</span>
                @break
            @case('incomplete')
                <span class="badge bg-warning">Incomplete</span>
                @break
            @case('past_due')
                <span class="badge bg-warning">Past Due</span>
                @break
            @case('trialing')
                <span class="badge bg-info">Trialing</span>
                @break
            @default
                <span class="badge bg-secondary">{{ ucfirst($subscription->stripe_status) }}</span>
        @endswitch
    </td>
    <td>
        @if($subscription->trial_ends_at)
            {{ $subscription->trial_ends_at->format('M d, Y') }}
            @if($subscription->isOnTrial())
                <br><small class="text-success">Active Trial</small>
            @endif
        @else
            <span class="text-muted">No Trial</span>
        @endif
    </td>
    <td>
        @if($subscription->ends_at)
            {{ $subscription->ends_at->format('M d, Y') }}
            @if($subscription->hasExpired())
                <br><small class="text-danger">Expired</small>
            @endif
        @else
            <span class="text-muted">No End Date</span>
        @endif
    </td>
    <td>
        @if($subscription->card_brand && $subscription->card_last_four)
            <div>
                <i class="fab fa-cc-{{ strtolower($subscription->card_brand) }}"></i>
                ••••{{ $subscription->card_last_four }}
            </div>
            @if($subscription->card_expiration)
                <small class="text-muted">{{ $subscription->card_expiration }}</small>
            @endif
        @else
            <span class="text-muted">No Card</span>
        @endif
    </td>
    <td>
        @if ($subscription->trashed())
            <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $subscription->id }}" title="Restore">
                <i class="fas fa-undo"></i>
            </button>
            <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $subscription->id }}" title="Delete Permanently">
                <i class="fas fa-trash-alt"></i>
            </button>
        @else
            <div class="btn-group" role="group">
                <button class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $subscription->id }}" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-gradient-info btn-sm showBtn" data-id="{{ $subscription->id }}" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
                @if($subscription->isCanceled())
                    <button class="btn btn-gradient-success btn-sm reactivateBtn" data-id="{{ $subscription->id }}" title="Reactivate">
                        <i class="fas fa-play"></i>
                    </button>
                @else
                    <button class="btn btn-gradient-warning btn-sm cancelBtn" data-id="{{ $subscription->id }}" title="Cancel">
                        <i class="fas fa-pause"></i>
                    </button>
                @endif
                <button class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $subscription->id }}" title="Move to Trash">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        @endif
    </td>
</tr>
@endforeach