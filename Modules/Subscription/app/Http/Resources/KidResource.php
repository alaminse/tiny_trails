<?php

namespace Modules\UserRolePermission\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KidResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'user_id'           => $this->user_id,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'dob'               => $this->dob,
            'gender'            => $this->gender,
            'height_cm'         => $this->height_cm,
            'weight_kg'         => $this->weight_kg,
            'photo'             => getImageUrl($this->photo),
            'school_name'       => $this->school_name,
            'school_address'    => $this->school_address,
            'emergency_contact' => $this->emergency_contact,
            'parent_first_name' => $this->parent->first_name ?? null,
            'parent_last_name'  => $this->parent->last_name ?? null,
            'parent_email'      => $this->parent->email ?? null,
            'parent_phone'      => $this->parent->phone ?? null,
            'parent_dob'        => $this->parent->dob ?? null,
            'parent_gender'     => $this->parent->gender ?? null,
            'parent_photo'      => getImageUrl($this->parent->photo),
        ];
    }
}
