@extends('backend.app')
@section('title', 'Pickup Type')
@section('css')
    <link href="{{ asset('backend/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Pickup Type', 'subTitle' => 'Pickup Type Management'])
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1 card-title">Pickup Type</div>
                            <div class="p-2">
                                <a href="#" class="btn btn-gradient-warning btn-sm" id="showTrashed">Trashed</a>
                            </div>

                            <div class="p-2">
                                <a href="#" class="btn btn-sm btn-gradient-success" id="addPickupBtn">Add Pickup Type</a>
                            </div>
                        </div>
                        <div class="table-responsive pt-3">
                            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Notice (minutes)</th>
                                        <th>Instant Notification</th>
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
   <div class="modal fade" id="pickupModal" tabindex="-1" aria-labelledby="pickupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border border-primary">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="pickupModalLabel">Create / Edit Pickup Type</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="pickupForm">
                @csrf
                <input type="hidden" name="id" id="pickup_id">

                <div class="modal-body">
                    <!-- Name -->
                    <div class="form-group mb-3">
                        <label for="name">Pickup Type Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <!-- Amount -->
                    <div class="form-group mb-3">
                        <label for="amount">Amount (aud)</label>
                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0" required>
                    </div>

                    <!-- Min Notice Minutes -->
                    <div class="form-group mb-3">
                        <label for="min_notice_minutes">Minimum Notice (minutes)</label>
                        <input type="number" name="min_notice_minutes" id="min_notice_minutes" class="form-control" min="0" required>
                    </div>

                    <!-- Requires Instant Notification -->
                    <div class="form-group mb-3">
                        <label for="requires_instant_notification">Requires Instant Notification?</label>
                        <select name="requires_instant_notification" id="requires_instant_notification" class="form-control" required>
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="form-group mb-3">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
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
                    moduleName: 'pickupType',
                    tableId: 'datatable-responsive',
                    modalId: 'pickupModal',
                    formId: 'pickupForm',
                    createBtnId: 'addPickupBtn',
                    trashedBtnId: 'showTrashed',
                    baseUrl: '/pickuptypes',
                    fields: ['name', 'amount', 'min_notice_minutes', 'requires_instant_notification', 'status']
                });
            });
        </script>
    @endpush
@endsection
