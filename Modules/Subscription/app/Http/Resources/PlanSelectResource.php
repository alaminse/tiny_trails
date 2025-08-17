<?php

namespace Modules\Subscription\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanSelectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->name . ' - ' . $this->formatted_price . '/' . $this->interval_display,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'interval' => $this->interval,
            'interval_display' => $this->interval_display,
            'status' => $this->status,
        ];
    }
}