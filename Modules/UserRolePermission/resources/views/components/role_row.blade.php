@foreach ($roles as $index => $role)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $role->name }}</td>
    <td>
        @if ($role->trashed())
            <button class="btn btn-info btn-sm restoreRoleBtn" data-id="{{ $role->id }}">
                <i class="fas fa-undo"></i>
            </button>
            <button class="btn btn-danger btn-sm forceDeleteRoleBtn" data-id="{{ $role->id }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        @else
            <a href="#" class="btn btn-primary btn-sm editRoleBtn" data-id="{{ $role->id }}">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="btn btn-danger btn-sm deleteRoleBtn" data-id="{{ $role->id }}">
                <i class="fas fa-trash"></i>
            </a>
        @endif
    </td>
</tr>
@endforeach
