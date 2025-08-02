@extends('backend.app')
@section('title', 'City')
@section('css')
    <link href="{{ asset('backend/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'City', 'subTitle' => 'City Management'])
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1 card-title">City</div>
                            <div class="p-2">
                                <a href="#" class="btn btn-gradient-warning btn-sm" id="showTrashed">Trashed</a>
                            </div>

                            <div class="p-2">
                                <a href="#" class="btn btn-sm btn-gradient-success" id="addCityBtn">Add City</a>
                            </div>
                        </div>
                        <div class="table-responsive pt-3">
                            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>State Name</th>
                                        <th>Name</th>
                                        <th>Status</th>
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
   <div class="modal fade" id="cityModal" tabindex="-1" aria-labelledby="cityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border border-primary">
            <div class="modal-header btn-gradient-primary">
                <h5 class="modal-title" id="cityModalLabel">Create / Edit City</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="cityForm">
                @csrf
                <input type="hidden" name="id" id="city_id">

                <div class="modal-body">
                    <!-- Name -->
                    <div class="form-group mb-3">
                        <label for="name">City Name</label>
                        <input type="text" name="name" id="city_name" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="state_id">Select State</label>
                        <select name="state_id" id="city_state_id" class="form-control select2 select2-danger" data-dropdown-css-class="select2-danger" style="width: 100%;" required>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}">{{ ucfirst($state->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Status -->
                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" id="city_status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
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
                    moduleName: 'city',
                    tableId: 'datatable-responsive',
                    modalId: 'cityModal',
                    formId: 'cityForm',
                    createBtnId: 'addCityBtn',
                    trashedBtnId: 'showTrashed',
                    baseUrl: '/cities',
                    fields: ['name', 'status']
                });
            });
        </script>
    @endpush
@endsection
