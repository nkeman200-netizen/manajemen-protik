<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarningResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'admin_id'   => $this->admin_id,
            'reason'     => $this->reason,
            'date'       => $this->date?->format('Y-m-d'),
            'read_at'    => $this->read_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'user'       => $this->whenLoaded('user'),
            'admin'      => $this->whenLoaded('admin'),
        ];
    }
}
