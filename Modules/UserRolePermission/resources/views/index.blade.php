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
    @include('userrolepermission::components.user_show_modal')

    @push('scripts')
        <script src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('backend/js/responsive.bootstrap.js') }}"></script>

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
                    userShowModal: 'userShowModal',
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

            // $(document).ready(function() {
            //     // If you're editing an existing record, set these values via data attributes or JS variables
            //     let selectedCountry_id = $('#country_id').data('selected'); // example: data-selected="1"
            //     let selectedState_id = $('#state_id').data('selected'); // same here
            //     let selectedCity_id = $('#city_id').data('selected'); // and here


            //     function loadStates(country_id, callback = null) {
            //         $('#state_id').html('<option value="">Loading...</option>');
            //         $('#city_id').html('<option value="">Select City</option>');

            //         $.ajax({
            //             url: `/users/states/by-country/${country_id}`,
            //             type: 'GET',
            //             success: function(states) {
            //                 let options = '<option value="">Select State</option>';
            //                 states.forEach(state => {
            //                     options += `<option value="${state.id}">${state.name}</option>`;
            //                 });
            //                 $('#state_id').html(options);

            //                 if (callback) callback();
            //             }
            //         });
            //     }

            //     function loadCities(state_d, callback = null) {
            //         $('#city_id').html('<option value="">Loading...</option>');

            //         $.ajax({
            //             url: `/users/cities/by-state/${state_d}`,
            //             type: 'GET',
            //             success: function(cities) {
            //                 let options = '<option value="">Select City</option>';
            //                 cities.forEach(city => {
            //                     options += `<option value="${city.id}">${city.name}</option>`;
            //                 });
            //                 $('#city_id').html(options);

            //                 if (callback) callback();
            //             }
            //         });
            //     }

            //     // Handle change
            //     $('#country_id').on('change', function() {
            //         const countryId = $(this).val();
            //         if (countryId) {
            //             loadStates(countryId);
            //         } else {
            //             $('#state_id').html('<option value="">Select State</option>');
            //             $('#city_id').html('<option value="">Select City</option>');
            //         }
            //     });

            //     $('#state_id').on('change', function() {
            //         const state_d = $(this).val();
            //         if (state_d) {
            //             loadCities(state_d);
            //         } else {
            //             $('#city_id').html('<option value="">Select City</option>');
            //         }
            //     });

            //     // Auto-select for edit mode
            //     if (selectedCountry_id) {
            //         $('#country_id').val(selectedCountry_id);
            //         loadStates(selectedCountry_id, function() {
            //             if (selectedState_id) {
            //                 $('#state_id').val(selectedState_id);
            //                 loadCities(selectedState_id, function() {
            //                     if (selectedCity_id) {
            //                         $('#city_id').val(selectedCity_id);
            //                     }
            //                 });
            //             }
            //         });
            //     }
            // });


            $(document).ready(function() {
    // Define functions globally so module-crud.js can access
    window.loadStates = function(country_id, callback = null) {
      $('#state_id').html('<option value="">Loading...</option>');
      $('#city_id').html('<option value="">Select City</option>');

      $.ajax({
        url: `/users/states/by-country/${country_id}`,
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
    };

    window.loadCities = function(state_d, callback = null) {
      $('#city_id').html('<option value="">Loading...</option>');

      $.ajax({
        url: `/users/cities/by-state/${state_d}`,
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
    };

    // You can also expose selected IDs globally if needed
    window.selectedCountry_id = $('#country_id').data('selected');
    window.selectedState_id = $('#state_id').data('selected');
    window.selectedCity_id = $('#city_id').data('selected');

    // Attach event handlers (they can remain here)
    $('#country_id').on('change', function() {
      const countryId = $(this).val();
      if (countryId) {
        window.loadStates(countryId);
      } else {
        $('#state_id').html('<option value="">Select State</option>');
        $('#city_id').html('<option value="">Select City</option>');
      }
    });

    $('#state_id').on('change', function() {
      const state_d = $(this).val();
      if (state_d) {
        window.loadCities(state_d);
      } else {
        $('#city_id').html('<option value="">Select City</option>');
      }
    });

    // Auto-select for edit mode
    if (window.selectedCountry_id) {
      $('#country_id').val(window.selectedCountry_id);
      window.loadStates(window.selectedCountry_id, function() {
        if (window.selectedState_id) {
          $('#state_id').val(window.selectedState_id);
          window.loadCities(window.selectedState_id, function() {
            if (window.selectedCity_id) {
              $('#city_id').val(window.selectedCity_id);
            }
          });
        }
      });
    }
  });
        </script>
        <script src="{{ asset('backend/js/module-crud.js') }}"></script>
    @endpush
@endsection
