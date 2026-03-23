@foreach ($data as $index => $state)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $state->state_name }}</td>
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
                @can('restore-trash')
                    <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $state->id }}">
                        <i class="fas fa-undo"></i>
                    </button>
                @endcan
                @can('force-delete-trash')
                    <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $state->id }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                @endcan
            @else
                @can('edit-city')
                    <a href="#" class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $state->id }}">
                        <i class="fas fa-edit"></i>
                    </a>
                @endcan
                @can('delete-city')
                    <a href="#" class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $state->id }}">
                        <i class="fas fa-trash"></i>
                    </a>
                @endcan
            @endif
        </td>
    </tr>
@endforeach
