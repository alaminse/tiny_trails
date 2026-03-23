@extends('backend.app')
@section('title', 'Unassigned Subscriptions')

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Unassigned Subscriptions'])

    @can('unassign-subscription')
        <div class="app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline mb-4">
                            <div class="d-flex">
                                <div class="p-2 flex-grow-1 card-title">Unassigned Subscription List</div>
                                @can('delete-subscription')
                                    <div class="p-2">
                                        <a href="#" class="btn btn-gradient-warning btn-sm" id="showTrashed">Trashed</a>
                                    </div>
                                @endcan
                            </div>

                            <div class="table-responsive pt-3">
                                <table id="subscriptionTable" class="table table-striped table-bordered dt-responsive nowrap"
                                    cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>User</th>
                                            <th>Kid</th>
                                            <th>Plan</th>
                                            <th>Pickup</th>
                                            <th>Dropoff</th>
                                            <th>Status</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- AJAX load --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endcan
    @push('scripts')
        <script src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('backend/js/responsive.bootstrap.js') }}"></script>

        <script>
            $(document).ready(function() {
                let tableId = 'subscriptionTable';
                let $table = $(`#${tableId}`);
                let finalUrl = "{{ route('admin.ride.assign.get.subscriptions') }}";

                $.ajax({
                    url: finalUrl,
                    method: "GET",
                    success: function(response) {
                        console.log(response);

                        if ($.fn.DataTable.isDataTable(`#${tableId}`)) {
                            $table.DataTable().destroy();
                        }
                        $table.find("tbody").html(response.html);
                        $table.DataTable({
                            responsive: true
                        });
                    },
                    error: function(xhr) {
                        console.error(`Error fetching subscriptions data`, xhr);
                    }
                });
            });
        </script>
    @endpush
@endsection
