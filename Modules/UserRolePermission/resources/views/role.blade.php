@extends('backend.app')
@section('title', 'Roles')
@section('css')
    <link href="{{ asset('backend/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Roles', 'subTitle' => 'Roles Management'])
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1 card-title">Roles</div>
                            <div class="p-2">
                                <a href="#" class="btn btn-warning btn-sm" id="showTrashed">Trashed</a>
                            </div>

                            <div class="p-2">
                                <a href="#" class="btn btn-sm btn-success" id="addRoleBtn">Add Role</a>
                            </div>
                        </div>
                        <div class="table-responsive pt-3">
                            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Role Name</th>
                                        <th width="25%">Action</th>
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
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border border-primary">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="roleModalLabel">Create Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form id="roleForm">
                    @csrf
                    <input type="hidden" name="id" id="role_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name">Role Name</label>
                            <input type="text" name="name" id="role_name" class="form-control" required>
                        </div>
                        <div class="d-flex flex-row-reverse">
                            <button type="submit" class="btn btn-primary btn-sm p-2">Save</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>


    @push('scripts')
        <script src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('backend/js/responsive.bootstrap.js') }}"></script>

        <script src="{{ asset('backend/js/module-crud.js') }}"></script>
        <script>
            $(document).ready(function () {
                initModuleCrud({
                    moduleName: 'role',
                    tableId: 'datatable-responsive',
                    modalId: 'roleModal',
                    formId: 'roleForm',
                    createBtnId: 'addRoleBtn',
                    trashedBtnId: 'showTrashed',
                    baseUrl: '/roles',
                    fields: ['name']
                });
            });
        </script>
        {{-- <script>
            $(document).ready(function() {
                let currentView = 'active';
                // Load active data on initial load
                getData('roles/get/data');

                // Toggle button click
                $('#showTrashed').on('click', function(e) {
                    e.preventDefault();

                    if (currentView === 'active') {
                        getData('roles/get/data?trashed=true');
                        currentView = 'trashed';
                        $(this).text('Back to Active');
                    } else {
                        getData('roles/get/data');
                        currentView = 'active';
                        $(this).text('Trashed');
                    }
                });

                function getData(url) {
                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function(response) {
                            if ($.fn.DataTable.isDataTable('#datatable-responsive')) {
                                $('#datatable-responsive').DataTable().destroy();
                            }

                            // Replace tbody with new HTML
                            $('#datatable-responsive tbody').html(response.html);

                            // Re-initialize DataTable
                            $('#datatable-responsive').DataTable({
                                responsive: true,
                                paging: true,
                                searching: true,
                                ordering: true
                            });
                        },
                        error: function(xhr) {
                            console.error('Error fetching data:', xhr);
                        }
                    });
                }
                // Create Role
                $('#addRoleBtn').click(function(e) {
                    e.preventDefault();

                    $('#roleModalLabel').text('Create Role');
                    $('#roleForm')[0].reset();
                    $('#role_id').val('');
                    $('#roleModal').modal('show');
                });

                // Edit Role
                $(document).on('click', '.editRoleBtn', function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');

                    $.ajax({
                        url: 'roles/edit/' + id,
                        method: 'GET',
                        success: function(response) {
                            $('#roleModalLabel').text('Edit Role');
                            $('#role_id').val(response.id);
                            $('#role_name').val(response.name);
                            $('#roleModal').modal('show');
                        },
                        error: function() {
                            toastr.error('Failed to load role data.');
                        }
                    });
                });

                // Submit Role Form (Create or Update)
                $('#roleForm').submit(function(e) {
                    e.preventDefault();

                    let id = $('#role_id').val();
                    let url = id ? '/roles/update/' + id : '{{ route('admin.roles.store') }}';
                    let method = id ? 'PUT' : 'POST';

                    $.ajax({
                        url: url,
                        type: method,
                        data: $(this).serialize(),
                        success: function(response) {
                            $('#roleModal').modal('hide');
                            toastr.success(response.message);
                            // Reload DataTable or refresh list
                            getData('roles/get/data');
                        },
                        error: function(xhr) {
                            toastr.error('Error saving role.');
                        }
                    });
                });

                $(document).on('click', '.deleteRoleBtn', function(e) {
                    e.preventDefault();
                    const id = $(this).data('id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You can restore it later from trash.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/roles/delete/' + id,
                                type: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    getData('roles/get/data');
                                },
                                error: function() {
                                    Swal.fire('Error!', 'Failed to delete the role.',
                                        'error');
                                }
                            });
                        }
                    });
                });

                // Restore Role
                $(document).on('click', '.restoreRoleBtn', function() {
                    const id = $(this).data('id');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You want to restore.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Restore it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/roles/restore/' + id,
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Restored!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    getData('roles/get/data?trashed=true');
                                },
                                error: function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed',
                                        text: 'Failed to restore role.',
                                    });
                                }
                            });
                        }
                    });
                });

                // Force Delete Role
                $(document).on('click', '.forceDeleteRoleBtn', function() {
                    const id = $(this).data('id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This will permanently delete the role. This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/roles/force-delete/' + id,
                                method: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    getData('roles/get/data?trashed=true');
                                },
                                error: function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Failed to delete permanently.',
                                    });
                                }
                            });
                        }
                    });
                });


            });
        </script> --}}
    @endpush
@endsection
