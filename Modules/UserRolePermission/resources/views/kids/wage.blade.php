@extends('backend.app')

@section('title', 'Wage Management')

@section('css')
    <style>
        .info-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-card h5 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 10px;
        }

        .info-item {
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 14px;
        }

        .photo-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #ddd;
        }

        .wage-form-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-top: 20px;
        }

        .form-section-card {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            height: 100%;
        }

        .plans-section-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            color: #2c3e50;
            font-weight: 600;
            font-size: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e9ecef;
        }

        .section-title i {
            color: #007bff;
            margin-right: 8px;
        }

        .plans-wrapper {
            max-height: 660px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .plans-wrapper::-webkit-scrollbar {
            width: 6px;
        }

        .plans-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .plans-wrapper::-webkit-scrollbar-thumb {
            background: #007bff;
            border-radius: 10px;
        }

        .plan-card {
            background: #fff;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow-y: auto;
            max-height: 450px;
        }

        .plan-card::-webkit-scrollbar {
            width: 4px;
        }

        .plan-card::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .plan-card::-webkit-scrollbar-thumb {
            background: #007bff;
            border-radius: 10px;
        }

        .plan-card::-webkit-scrollbar-thumb:hover {
            background: #0056b3;
        }

        .plan-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
            transform: translateY(-2px);
        }

        .plan-card.selected {
            border-color: #007bff;
            background: linear-gradient(135deg, #e7f3ff 0%, #fff 100%);
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.25);
        }

        .plan-card.selected::before {
            content: '\f00c';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            top: 10px;
            right: 10px;
            background: #007bff;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            z-index: 1;
        }

        .plan-badge {
            margin-bottom: 12px;
        }

        .plan-badge .badge {
            font-size: 11px;
            padding: 4px 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .plan-header {
            margin-bottom: 12px;
        }

        .plan-name {
            font-size: 17px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .plan-description {
            color: #6c757d;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 12px;
        }

        .plan-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
        }

        .meta-item {
            background: #f8f9fa;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .meta-item i {
            font-size: 11px;
        }

        .plan-features {
            border-top: 1px solid #e9ecef;
            padding-top: 12px;
            margin-top: 12px;
        }

        .features-title {
            color: #495057;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .feature-item {
            display: flex;
            align-items: start;
            gap: 8px;
            font-size: 12px;
            color: #495057;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .feature-item i {
            color: #28a745;
            font-size: 11px;
            margin-top: 3px;
            flex-shrink: 0;
        }

        .feature-item span {
            flex: 1;
        }

        .selected-plan-box {
            background: linear-gradient(135deg, #e7f3ff 0%, #d0e9ff 100%);
            border: 2px solid #007bff;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }

        .selected-plan-box strong {
            color: #2c3e50;
            font-size: 15px;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
        }

        label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .input-group-text {
            display: flex;
            align-items: center;
            padding: 0.530rem 0.75rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {

            .col-md-5,
            .col-md-7 {
                margin-bottom: 20px;
            }

            .plans-wrapper {
                max-height: 400px;
            }
        }
    </style>
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Kid', 'subTitle' => 'Wage Management'])

    @can('view-kids')
        <div class="container-fluid">
            <!-- Personal Information (Kid) -->
            <div class="info-card">
                <h5><i class="fas fa-child"></i> Personal Information (Kid)</h5>
                <div class="info-row">
                    <div class="info-item">
                        <div class="info-label">First Name</div>
                        <div class="info-value">{{ $data->first_name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Last Name</div>
                        <div class="info-value">{{ $data->last_name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Middle Name</div>
                        <div class="info-value">{{ $data->middle_name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value">{{ $data->dob ? \Carbon\Carbon::parse($data->dob)->format('d M, Y') : 'N/A' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Gender</div>
                        <div class="info-value">{{ $data->gender ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Height (cm)</div>
                        <div class="info-value">{{ $data->height_cm ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Weight (kg)</div>
                        <div class="info-value">{{ $data->weight_kg ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Hair Color</div>
                        <div class="info-value">{{ $data->hair_color ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Eye Color</div>
                        <div class="info-value">{{ $data->eye_color ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Birthmarks</div>
                        <div class="info-value">{{ $data->birthmarks ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">School Name</div>
                        <div class="info-value">{{ $data->school_name ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">School Address</div>
                        <div class="info-value">{{ $data->school_address ?? 'N/A' }}</div>
                    </div>
                </div>
                @if ($data->photo)
                    <div class="mt-3">
                        <div class="info-label">Photo</div>
                        <img src="{{ asset($data->photo) }}" alt="Kid Photo" class="photo-preview">
                    </div>
                @endif
            </div>

            <!-- Emergency Contacts -->
            @if (isset($data->emergency_contacts) && !empty($data->emergency_contacts))
                @php
                    $emergencyContacts = is_string($data->emergency_contacts)
                        ? json_decode($data->emergency_contacts, true)
                        : $data->emergency_contacts;
                @endphp

                @if (is_array($emergencyContacts) && count($emergencyContacts) > 0)
                    <div class="info-card">
                        <h5><i class="fas fa-phone-alt"></i> Emergency Contacts</h5>
                        @foreach ($emergencyContacts as $contact)
                            <div class="info-row">
                                <div class="info-item">
                                    <div class="info-label">Name</div>
                                    <div class="info-value">{{ $contact['name'] ?? 'N/A' }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Relationship</div>
                                    <div class="info-value">{{ $contact['relationship'] ?? 'N/A' }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Phone</div>
                                    <div class="info-value">{{ $contact['phone'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            <!-- Parent Information -->
            @if (isset($data->parent))
                <div class="info-card">
                    <h5><i class="fas fa-user-friends"></i> Parent Information</h5>
                    <div class="info-row">
                        <div class="info-item">
                            <div class="info-label">First Name</div>
                            <div class="info-value">{{ $data->parent->first_name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Last Name</div>
                            <div class="info-value">{{ $data->parent->last_name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $data->parent->email ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Phone</div>
                            <div class="info-value">{{ $data->parent->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Gender</div>
                            <div class="info-value">{{ $data->parent->gender ?? 'N/A' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Parent Address</div>
                            <div class="info-value">{{ $data->parent->address ?? 'N/A' }}</div>
                        </div>
                    </div>
                    @if (isset($data->parent->photo))
                        <div class="mt-3">
                            <div class="info-label">Parent Photo</div>
                            <img src="{{ asset($data->parent->photo) }}" alt="Parent Photo" class="photo-preview">
                        </div>
                    @endif
                </div>
            @endif


            <!-- Location Information -->
            <div class="info-card">
                <h5><i class="fas fa-map-marker-alt"></i> Location Information</h5>
                <div class="info-row">
                    <div class="info-item">
                        <div class="info-label">Pickup Location</div>
                        <div class="info-value">{{ $data->pickup_location ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Dropoff Location</div>
                        <div class="info-value">{{ $data->dropoff_location ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Distance (KM Round trip)</div>
                        <div class="info-value">{{ $data->distance_between_locations ?? 'N/A' }} KM</div>
                    </div>
                </div>
            </div>

            <!-- Wage Form -->
            @can('create-subscription')
                <div class="wage-form-card">
                    <h5 class="mb-4"><i class="fas fa-money-bill-wave"></i> Add Wage Plan</h5>

                    <form action="{{ route('admin.kids.wage.store', $data->id) }}" method="POST" id="wageForm">
                        @csrf

                        <div class="row">
                            <!-- Left Column - Form Fields -->
                            <div class="col-md-5">
                                <div class="form-section-card">
                                    <h6 class="section-title mb-4">
                                        <i class="fas fa-edit"></i> Wage Details
                                    </h6>

                                    <div class="form-group mb-3">
                                        <label for="price">Regular Price <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">৳</span>
                                            </div>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control @error('price') is-invalid @enderror" id="price"
                                                name="price" value="{{ old('price') }}" placeholder="0.00" required>
                                            @error('price')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="sell_price">Selling Price <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">৳</span>
                                            </div>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control @error('sell_price') is-invalid @enderror" id="sell_price"
                                                name="sell_price" value="{{ old('sell_price') }}" placeholder="0.00" required>
                                            @error('sell_price')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> The actual price to be charged
                                        </small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                            id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                        @error('start_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                            id="end_date" name="end_date" value="{{ old('end_date') }}">
                                        @error('end_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> Leave empty for ongoing wage
                                        </small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status"
                                            name="status" required>
                                            <option value="">-- Select Status --</option>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                            </option>
                                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending
                                            </option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="notes">Additional Notes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4"
                                            placeholder="Enter any special instructions or remarks...">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Selected Plan Summary -->
                                    <div id="selectedPlanSummary" class="selected-plan-box" style="display: none;">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <small class="text-muted d-block mb-1">Selected Plan:</small>
                                                <strong id="displayPlanName" class="d-block"></strong>
                                                <small class="text-muted" id="displayPlanTier"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Plan Selection -->
                            <div class="col-md-7">
                                <div class="plans-section-card">
                                    <h6 class="section-title mb-3">
                                        <i class="fas fa-th-large"></i> Select a Plan <span class="text-danger">*</span>
                                    </h6>

                                    <div class="plans-wrapper">
                                        <div class="row">
                                            @forelse($plans as $plan)
                                                @php
                                                    $features = is_array($plan->features)
                                                        ? $plan->features
                                                        : json_decode($plan->features, true) ?? [];
                                                @endphp

                                                <div class="col-md-6 mb-3">
                                                    <div class="plan-card h-100" data-plan-id="{{ $plan->id }}"
                                                        data-name="{{ $plan->name }}"
                                                        data-tier="{{ ucfirst($plan->plan_tier) }}">

                                                        <!-- Plan Badge -->
                                                        <div class="plan-badge">
                                                            @switch($plan->plan_tier)
                                                                @case('basic')
                                                                    <span class="badge badge-secondary">Basic</span>
                                                                @break

                                                                @case('standard')
                                                                    <span class="badge badge-primary">Standard</span>
                                                                @break

                                                                @case('premium')
                                                                    <span class="badge badge-warning">Premium</span>
                                                                @break

                                                                @case('enterprise')
                                                                    <span class="badge badge-success">Enterprise</span>
                                                                @break

                                                                @default
                                                                    <span class="badge badge-info">{{ $plan->plan_tier }}</span>
                                                            @endswitch
                                                        </div>

                                                        <!-- Plan Header -->
                                                        <div class="plan-header">
                                                            <div class="plan-name">{{ $plan->name }}</div>
                                                        </div>

                                                        <!-- Plan Description -->
                                                        @if ($plan->description)
                                                            <div class="plan-description">
                                                                {{ $plan->description }}
                                                            </div>
                                                        @endif

                                                        <!-- Plan Meta Info -->
                                                        @if ($plan->pickup_type_id || $plan->iot_level || $plan->includes_hardware)
                                                            <div class="plan-meta">
                                                                @if ($plan->pickup_type_id)
                                                                    <div class="meta-item">
                                                                        <i class="fas fa-car"></i>
                                                                        <span>Type {{ $plan->pickup_type_id }}</span>
                                                                    </div>
                                                                @endif

                                                                @if ($plan->iot_level)
                                                                    <div class="meta-item">
                                                                        <i class="fas fa-wifi"></i>
                                                                        <span>IoT L{{ $plan->iot_level }}</span>
                                                                    </div>
                                                                @endif

                                                                @if ($plan->includes_hardware)
                                                                    <div class="meta-item">
                                                                        <i class="fas fa-tools"></i>
                                                                        <span>Hardware Included</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        <!-- Plan Features - Show All -->
                                                        @if (!empty($features) && is_array($features))
                                                            <div class="plan-features">
                                                                <div class="features-title">Features:</div>
                                                                @foreach ($features as $feature)
                                                                    <div class="feature-item">
                                                                        <i class="fas fa-check-circle"></i>
                                                                        <span>{{ $feature }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @empty
                                                    <div class="col-12">
                                                        <div class="alert alert-info text-center">
                                                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                                                            <p class="mb-0">No plans available at the moment.</p>
                                                        </div>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <input type="hidden" name="plan_id" id="selectedPlanId" required>
                                        <input type="hidden" name="kid_id" value="{{ $data->id }}">

                                        <div id="planError" class="alert alert-danger mt-3" style="display: none;">
                                            <i class="fas fa-exclamation-triangle"></i> Please select a plan to continue
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('admin.kids.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Wage Plan
                                </button>
                            </div>
                        </form>
                    </div>
                @endcan
            </div>
        @endcan
    @endsection

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Plan selection
                $('.plan-card').on('click', function() {
                    $('.plan-card').removeClass('selected');
                    $(this).addClass('selected');

                    const planId = $(this).data('plan-id');
                    const planName = $(this).data('name');
                    const planTier = $(this).data('tier');

                    $('#selectedPlanId').val(planId);
                    $('#planError').hide();

                    // Show selected plan summary
                    $('#displayPlanName').text(planName);
                    $('#displayPlanTier').text(planTier);
                    $('#selectedPlanSummary').slideDown();
                });

                // Auto-fill sell price when price is entered (optional)
                $('#price').on('change', function() {
                    const sellPriceInput = $('#sell_price');
                    if (!sellPriceInput.val()) {
                        sellPriceInput.val($(this).val());
                    }
                });

                // Form validation
                $('#wageForm').on('submit', function(e) {
                    if (!$('#selectedPlanId').val()) {
                        e.preventDefault();
                        $('#planError').show();
                        $('html, body').animate({
                            scrollTop: $('.plans-wrapper').offset().top - 100
                        }, 500);
                        return false;
                    }

                    // Validate prices
                    const price = parseFloat($('#price').val());
                    const sellPrice = parseFloat($('#sell_price').val());

                    if (!price || price < 0) {
                        e.preventDefault();
                        alert('Please enter a valid regular price');
                        $('#price').focus();
                        return false;
                    }

                    if (!sellPrice || sellPrice < 0) {
                        e.preventDefault();
                        alert('Please enter a valid selling price');
                        $('#sell_price').focus();
                        return false;
                    }

                    if (sellPrice > price) {
                        if (!confirm('Selling price is higher than regular price. Do you want to continue?')) {
                            e.preventDefault();
                            $('#sell_price').focus();
                            return false;
                        }
                    }

                    // Additional validation for dates
                    const startDate = new Date($('#start_date').val());
                    const endDate = new Date($('#end_date').val());

                    if (endDate && startDate >= endDate) {
                        e.preventDefault();
                        alert('End date must be after start date');
                        $('#end_date').focus();
                        return false;
                    }
                });

                // Set minimum date for start_date to today
                const today = new Date().toISOString().split('T')[0];
                $('#start_date').attr('min', today);

                // Update end_date minimum based on start_date
                $('#start_date').on('change', function() {
                    const startDate = $(this).val();
                    $('#end_date').attr('min', startDate);

                    // Clear end_date if it's before new start_date
                    const endDateVal = $('#end_date').val();
                    if (endDateVal && endDateVal < startDate) {
                        $('#end_date').val('');
                    }
                });
            });
        </script>
    @endpush
