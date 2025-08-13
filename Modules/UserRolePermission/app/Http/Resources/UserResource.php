<?php

namespace Modules\UserRolePermission\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        // If user has the 'driver' role, ensure relation is present
        if ($this->resource->hasRole('driver')) {
            $this->resource->loadMissing('driver');
        }

        return [
            'id'                => $this->id,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'dob'               => $this->dob,
            'gender'            => $this->gender,
            'height_cm'         => $this->height_cm,
            'weight_kg'         => $this->weight_kg,
            'photo'             => getImageUrl($this->photo),
            'address'           => $this->address,
            'country'           => $this->country_name,
            'state'             => $this->state_name,
            'city'              => $this->city_name,
            'status'            => $this->status,
            // Include only if the user is a driver and relation exists
            'driver'     => $this->when(
                $this->resource->hasRole('driver') && $this->driver,
                fn () => new DriverResource($this->driver)
            ),
        ];
    }
}
