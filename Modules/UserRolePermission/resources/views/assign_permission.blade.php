@extends('backend.app')
@section('title', 'Assign Permissions — ' . ucfirst($role->name))

@section('css')
<style>
    /* ── Layout ──────────────────────────────────────────────────────── */
    .perm-wrapper {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    /* ── Module Card ─────────────────────────────────────────────────── */
    .perm-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        transition: box-shadow .2s;
    }
    .perm-card:hover {
        box-shadow: 0 3px 10px rgba(0,0,0,.10);
    }

    /* ── Card Header ─────────────────────────────────────────────────── */
    .perm-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        cursor: pointer;
        user-select: none;
    }
    .perm-card-header:hover {
        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    }

    .perm-module-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        font-size: .92rem;
        color: #343a40;
        text-transform: capitalize;
    }
    .perm-module-title .mod-icon {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        color: #fff;
    }
    .perm-badge {
        font-size: .72rem;
        padding: 2px 8px;
        border-radius: 20px;
        background: #6c757d;
        color: #fff;
        font-weight: 500;
    }
    .perm-badge.all-selected {
        background: #28a745;
    }
    .perm-badge.partial {
        background: #ffc107;
        color: #343a40;
    }

    .perm-header-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* ── Select-All toggle ───────────────────────────────────────────── */
    .select-module-toggle {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .82rem;
        font-weight: 500;
        color: #495057;
        cursor: pointer;
        white-space: nowrap;
    }
    .select-module-toggle input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        accent-color: #4e73df;
    }

    /* ── Collapse caret ──────────────────────────────────────────────── */
    .collapse-caret {
        color: #6c757d;
        font-size: .8rem;
        transition: transform .25s;
    }
    .collapsed .collapse-caret {
        transform: rotate(-90deg);
    }

    /* ── Card Body / Permission Grid ─────────────────────────────────── */
    .perm-card-body {
        padding: 14px 16px;
        background: #fff;
    }
    .perm-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 8px 12px;
    }

    /* ── Individual Permission Checkbox ──────────────────────────────── */
    .perm-item {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #e9ecef;
        background: #f8f9fa;
        transition: background .15s, border-color .15s;
        cursor: pointer;
        font-size: .83rem;
        font-weight: 500;
        color: #495057;
    }
    .perm-item:hover {
        background: #e8f0fe;
        border-color: #4e73df;
        color: #2c4abf;
    }
    .perm-item input[type="checkbox"] {
        width: 15px;
        height: 15px;
        cursor: pointer;
        accent-color: #4e73df;
        flex-shrink: 0;
    }
    .perm-item.is-checked {
        background: #e8f4fd;
        border-color: #4e73df;
        color: #2c4abf;
    }
    .perm-item .perm-action-icon {
        color: #adb5bd;
        font-size: .75rem;
    }
    .perm-item.is-checked .perm-action-icon {
        color: #4e73df;
    }

    /* ── Action Bar ──────────────────────────────────────────────────── */
    .action-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
        background: #f8f9fa;
        border-radius: 10px;
        border: 1px solid #dee2e6;
        flex-wrap: wrap;
    }
    .action-bar .divider {
        width: 1px;
        height: 28px;
        background: #dee2e6;
        margin: 0 4px;
    }
    .perm-stats {
        margin-left: auto;
        font-size: .82rem;
        color: #6c757d;
        font-weight: 500;
    }
    .perm-stats span {
        font-weight: 700;
        color: #4e73df;
    }

    /* ── Search ──────────────────────────────────────────────────────── */
    .perm-search-wrap {
        position: relative;
        max-width: 300px;
    }
    .perm-search-wrap input {
        padding-left: 34px;
        border-radius: 20px;
        font-size: .83rem;
        height: 34px;
    }
    .perm-search-wrap .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #adb5bd;
        font-size: .8rem;
    }

    /* ── Module color palette (cycles) ───────────────────────────────── */
    .mod-color-0  { background: #4e73df; }
    .mod-color-1  { background: #1cc88a; }
    .mod-color-2  { background: #36b9cc; }
    .mod-color-3  { background: #f6c23e; }
    .mod-color-4  { background: #e74a3b; }
    .mod-color-5  { background: #858796; }
    .mod-color-6  { background: #5a5c69; }
    .mod-color-7  { background: #20c997; }
    .mod-color-8  { background: #fd7e14; }
    .mod-color-9  { background: #6f42c1; }
    .mod-color-10 { background: #17a2b8; }
    .mod-color-11 { background: #e83e8c; }
    .mod-color-12 { background: #28a745; }
    .mod-color-13 { background: #dc3545; }
    .mod-color-14 { background: #6c757d; }

    /* ── Bottom save bar ─────────────────────────────────────────────── */
    .save-bar {
        position: sticky;
        bottom: 0;
        background: #fff;
        border-top: 2px solid #dee2e6;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 100;
        box-shadow: 0 -3px 10px rgba(0,0,0,.08);
        border-radius: 0 0 8px 8px;
    }

    /* ── Role pill ───────────────────────────────────────────────────── */
    .role-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: .82rem;
        font-weight: 600;
    }
    .role-super-admin { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
    .role-boh-it      { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    .role-boh-marketing { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .role-boh-sales   { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .role-boh-support { background: #e2d9f3; color: #4e2e8c; border: 1px solid #c8b8e8; }
    .role-default     { background: #e9ecef; color: #495057; border: 1px solid #dee2e6; }

    @media (max-width: 576px) {
        .perm-grid { grid-template-columns: 1fr 1fr; }
        .perm-stats { display: none; }
    }
</style>
@endsection


@php
    /**
     * ────────────────────────────────────────────────────────────────
     * Module config: human label + icon for each module key
     * Key = the segment AFTER the last '-' in permission name
     * ────────────────────────────────────────────────────────────────
     */
    $moduleConfig = [
        'dashboard'       => ['label' => 'Dashboard',            'icon' => 'fas fa-tachometer-alt'],
        'users'           => ['label' => 'User Management',      'icon' => 'fas fa-users'],
        'roles'           => ['label' => 'Roles',                'icon' => 'fas fa-user-tag'],
        'permissions'     => ['label' => 'Permissions',          'icon' => 'fas fa-key'],
        'kids'            => ['label' => 'Kids Management',      'icon' => 'fas fa-child'],
        'drivers'         => ['label' => 'Driver Management',    'icon' => 'fas fa-id-badge'],
        'parents'         => ['label' => 'Parent Management',    'icon' => 'fas fa-user-friends'],
        'pickup'          => ['label' => 'PickUp Type',          'icon' => 'fas fa-map-marker-alt'],
        'country'         => ['label' => 'Country',              'icon' => 'fas fa-flag'],
        'state'           => ['label' => 'State',                'icon' => 'fas fa-map'],
        'city'            => ['label' => 'City',                 'icon' => 'fas fa-city'],
        'plan'            => ['label' => 'Plan Lists',           'icon' => 'fas fa-list-alt'],
        'subscription'    => ['label' => 'Subscription',         'icon' => 'fas fa-file-contract'],
        'rideassign'      => ['label' => 'Ride Assignment',      'icon' => 'fas fa-route'],
        'rides'           => ['label' => 'Rides',                'icon' => 'fas fa-car'],
        'shifts'          => ['label' => 'Driver Shifts',        'icon' => 'fas fa-calendar-alt'],
        'broadcast'       => ['label' => 'Shift Broadcasts',     'icon' => 'fas fa-broadcast-tower'],
        'timesheets'      => ['label' => 'Timesheets',           'icon' => 'fas fa-clock'],
        'attendance'      => ['label' => 'Attendance',           'icon' => 'fas fa-user-check'],
        'wages'           => ['label' => 'Driver Wages',         'icon' => 'fas fa-dollar-sign'],
        'types'           => ['label' => 'Vehicle Types',        'icon' => 'fas fa-bus'],
        'verification'    => ['label' => 'Face Verification',    'icon' => 'fas fa-camera'],
        'trash'           => ['label' => 'Trash',                'icon' => 'fas fa-trash-alt'],
        'other'           => ['label' => 'Other',                'icon' => 'fas fa-puzzle-piece'],
    ];

    /**
     * Group by the LAST segment after '-' so:
     *   'list-driver-wages'  → key = 'wages'
     *   'create-shift-broadcast' → key = 'broadcast'
     *   'view-boh-dashboard' → key = 'dashboard'
     */
    $groupedPermissions = $permissions->groupBy(function ($permission) {
        $parts = explode('-', $permission->name);
        return end($parts) ?? 'other';
    })->sortKeys();

    $rolePermissionNames = $role->permissions->pluck('name')->toArray();

    /**
     * Role pill CSS class helper
     */
    $roleLower = strtolower(str_replace(' ', '-', $role->name));
    $rolePillClass = match(true) {
        str_contains($roleLower, 'super-admin')   => 'role-super-admin',
        str_contains($roleLower, 'boh-it')        => 'role-boh-it',
        str_contains($roleLower, 'boh-marketing') => 'role-boh-marketing',
        str_contains($roleLower, 'boh-sales')     => 'role-boh-sales',
        str_contains($roleLower, 'boh-support')   => 'role-boh-support',
        default                                   => 'role-default',
    };

    /**
     * Action label: prettify the FIRST segment of permission name
     * e.g. 'create-driver-wages' → 'Create'
     *      'force-delete-trash'  → 'Force Delete'
     *      'unassign-subscription' → 'Unassign'
     */
    $actionLabel = function (string $permName): string {
        $parts   = explode('-', $permName);
        $modKey  = end($parts);
        // Remove the last segment; what remains is the action
        array_pop($parts);
        return ucwords(implode(' ', $parts));
    };

    $actionIcon = function (string $permName): string {
        return match(true) {
            str_starts_with($permName, 'list')          => 'fas fa-list',
            str_starts_with($permName, 'view')          => 'fas fa-eye',
            str_starts_with($permName, 'create')        => 'fas fa-plus',
            str_starts_with($permName, 'edit')          => 'fas fa-pen',
            str_starts_with($permName, 'delete')        => 'fas fa-trash',
            str_starts_with($permName, 'force-delete')  => 'fas fa-skull',
            str_starts_with($permName, 'restore')       => 'fas fa-undo',
            str_starts_with($permName, 'approve')       => 'fas fa-check',
            str_starts_with($permName, 'reject')        => 'fas fa-times',
            str_starts_with($permName, 'assign')        => 'fas fa-link',
            str_starts_with($permName, 'unassign')      => 'fas fa-unlink',
            str_starts_with($permName, 'cancel')        => 'fas fa-ban',
            default                                     => 'fas fa-circle',
        };
    };
@endphp


@section('content')
@include('backend.includes.header', [
    'mainTitle' => 'Assign Permissions — ' . ucfirst($role->name)
])

<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline mb-4">

                    {{-- ── Card Header ──────────────────────────────── --}}
                    <div class="card-header d-flex align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2 me-auto">
                            <h3 class="card-title mb-0">Assign Permissions</h3>
                            <span class="role-pill {{ $rolePillClass }}">
                                <i class="fas fa-user-shield"></i>
                                {{ ucfirst($role->name) }}
                            </span>
                        </div>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-gradient-info btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Roles
                        </a>
                    </div>

                    {{-- ── Card Body ────────────────────────────────── --}}
                    <div class="card-body">
                        @include('backend.includes.error')

                        <form action="{{ route('admin.permissions.assign', $role->id) }}"
                              method="POST"
                              id="permissionsForm">
                            @csrf

                            {{-- ── Action Bar ───────────────────────── --}}
                            <div class="action-bar mb-3">
                                <button type="button" class="btn btn-gradient-primary btn-sm" id="selectAll">
                                    <i class="fas fa-check-double"></i> Select All
                                </button>
                                <button type="button" class="btn btn-gradient-warning btn-sm" id="deselectAll">
                                    <i class="fas fa-times-circle"></i> Deselect All
                                </button>
                                <div class="divider d-none d-sm-block"></div>
                                {{-- Search --}}
                                <div class="perm-search-wrap">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text"
                                           id="permSearch"
                                           class="form-control form-control-sm"
                                           placeholder="Search permissions…">
                                </div>
                                <div class="perm-stats ms-auto">
                                    Selected: <span id="selectedCount">0</span> / <span id="totalCount">0</span>
                                </div>
                            </div>

                            {{-- ── Module Cards ─────────────────────── --}}
                            <div class="perm-wrapper" id="permWrapper">

                                @foreach($groupedPermissions as $moduleKey => $modulePermissions)
                                @php
                                    $colorIdx  = $loop->index % 15;
                                    $cfg       = $moduleConfig[$moduleKey] ?? $moduleConfig['other'];
                                    $modLabel  = $cfg['label'];
                                    $modIcon   = $cfg['icon'];

                                    // How many are already checked?
                                    $checkedCount = $modulePermissions->filter(fn($p) =>
                                        in_array($p->name, $rolePermissionNames)
                                    )->count();
                                    $totalMod = $modulePermissions->count();

                                    $badgeClass = match(true) {
                                        $checkedCount === $totalMod && $totalMod > 0 => 'all-selected',
                                        $checkedCount > 0                           => 'partial',
                                        default                                     => '',
                                    };
                                @endphp

                                <div class="perm-card" data-module="{{ $moduleKey }}">
                                    {{-- Header (click to collapse) --}}
                                    <div class="perm-card-header"
                                         data-bs-toggle="collapse"
                                         data-bs-target="#module_{{ $moduleKey }}"
                                         aria-expanded="true">

                                        <div class="perm-module-title">
                                            <span class="mod-icon mod-color-{{ $colorIdx }}">
                                                <i class="{{ $modIcon }}"></i>
                                            </span>
                                            {{ $modLabel }}
                                            <span class="perm-badge {{ $badgeClass }}" id="badge_{{ $moduleKey }}">
                                                {{ $checkedCount }}/{{ $totalMod }}
                                            </span>
                                        </div>

                                        <div class="perm-header-right">
                                            <label class="select-module-toggle" onclick="event.stopPropagation()">
                                                <input type="checkbox"
                                                       class="select-module"
                                                       id="selectAll_{{ $moduleKey }}"
                                                       data-module="{{ $moduleKey }}"
                                                       {{ $checkedCount === $totalMod && $totalMod > 0 ? 'checked' : '' }}>
                                                Select All
                                            </label>
                                            <i class="fas fa-chevron-down collapse-caret" id="caret_{{ $moduleKey }}"></i>
                                        </div>
                                    </div>

                                    {{-- Collapsible Body --}}
                                    <div class="collapse show" id="module_{{ $moduleKey }}">
                                        <div class="perm-card-body">
                                            <div class="perm-grid">
                                                @foreach($modulePermissions as $permission)
                                                @php
                                                    $isChecked = in_array($permission->name, $rolePermissionNames);
                                                    $label     = $actionLabel($permission->name);
                                                    $icon      = $actionIcon($permission->name);
                                                @endphp
                                                <label class="perm-item {{ $isChecked ? 'is-checked' : '' }}"
                                                       for="perm_{{ $permission->id }}"
                                                       data-module="{{ $moduleKey }}"
                                                       data-name="{{ strtolower($permission->name) }}">
                                                    <input type="checkbox"
                                                           class="permission-checkbox module-{{ $moduleKey }}"
                                                           name="permissions[]"
                                                           value="{{ $permission->id }}"
                                                           id="perm_{{ $permission->id }}"
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                    <i class="{{ $icon }} perm-action-icon"></i>
                                                    {{ $label }}
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>{{-- /.perm-card --}}
                                @endforeach

                            </div>{{-- /#permWrapper --}}

                            {{-- ── Sticky Save Bar ──────────────────── --}}
                            <div class="save-bar">
                                <button type="submit" class="btn btn-gradient-primary btn-sm">
                                    <i class="fas fa-save"></i> Save Permissions
                                </button>
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-gradient-info btn-sm">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <span class="perm-stats ms-auto d-none d-sm-block">
                                    Saving <span id="selectedCountBottom">0</span> permission(s) for
                                    <strong>{{ ucfirst($role->name) }}</strong>
                                </span>
                            </div>

                        </form>
                    </div>{{-- /.card-body --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
$(document).ready(function () {

    // ── Helpers ────────────────────────────────────────────────────
    function updateStats() {
        const total    = $('.permission-checkbox').length;
        const selected = $('.permission-checkbox:checked').length;
        $('#selectedCount, #selectedCountBottom').text(selected);
        $('#totalCount').text(total);
    }

    function updateModuleBadge(module) {
        const all     = $('.module-' + module);
        const checked = $('.module-' + module + ':checked');
        const total   = all.length;
        const count   = checked.length;

        const $badge = $('#badge_' + module);
        $badge.text(count + '/' + total);
        $badge.removeClass('all-selected partial');
        if (count === total && total > 0) $badge.addClass('all-selected');
        else if (count > 0)              $badge.addClass('partial');

        $('#selectAll_' + module).prop('checked', count === total && total > 0);
    }

    function updateCheckedStyle($checkbox) {
        const $label = $checkbox.closest('.perm-item');
        $label.toggleClass('is-checked', $checkbox.is(':checked'));
    }

    // ── Initial stats ──────────────────────────────────────────────
    updateStats();

    // ── Select All ─────────────────────────────────────────────────
    $('#selectAll').on('click', function () {
        $('.permission-checkbox:visible').prop('checked', true);
        $('.permission-checkbox:visible').each(function () { updateCheckedStyle($(this)); });
        $('.select-module').prop('checked', true);
        // Update all badges
        $('.select-module').each(function () { updateModuleBadge($(this).data('module')); });
        updateStats();
    });

    // ── Deselect All ───────────────────────────────────────────────
    $('#deselectAll').on('click', function () {
        $('.permission-checkbox:visible').prop('checked', false);
        $('.permission-checkbox:visible').each(function () { updateCheckedStyle($(this)); });
        $('.select-module').prop('checked', false);
        $('.select-module').each(function () { updateModuleBadge($(this).data('module')); });
        updateStats();
    });

    // ── Module Select-All toggle ───────────────────────────────────
    $('.select-module').on('change', function () {
        const module    = $(this).data('module');
        const isChecked = $(this).is(':checked');
        $('.module-' + module).prop('checked', isChecked)
            .each(function () { updateCheckedStyle($(this)); });
        updateModuleBadge(module);
        updateStats();
    });

    // ── Individual checkbox change ─────────────────────────────────
    $(document).on('change', '.permission-checkbox', function () {
        updateCheckedStyle($(this));
        // Extract module from class list
        const classList = $(this).attr('class') || '';
        const match     = classList.match(/module-(\S+)/);
        if (match) updateModuleBadge(match[1]);
        updateStats();
    });

    // ── Collapse caret ─────────────────────────────────────────────
    $('.perm-card-header').on('click', function () {
        const moduleKey = $(this).closest('.perm-card').data('module');
        const $caret    = $('#caret_' + moduleKey);
        const $target   = $($(this).data('bs-target'));
        // toggled AFTER Bootstrap fires; check aria-expanded
        setTimeout(() => {
            const expanded = $target.hasClass('show');
            $caret.toggleClass('fa-chevron-down', expanded)
                  .toggleClass('fa-chevron-up', !expanded);
        }, 10);
    });

    // ── Live Search ────────────────────────────────────────────────
    $('#permSearch').on('input', function () {
        const query = $(this).val().toLowerCase().trim();

        if (!query) {
            // Show everything
            $('.perm-item').show();
            $('.perm-card').show();
            return;
        }

        $('.perm-card').each(function () {
            let cardHasMatch = false;
            $(this).find('.perm-item').each(function () {
                const name = $(this).data('name') || '';
                const text = $(this).text().toLowerCase();
                const match = name.includes(query) || text.includes(query);
                $(this).toggle(match);
                if (match) cardHasMatch = true;
            });
            $(this).toggle(cardHasMatch);

            // Auto-expand if matches found
            if (cardHasMatch) {
                const $collapse = $(this).find('.collapse');
                if (!$collapse.hasClass('show')) $collapse.addClass('show');
            }
        });
    });

});
</script>
@endpush
