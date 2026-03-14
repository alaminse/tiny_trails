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
            'middle_name'              => $this->middle_name,
            'driving_license_number'   => $this->driving_license_number,
            'licence_card_number'      => $this->licence_card_number,
            'licence_type'             => $this->licence_type,
            'licence_address_line_1'   => $this->licence_address_line_1,
            'licence_address_line_2'   => $this->licence_address_line_2,
            'licence_city'             => $this->licence_city,
            'licence_state'            => $this->licence_state,
            'licence_postal_code'      => $this->licence_postal_code,
            'licence_country'          => $this->licence_country,
            'wwc_card_number'          => $this->wwc_card_number,
            'wwc_expiry_date'          => $this->wwc_expiry_date,
            'wwc_card_image'           => getImageUrl($this->wwc_card_image),
            'police_clearance_ref'     => $this->police_clearance_ref,
            'police_clearance_image'   => getImageUrl($this->police_clearance_image),
            'other_qualifications'     => $this->other_qualifications,
            'driving_license_expiry'   => $this->driving_license_expiry,
            'driving_license_image'    => getImageUrl($this->driving_license_image),
            'car_model'                => $this->car_model,
            'car_make'                 => $this->car_make,
            'car_year'                 => $this->car_year,
            'car_color'                => $this->car_color,
            'car_plate_number'         => $this->car_plate_number,
            'car_image'                => getImageUrl($this->car_image),
            'face_embedding'           => $this->face_embedding,
            'face_image'               => getImageUrl($this->face_image),
            'is_verified'              => (int) $this->getRawOriginal('is_verified'), // ← fix
            'device_token'             => $this->device_token,
            'status'                   => $this->status,
            'vehicle_type_id'          => $this->vehicle_type_id,
            'availability_status'      => $this->availability_status,
            'face_verified_at'         => $this->face_verified_at,
            'face_verified_until'      => $this->face_verified_until,
            'created_at'               => $this->created_at,
            'updated_at'               => $this->updated_at,
            'deleted_at'               => $this->deleted_at,
        ];
    }
}
