@extends('backend.app')
@section('title', 'User')
@section('css')
    <link href="{{ asset('backend/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/image-preview.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'User', 'subTitle' => 'User Management'])
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1 card-title">User</div>
                            <div class="p-2">
                                <a href="#" class="btn btn-gradient-warning btn-sm" id="showTrashed">Trashed</a>
                            </div>

                            <div class="p-2">
                                <a href="#" class="btn btn-sm btn-gradient-success" id="addUserBtn">Add User</a>
                            </div>
                        </div>
                        <div class="table-responsive pt-3">
                            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>First Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th width="15%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    @include('userrolepermission::components.user_modal')

    @push('scripts')
        <script src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('backend/js/responsive.bootstrap.js') }}"></script>

        <script src="{{ asset('backend/js/module-crud.js') }}"></script>
        <script src="{{ asset('backend/js/image-preview.js') }}"></script>
        <script>

            $(document).ready(function() {
                // $('.select2').select2();

                $('#role').on('change', function() {
                    if ($(this).val().toLowerCase() === 'driver') {
                        $('#driverFields').slideDown();
                    } else {
                        $('#driverFields').slideUp();
                    }
                });

                $('#role').trigger('change');

                initModuleCrud({
                    moduleName: 'user',
                    tableId: 'datatable-responsive',
                    modalId: 'userModal',
                    formId: 'userForm',
                    createBtnId: 'addUserBtn',
                    trashedBtnId: 'showTrashed',
                    baseUrl: '/users',
                    fields: [
                        'first_name',
                        'last_name',
                        'email',
                        'password',
                        'phone',
                        'dob',
                        'gender',
                        'height_cm',
                        'weight_kg',
                        'photo',
                        'address',
                        'country_id',
                        'state_id',
                        'city_id',
                        'status',
                        'role',
                        'driving_license_number',
                        'driving_license_expiry',
                        'driving_license_image',
                        'car_model',
                        'car_make',
                        'car_year',
                        'car_color',
                        'car_plate_number',
                        'car_image',
                    ],
                });
            });

$(document).ready(function () {
    // If you're editing an existing record, set these values via data attributes or JS variables
    const selectedCountryId = $('#country_id').data('selected'); // example: data-selected="1"
    const selectedStateId = $('#state_id').data('selected');     // same here
    const selectedCityId = $('#city_id').data('selected');       // and here


    function loadStates(countryId, callback = null) {
        $('#state_id').html('<option value="">Loading...</option>');
        $('#city_id').html('<option value="">Select City</option>');

        $.ajax({
            url: `/users/states/by-country/${countryId}`,
            type: 'GET',
            success: function(states) {
                let options = '<option value="">Select State</option>';
                states.forEach(state => {
                    options += `<option value="${state.id}">${state.name}</option>`;
                });
                $('#state_id').html(options);

                if (callback) callback();
            }
        });
    }



    function loadCities(stateId, callback = null) {
        $('#city_id').html('<option value="">Loading...</option>');

        $.ajax({
            url: `/users/cities/by-state/${stateId}`,
            type: 'GET',
            success: function(cities) {
                let options = '<option value="">Select City</option>';
                cities.forEach(city => {
                    options += `<option value="${city.id}">${city.name}</option>`;
                });
                $('#city_id').html(options);

                if (callback) callback();
            }
        });
    }

    // Handle change
    $('#country_id').on('change', function () {
        const countryId = $(this).val();
        if (countryId) {
            loadStates(countryId);
        } else {
            $('#state_id').html('<option value="">Select State</option>');
            $('#city_id').html('<option value="">Select City</option>');
        }
    });

    $('#state_id').on('change', function () {
        const stateId = $(this).val();
        if (stateId) {
            loadCities(stateId);
        } else {
            $('#city_id').html('<option value="">Select City</option>');
        }
    });

    // Auto-select for edit mode
    if (selectedCountryId) {
        $('#country_id').val(selectedCountryId);
        loadStates(selectedCountryId, function () {
            if (selectedStateId) {
                $('#state_id').val(selectedStateId);
                loadCities(selectedStateId, function () {
                    if (selectedCityId) {
                        $('#city_id').val(selectedCityId);
                    }
                });
            }
        });
    }
});
</script>

    @endpush
@endsection
