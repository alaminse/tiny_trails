@foreach ($data as $index => $role)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $role->name }}</td>
    <td>
        @if ($role->trashed())

            <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $role->id }}">
                <i class="fas fa-undo"></i>
            </button>
            <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $role->id }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        @else
            @can('edit-plan')
            <a href="#" class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $role->id }}">
                <i class="fas fa-edit"></i>
            </a>
            @endcan
            @can('delete-plan')
            <a href="#" class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $role->id }}">
                <i class="fas fa-trash"></i>
            </a>
            @endcan
            @if (request()->routeIs('admin.roles.data'))
                <a href="{{ route('admin.permissions.assign', $role->id) }}" class="btn btn-gradient-info btn-sm">
                    <i class="fas fa-user-lock"></i>
                </a>
            @endif
        @endif
    </td>
</tr>
@endforeach
