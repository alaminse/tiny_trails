@foreach ($data as $index => $kid)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $kid->parent_name }}</td>
    <td>{{ $kid->first_name }} {{ $kid->last_name }}</td>
    <td>{{ $kid->dob }}</td>
    <td>{{ $kid->gender }}</td>
    <td>
        @if ($kid->trashed())
            <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $kid->id }}" title="Restore">
                <i class="fas fa-undo"></i>
            </button>
            <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $kid->id }}" title="Delete">
                <i class="fas fa-trash-alt"></i>
            </button>
        @else
            <a href="#" class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $kid->id }}" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="btn btn-gradient-primary btn-sm showBtn" data-id="{{ $kid->id }}" title="Show">
                <i class="fas fa-eye"></i>
            </a>
            <a href="#" class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $kid->id }}" title="Trash">
                <i class="fas fa-trash"></i>
            </a>
        @endif
    </td>
</tr>
@endforeach
