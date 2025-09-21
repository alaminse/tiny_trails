{{-- resources/views/subscription/create.blade.php --}}
@extends('backend.app')
@section('title', 'Create New Subscription')
@section('css')
    <style>
        .payment-card {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .test-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .connection-status {
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .connection-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .connection-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin: 0 5px;
            background: #e9ecef;
            color: #6c757d;
        }
        .step.active {
            background: #007bff;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
    </style>
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Subscriptions', 'subTitle' => 'Create New Subscription with PayWay'])

    <div class="app-content">
        <div class="container-fluid">

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step-1">
                    <i class="fas fa-user-circle fa-2x"></i>
                    <div>User & Plan</div>
                </div>
                <div class="step" id="step-2">
                    <i class="fas fa-credit-card fa-2x"></i>
                    <div>Payment Info</div>
                </div>
                <div class="step" id="step-3">
                    <i class="fas fa-address-card fa-2x"></i>
                    <div>Billing Address</div>
                </div>
                <div class="step" id="step-4">
                    <i class="fas fa-check-circle fa-2x"></i>
                    <div>Review & Create</div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-plus-circle me-2"></i>Create New Subscription
                            </h4>
                            <div class="card-tools">
                                <button type="button" class="btn btn-info btn-sm" id="testConnectionBtn">
                                    <i class="fas fa-wifi me-1"></i>Test PayWay Connection
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Connection Status -->
                            <div id="connection-status" style="display: none;"></div>

                            <!-- Alerts Container -->
                            <div id="alerts-container"></div>

                            <!-- PayWay Test Info -->
                            <div class="test-info">
                                <h6><i class="fas fa-info-circle me-2"></i>PayWay Test Mode</h6>
                                <p class="mb-2"><strong>Test Card:</strong> <code>4564710000000004</code></p>
                                <p class="mb-2"><strong>CVN:</strong> <code>847</code></p>
                                <p class="mb-0"><strong>Expiry:</strong> <code>02/29</code></p>
                            </div>

                            <form id="subscription-form" method="POST" action="/admin/payway/subscription/create">
                                @csrf

                                <!-- Step 1: User & Plan Selection -->
                                <div class="form-section" id="section-1">
                                    <h5 class="mb-3"><i class="fas fa-users me-2"></i>User & Plan Information</h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="user_id" class="form-label">Select User <span class="text-danger">*</span></label>
                                                <select name="user_id" id="user_id" class="form-select" required>
                                                    <option value="">Choose User...</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                                            {{ $user->name }} ({{ $user->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="plan_id" class="form-label">Select Plan <span class="text-danger">*</span></label>
                                                <select name="plan_id" id="plan_id" class="form-select" required>
                                                    <option value="">Choose Plan...</option>
                                                    @foreach($plans as $plan)
                                                        <option value="{{ $plan->id }}" 
                                                                data-price="{{ $plan->sell_price > 0 ? $plan->sell_price : $plan->price }}"
                                                                data-name="{{ $plan->name }}">
                                                            {{ $plan->name }} - ${{ number_format($plan->sell_price > 0 ? $plan->sell_price : $plan->price, 2) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Subscription Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" id="name" class="form-control" value="Default Subscription" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="trial_days" class="form-label">Trial Days</label>
                                                <input type="number" name="trial_days" id="trial_days" class="form-control" min="0" max="365" value="0">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select name="status" id="status" class="form-select" required>
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                            Next: Payment Info <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 2: Payment Information -->
                                <div class="form-section" id="section-2" style="display: none;">
                                    <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Information</h5>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="card_number" class="form-label">Card Number <span class="text-danger">*</span></label>
                                                <input type="text" name="card_number" id="card_number" class="form-control"
                                                       placeholder="4564 7100 0000 0004" required maxlength="19" pattern="[0-9\s]{13,19}">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="expiry_month" class="form-label">Month <span class="text-danger">*</span></label>
                                                <select name="expiry_month" id="expiry_month" class="form-select" required>
                                                    <option value="">MM</option>
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                                            {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="expiry_year" class="form-label">Year <span class="text-danger">*</span></label>
                                                <select name="expiry_year" id="expiry_year" class="form-select" required>
                                                    <option value="">YYYY</option>
                                                    @for($i = date('Y'); $i <= date('Y') + 20; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="cvn" class="form-label">CVN <span class="text-danger">*</span></label>
                                                <input type="text" name="cvn" id="cvn" class="form-control"
                                                       placeholder="847" required maxlength="4" pattern="[0-9]{3,4}">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="cardholder_name" class="form-label">Cardholder Name <span class="text-danger">*</span></label>
                                                <input type="text" name="cardholder_name" id="cardholder_name" class="form-control"
                                                       placeholder="Test Card" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary" onclick="prevStep(1)">
                                            <i class="fas fa-arrow-left me-1"></i> Previous
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="nextStep(3)">
                                            Next: Billing Address <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 3: Billing Information -->
                                <div class="form-section" id="section-3" style="display: none;">
                                    <h5 class="mb-3"><i class="fas fa-address-card me-2"></i>Billing Information</h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="billing_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" name="billing_name" id="billing_name" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="billing_email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="billing_email" id="billing_email" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="billing_phone" class="form-label">Phone (Optional)</label>
                                                <input type="tel" name="billing_phone" id="billing_phone" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="billing_address_street1" class="form-label">Street Address <span class="text-danger">*</span></label>
                                                <input type="text" name="billing_address[street1]" id="billing_address_street1" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="billing_address_street2" class="form-label">Street Address 2 (Optional)</label>
                                                <input type="text" name="billing_address[street2]" id="billing_address_street2" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="billing_address_city" class="form-label">City <span class="text-danger">*</span></label>
                                                <input type="text" name="billing_address[city]" id="billing_address_city" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="billing_address_state" class="form-label">State <span class="text-danger">*</span></label>
                                                <select name="billing_address[state]" id="billing_address_state" class="form-select" required>
                                                    <option value="">Choose State...</option>
                                                    <option value="NSW">New South Wales</option>
                                                    <option value="VIC">Victoria</option>
                                                    <option value="QLD">Queensland</option>
                                                    <option value="WA">Western Australia</option>
                                                    <option value="SA">South Australia</option>
                                                    <option value="TAS">Tasmania</option>
                                                    <option value="NT">Northern Territory</option>
                                                    <option value="ACT">Australian Capital Territory</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="billing_address_postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                                <input type="text" name="billing_address[postal_code]" id="billing_address_postal_code"
                                                       class="form-control" required pattern="[0-9]{4}" maxlength="4">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="billing_address_country_code" class="form-label">Country <span class="text-danger">*</span></label>
                                                <select name="billing_address[country_code]" id="billing_address_country_code" class="form-select" required>
                                                    <option value="AU" selected>Australia</option>
                                                    <option value="NZ">New Zealand</option>
                                                    <option value="US">United States</option>
                                                    <option value="GB">United Kingdom</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary" onclick="prevStep(2)">
                                            <i class="fas fa-arrow-left me-1"></i> Previous
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="nextStep(4)">
                                            Review & Create <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 4: Review & Submit -->
                                <div class="form-section" id="section-4" style="display: none;">
                                    <h5 class="mb-3"><i class="fas fa-check-circle me-2"></i>Review & Create Subscription</h5>

                                    <div id="review-summary" class="payment-card">
                                        <h6>Subscription Summary</h6>
                                        <div id="summary-content">
                                            <!-- Will be populated by JavaScript -->
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary" onclick="prevStep(3)">
                                            <i class="fas fa-arrow-left me-1"></i> Previous
                                        </button>
                                        <div>
                                            <button type="button" class="btn btn-info me-2" id="validateFormBtn">
                                                <i class="fas fa-check me-1"></i> Validate Form
                                            </button>
                                            <button type="submit" class="btn btn-success" id="submitBtn">
                                                <i class="fas fa-credit-card me-1"></i> Create Subscription
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Include the subscription management JS -->
    <script src="{{ asset('js/subscription-management.js') }}"></script>
    <script>
        // Initialize the form
        $(document).ready(function() {
            initializeSubscriptionForm();
        });
    </script>
@endpush
