<?php

namespace Modules\UserRolePermission\app\Repositories;

use Modules\UserRolePermission\app\Models\Kid;
use App\Traits\Upload;
use App\Models\User;

class KidRepository
{
    use Upload;

    protected $model;

    public function __construct(Kid $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->orderBy('id', 'DESC')->get();
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        if(isset($data['photo']) && $data['photo'])
        {
            $data['photo'] = $this->uploadFile($data['photo'], 'kid/'.$data['user_id']);
        }

        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $kid = $this->getById($id);

        if(isset($data['photo']) && $data['photo'])
        {
            $data['photo'] = $this->uploadFile($data['photo'], 'kid/'.$data['user_id']);
             if($kid->photo) $this->deleteFile($kid->photo);
        }

        $kid->update($data);

        return $kid;
    }

    public function delete($id)
    {
        $kid = $this->getById($id);
        $kid->delete();
    }

    public function restore($id)
    {
        $kid = $this->model->withTrashed()->findOrFail($id);
        $kid->restore();
    }

    public function forceDelete($id)
    {
        $kid = $this->model->withTrashed()->findOrFail($id);
        $kid->forceDelete();
    }

    public function getByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->orderBy('id', 'DESC')->get();
    }

    public function getTrashed()
    {
        return $this->model->onlyTrashed()->orderBy('id', 'DESC')->get();
    }

    public function getData($request, $parentId = null)
    {
        $query = $this->model->orderBy('id', 'DESC');

        // Check if trashed is requested
        if ($request->filled('trashed') && $request->trashed == 'true') {
            $query = $query->onlyTrashed();
        }

        if (!empty($parentId) && $parentId !== 'null') {
            $query = $query->where('user_id', $parentId);
        }

        return $query->get();
    }

    public function getParents()
    {
        return User::whereHas('roles', function ($query) {
                $query->where('name', 'parent');
            })
            ->where('status', 1)
            ->orderByDesc('id')
            ->get(['id', 'first_name', 'last_name']);
    }
}
