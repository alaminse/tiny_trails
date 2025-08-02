@extends('backend.app')
@section('title', 'Permissions')
@section('css')
    <link href="{{ asset('backend/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Permissions', 'subTitle' => 'Permissions Management'])
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1 card-title">Permissions</div>
                            <div class="p-2">
                                <a href="#" class="btn btn-warning btn-sm" id="showTrashed">Trashed</a>
                            </div>

                            <div class="p-2">
                                <a href="#" class="btn btn-sm btn-success" id="addPermissionBtn">Add Permission</a>
                            </div>
                        </div>
                        <div class="table-responsive pt-3">
                            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Permission Name</th>
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
    <div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border border-primary">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="permissionModalLabel">Create Permission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form id="permissionForm">
                    @csrf
                    <input type="hidden" name="id" id="permission_id">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name">Permission Name</label>
                            <input type="text" name="name" id="permission_name" class="form-control" required>
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
                    moduleName: 'permission',
                    tableId: 'datatable-responsive',
                    modalId: 'permissionModal',
                    formId: 'permissionForm',
                    createBtnId: 'addPermissionBtn',
                    trashedBtnId: 'showTrashed',
                    baseUrl: '/permissions',
                    fields: ['name']
                });
            });
        </script>
    @endpush
@endsection
