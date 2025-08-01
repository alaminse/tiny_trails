@extends('backend.app')
@section('title', 'Permissions')
@section('css')
    <link href="{{ asset('frontend/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Permissions', 'subTitle' => 'Permissions Management'])
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <div class="card-title">Quick Example</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')

    <script src="{{ asset('frontend/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('frontend/js/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('frontend/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('frontend/js/responsive.bootstrap.js') }}"></script>
        <script>
            $(document).ready(function() {

                function getData() {
                    $.ajax({
                        url: '/testbattaries/get/data',
                        type: 'GET',
                        success: function(response) {
                            console.log(response);
                            var userDataTable = $('#infoTable').DataTable();
                            if (response.tableRows && response.total > 0) {
                                if (userDataTable !== undefined && userDataTable !== null) {
                                    userDataTable.destroy();
                                }

                                $('#tablebody').html(response.tableRows);
                                $('.pagination').html(response.pagination);

                                userDataTable = $('#infoTable').DataTable({
                                    "paging": true,
                                    "lengthChange": false,
                                    "searching": true,
                                    "info": false,
                                    "autoWidth": true,
                                    "drawCallback": function(settings) {
                                        var api = this.api();
                                        var rows = api.rows({
                                            page: 'current'
                                        }).nodes();
                                        var startIndex = api.page() * api.page.len();
                                        var i = startIndex + 1;

                                        $(rows).each(function() {
                                            $(this).find('td:first').html(i++);
                                        });
                                    }
                                });
                            } else {
                                $('#tablebody').html('<tr>\
                                        <td align="center" colspan="6">No Data Found</td>\
                                    </tr>');
                                $('.pagination').html('');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                }
                getData();
            });
        </script>

    @endpush
@endsection
