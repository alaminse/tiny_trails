<?php

namespace Modules\UserRolePermission\app\Repositories;

use App\Models\User;
use App\Traits\Upload;
use Modules\UserRolePermission\app\Models\Kid;

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
        if (isset($data['photo']) && $data['photo']) {
            $data['photo'] = $this->uploadFile($data['photo'], 'kid/'.$data['user_id']);
        }

        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $kid = $this->getById($id);

        if (isset($data['photo']) && $data['photo']) {
            $data['photo'] = $this->uploadFile($data['photo'], 'kid/'.$data['user_id']);
            if ($kid->photo) {
                $this->deleteFile($kid->photo);
            }
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

        if (! empty($parentId) && $parentId !== 'null') {
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

    public function ridePricing($id)
    {
        $kid = Kid::with([
            'pendingWage.plan.pickupType',
        ])->findOrFail($id);

        $pendingWage = $kid->pendingWage;

        return [
            'kid' => [
                'id' => $kid->id,
                'first_name' => $kid->first_name,
                'last_name' => $kid->last_name,
                'photo' => $kid->photo ? getImageUrl($kid->photo) : null,
            ],
            'pending_wage' => $pendingWage ? [
                'id' => $pendingWage->id,
                'plan_id' => $pendingWage->plan_id,
                'price' => (float) $pendingWage->price,
                'sell_price' => (float) $pendingWage->sell_price,
                'start_date' => $pendingWage->start_date,
                'end_date' => $pendingWage->end_date,
                'status' => $pendingWage->status,
                'notes' => $pendingWage->notes,
                'plan' => $pendingWage->plan ? [
                    'id' => $pendingWage->plan->id,
                    'name' => $pendingWage->plan->name,
                    'slug' => $pendingWage->plan->slug,
                    'description' => $pendingWage->plan->description,
                    'billing_period' => $this->formatBillingPeriod(
                        $pendingWage->plan->interval_count,
                        $pendingWage->plan->interval
                    ),
                    'interval' => $pendingWage->plan->interval,
                    'interval_count' => $pendingWage->plan->interval_count,
                    'features' => $pendingWage->plan->features
                                              ? (is_array($pendingWage->plan->features)
                            ? $pendingWage->plan->features
                            : json_decode($pendingWage->plan->features, true))
                                              : [],
                    'plan_tier' => $pendingWage->plan->plan_tier,
                    'includes_hardware' => (bool) $pendingWage->plan->includes_hardware,
                    'hardware_price' => $pendingWage->plan->includes_hardware
                                              ? (float) $pendingWage->plan->hardware_price
                                              : null,
                    'pickup_type' => $pendingWage->plan->pickupType ? [
                    'id' => $pendingWage->plan->pickupType->id,
                    'name' => $pendingWage->plan->pickupType->name,
                ] : null,
                ] : null,
            ] : null,
        ];
    }

    private function formatBillingPeriod($count, $interval): string
    {
        if (! $interval) {
            return '';
        }

        if ($interval === 'trip') {
            return $count == 1 ? 'per trip' : 'every '.$count.' trips';
        }

        if ($count == 1) {
            return 'per '.$interval;
        }

        $namedPeriods = [
            'month' => [3 => 'per quarter', 6 => 'per half year', 12 => 'per year'],
            'week' => [2 => 'every fortnight'],
        ];

        if (isset($namedPeriods[$interval][$count])) {
            return $namedPeriods[$interval][$count];
        }

        $pluralIntervals = [
            'day' => 'days',
            'week' => 'weeks',
            'month' => 'months',
            'year' => 'years',
        ];

        $plural = $pluralIntervals[$interval] ?? $interval.'s';

        return 'every '.$count.' '.$plural;
    }
}
