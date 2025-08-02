@foreach ($data as $index => $state)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $state->country_name  }}</td>
    <td>{{ $state->name }}</td>
    <td>
        @if ($state->status === 'active')
            <span class="badge btn-gradient-success">Active</span>
        @else
            <span class="badge btn-gradient-warning text-dark">Inactive</span>
        @endif
    </td>
    <td>
        @if ($state->trashed())
            <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $state->id }}">
                <i class="fas fa-undo"></i>
            </button>
            <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $state->id }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        @else
            <a href="#" class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $state->id }}">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $state->id }}">
                <i class="fas fa-trash"></i>
            </a>
        @endif
    </td>
</tr>
@endforeach

