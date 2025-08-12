<?php

namespace Modules\UserRolePermission\app\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                       => $this->id,
            'user_id'                  => $this->user_id,
            'driving_license_number'   => $this->driving_license_number,
            'driving_license_expiry'   => $this->driving_license_expiry,
            'driving_license_image'    => getImageUrl($this->driving_license_image),
            'car_model'                => $this->car_model,
            'car_make'                 => $this->car_make,
            'car_year'                 => $this->car_year,
            'car_color'                => $this->car_color,
            'car_plate_number'         => $this->car_plate_number,
            'car_image'                => getImageUrl($this->car_image),
            'face_embedding'           => $this->face_embedding,
            'is_verified'              => $this->is_verified,
            'device_token'             => $this->device_token,
            'status'                   => $this->status,
        ];
    }
}
