@foreach ($data as $index => $user)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td>
        @foreach ($user->roles as $role)
            <span class="bg-purple-blue">{{ $role->name }}</span>
        @endforeach
    </td>

    <td>
        @if ($user->trashed())
            <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $user->id }}">
                <i class="fas fa-undo"></i>
            </button>
            <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $user->id }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        @else
            <a href="#" class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $user->id }}">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $user->id }}">
                <i class="fas fa-trash"></i>
            </a>
        @endif
    </td>
</tr>
@endforeach
