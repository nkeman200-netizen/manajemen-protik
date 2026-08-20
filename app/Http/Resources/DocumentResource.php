<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'created_by'    => $this->created_by,
            'event_id'      => $this->event_id,
            'letter_number' => $this->letter_number,
            'title'         => $this->title,
            'drive_url'     => $this->drive_url,
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
            'creator'       => $this->whenLoaded('creator'),
            'event'         => $this->whenLoaded('event'),
        ];
    }
}
