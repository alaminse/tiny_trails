<?php

namespace Modules\UserRolePermission\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
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
            'country_id'        => $this->country_name,
            'state_id'          => $this->state_name,
            'city_id'           => $this->city_name,
            'status'            => $this->status,
        ];

        // Check if the user has the 'driver' role
        if ($this->roles->contains('name', 'driver')) {
            $data['driver'] = new DriverResource($this->whenLoaded('driver'));
        }

        return $data;
    }
}
