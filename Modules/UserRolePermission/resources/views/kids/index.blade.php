@extends('backend.app')
@section('title', 'Kid')
@section('css')
    <link href="{{ asset('backend/css/dataTables.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/responsive.bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/css/image-preview.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Kid', 'subTitle' => 'Kid Management'])
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="d-flex">
                            <div class="p-2 flex-grow-1 card-title">Kid</div>
                            <div class="p-2">
                                <a href="#" class="btn btn-gradient-warning btn-sm" id="showTrashed">Trashed</a>
                            </div>

                            <div class="p-2">
                                <a href="#" class="btn btn-sm btn-gradient-success" id="addKidBtn">Add Kid</a>
                            </div>
                        </div>
                        <div class="table-responsive pt-3">
                            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap"
                                cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Parent Name</th>
                                        <th>Name</th>
                                        <th>DOB</th>
                                        <th>Gender</th>
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
    @include('userrolepermission::kids.kid_modal')
    @include('userrolepermission::kids.kid_show_modal')

    @push('scripts')
        <script src="{{ asset('backend/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ asset('backend/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('backend/js/responsive.bootstrap.js') }}"></script>

        <script src="{{ asset('backend/js/module-crud.js') }}"></script>
        <script src="{{ asset('backend/js/image-preview.js') }}"></script>
        <script>
            // parentId
            window.parentId = @json($parentId ?? null);

            $(document).ready(function() {
                initModuleCrud({
                    moduleName: 'kid',
                    tableId: 'datatable-responsive',
                    modalId: 'kidModal',
                    kidShowModal: 'kidShowModal',
                    formId: 'kidForm',
                    createBtnId: 'addKidBtn',
                    trashedBtnId: 'showTrashed',
                    userShowModal: 'kidShowModal',
                    baseUrl: '/kids',
                    parentId: window.parentId,
                    fields: [
                        'id',
                        'user_id',
                        'first_name',
                        'last_name',
                        'dob',
                        'gender',
                        'height_cm',
                        'weight_kg',
                        'photo',
                        'school_name',
                        'school_address',
                        'emergency_contact',
                    ]
                });
            });
            $(document).on("click", `.editBtn`, function (e) {
                console.log('clicked');
                modalOpen();
            })
            $('#addKidBtn').on('click', function() {
                $('#kidModal').modal('show');

                modalOpen();
            });
            function modalOpen() {
                const $select = $('#user_id');
                $select.empty().append('<option value="">Loading...</option>');
                let finalUrl = `kids/parents`;

                $.ajax({
                    url: finalUrl,
                    type: 'GET',
                    success: function(response) {
                        let parents = response.parents;

                        if (Array.isArray(parents)) {
                            // $('#user_id').empty(); // Clear previous options

                            parents.forEach(parent => {
                                let selected = (parentId && parent.id == parentId) ? 'selected' :
                                '';
                                $('#user_id').append(
                                    `<option value="${parent.id}" ${selected}>${parent.first_name}</option>`
                                    );
                            });

                            if (parentId) {
                                // Disable the dropdown to prevent changes
                                $('#user_id').prop('disabled', true);

                                // Insert or update hidden input to submit the selected user_id
                                if ($('#hidden_user_id').length === 0) {
                                    $('#user_id').after(
                                        `<input type="hidden" id="hidden_user_id" name="user_id" value="${parentId}">`
                                        );
                                } else {
                                    $('#hidden_user_id').val(parentId);
                                }
                            } else {
                                // Enable select if no parentId restriction
                                $('#user_id').prop('disabled', false);
                                $('#hidden_user_id').remove(); // Remove hidden input if any
                            }
                        }
                    },
                    error: function() {
                        $select.empty().append('<option value="">Error loading parents</option>');
                    }
                });
            }
        </script>
    @endpush
@endsection
