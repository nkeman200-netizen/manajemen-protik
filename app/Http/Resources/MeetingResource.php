<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'event_id'    => $this->event_id,
            'title'       => $this->title,
            'date'        => $this->date?->format('Y-m-d H:i:s'),
            'minutes_url' => $this->minutes_url,
            'created_at'  => $this->created_at?->toISOString(),
            'updated_at'  => $this->updated_at?->toISOString(),
            'attendances' => $this->whenLoaded('attendances'),
            'event'       => $this->whenLoaded('event'),
        ];
    }
}
