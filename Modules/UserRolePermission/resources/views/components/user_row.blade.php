@foreach ($data as $index => $user)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
    <td>{{ $user->email }}</td>
    <td>
        @foreach ($user->roles as $role)
            <span class="bg-purple-blue">{{ $role->name }}</span>
        @endforeach
    </td>
    <td>
        @if ($user->status === 'active')
            <span class="badge btn-gradient-success">Active</span>
        @else
            <span class="badge btn-gradient-warning text-dark">Inactive</span>
        @endif
    </td>
    <td>
        @if ($user->trashed())
            <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $user->id }}" title="Restore">
                <i class="fas fa-undo"></i>
            </button>
            <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $user->id }}" title="Delete">
                <i class="fas fa-trash-alt"></i>
            </button>
        @else
            <a href="#" class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $user->id }}" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="btn btn-gradient-primary btn-sm showBtn" data-id="{{ $user->id }}" title="Show">
                <i class="fas fa-eye"></i>
            </a>
            @if ($user->roles->contains('name', 'parent'))
                <a href="{{ route('admin.kids.index', ['parent' => $user->id]) }}" class="btn btn-gradient-success btn-sm" title="Kids">
                    <i class="fas fa-user-friends"></i>
                </a>
            @endif
            <a href="#" class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $user->id }}" title="Trash">
                <i class="fas fa-trash"></i>
            </a>
        @endif
    </td>
</tr>
@endforeach
