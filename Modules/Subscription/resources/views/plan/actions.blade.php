@if ($plan->trashed())
    <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $plan->id }}" title="Restore">
        <i class="fas fa-undo"></i>
    </button>
    <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $plan->id }}" title="Delete Permanently">
        <i class="fas fa-trash-alt"></i>
    </button>
@else
    <div class="btn-group" role="group">
        <button class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $plan->id }}" title="Edit">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-gradient-info btn-sm showBtn" data-id="{{ $plan->id }}" title="View Details">
            <i class="fas fa-eye"></i>
        </button>
        <button class="btn btn-gradient-warning btn-sm duplicateBtn" data-id="{{ $plan->id }}" title="Duplicate">
            <i class="fas fa-copy"></i>
        </button>
        <button class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $plan->id }}" title="Move to Trash">
            <i class="fas fa-trash"></i>
        </button>
    </div>
@endif
