@extends('backend.app')
@section('title', 'User')
@section('css')
    <link href="{{ asset('backend/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
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
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border border-primary">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="userModalLabel">Create User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form id="userForm">
                    @csrf
                    <input type="hidden" name="id" id="user_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name">User Name</label>
                            <input type="text" name="name" id="user_name" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="user_email" class="form-control" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="user_password" class="form-control">
                        </div>

                        <div class="form-group mb-3">
                            <label for="role">Role</label>
                            {{-- multiple="multiple" --}}
                            <select name="role" id="user_role" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" style="width: 100%;" required>
                                @foreach (\Spatie\Permission\Models\Role::all() as $role)
                                    <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex flex-row-reverse">
                            <button type="submit" class="btn btn-gradient-primary btn-sm p-2">Save</button>
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
            $(document).ready(function() {
                initModuleCrud({
                    moduleName: 'user',
                    tableId: 'datatable-responsive',
                    modalId: 'userModal',
                    formId: 'userForm',
                    createBtnId: 'addUserBtn',
                    trashedBtnId: 'showTrashed',
                    baseUrl: '/users',
                    fields: ['name', 'email', 'password', 'role']
                });
            });
        </script>
    @endpush
@endsection
