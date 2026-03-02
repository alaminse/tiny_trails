@extends('backend.app')
@section('title', 'Assign Permissions - ' . ucfirst($role->name))
@section('css')
<style>
    .permission-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
    .permission-header {
        background-color: #f8f9fa;
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        border-radius: 8px 8px 0 0;
    }
    .permission-body {
        padding: 15px;
    }
    .module-name {
        font-weight: 600;
        color: #495057;
        text-transform: capitalize;
    }
    .custom-checkbox {
        margin-right: 15px;
    }
    .select-all-module {
        float: right;
    }
</style>
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Assign Permissions - ' . ucfirst($role->name)])
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Assign Permissions to {{ ucfirst($role->name) }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-gradient-info btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to Roles
                                </a>
                            </div>
                        </div>
                        <div class="card-body">

                            @include('backend.includes.error')
                            <form action="{{ route('admin.permissions.assign', $role->id) }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <button type="button" class="btn btn-gradient-primary btn-sm" id="selectAll">
                                        <i class="fas fa-check-double"></i> Select All
                                    </button>
                                    <button type="button" class="btn btn-gradient-warning btn-sm" id="deselectAll">
                                        <i class="fas fa-times"></i> Deselect All
                                    </button>
                                </div>

                                @php
                                    $groupedPermissions = $permissions->groupBy(function($permission) {
                                        return explode('-', $permission->name)[1] ?? 'other';
                                    });
                                    $rolePermissions = $role->permissions->pluck('name')->toArray();
                                @endphp

                                @foreach($groupedPermissions as $module => $modulePermissions)
                                <div class="permission-card">
                                    <div class="permission-header">
                                        <span class="module-name">
                                            <i class="fas fa-cube"></i> {{ ucfirst($module) }}
                                        </span>
                                        <div class="custom-control custom-checkbox select-all-module">
                                            <input type="checkbox" class="custom-control-input select-module"
                                                   id="select_{{ $module }}" data-module="{{ $module }}">
                                            <label class="custom-control-label" for="select_{{ $module }}">
                                                Select All
                                            </label>
                                        </div>
                                    </div>
                                    <div class="permission-body">
                                        <div class="row">
                                            @foreach($modulePermissions as $permission)
                                            <div class="col-md-3 col-sm-6 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox"
                                                           class="custom-control-input permission-checkbox module-{{ $module }}"
                                                           name="permissions[]"
                                                           value="{{ $permission->id }}"
                                                           id="permission_{{ $permission->id }}"
                                                           {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                                        {{ ucfirst(str_replace('-', ' ', explode('-', $permission->name)[0])) }}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-gradient-primary btn-sm">
                                        <i class="fas fa-save"></i> Save Permissions
                                    </button>
                                    <a href="{{ route('admin.roles.index') }}" class="btn btn-gradient-info btn-sm">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Select All Permissions
            $('#selectAll').click(function() {
                $('.permission-checkbox').prop('checked', true);
                $('.select-module').prop('checked', true);
            });

            // Deselect All Permissions
            $('#deselectAll').click(function() {
                $('.permission-checkbox').prop('checked', false);
                $('.select-module').prop('checked', false);
            });

            // Select All for specific module
            $('.select-module').change(function() {
                let module = $(this).data('module');
                let isChecked = $(this).is(':checked');
                $('.module-' + module).prop('checked', isChecked);
            });

            // Update module checkbox when individual permissions change
            $('.permission-checkbox').change(function() {
                let moduleClass = $(this).attr('class').match(/module-\S+/)[0];
                let module = moduleClass.replace('module-', '');
                let totalCheckboxes = $('.' + moduleClass).length;
                let checkedCheckboxes = $('.' + moduleClass + ':checked').length;

                $('#select_' + module).prop('checked', totalCheckboxes === checkedCheckboxes);
            });
        });
    </script>
    @endpush
@endsection
