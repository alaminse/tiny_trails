@foreach ($data as $index => $country)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $country->name }}</td>
    <td>
        @if ($country->status === 'active')
            <span class="badge btn-gradient-success">Active</span>
        @else
            <span class="badge btn-gradient-warning text-dark">Inactive</span>
        @endif
    </td>
    <td>
        @if ($country->trashed())
            <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $country->id }}">
                <i class="fas fa-undo"></i>
            </button>
            <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $country->id }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        @else
            <a href="#" class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $country->id }}">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $country->id }}">
                <i class="fas fa-trash"></i>
            </a>
        @endif
    </td>
</tr>
@endforeach

