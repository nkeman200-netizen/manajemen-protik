<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'description'       => $this->description,
            'budget_approved'   => (float) $this->budget_approved,
            'drive_folder_url'  => $this->drive_folder_url,
            'document_sync_url' => $this->document_sync_url,
            'finance_sync_url'  => $this->finance_sync_url,
            'agenda_sync_url'   => $this->agenda_sync_url,
            'start_date'        => $this->start_date?->format('Y-m-d'),
            'end_date'          => $this->end_date?->format('Y-m-d'),
            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),
            'committees'        => $this->whenLoaded('committees'),
            'finances'          => $this->whenLoaded('finances'),
        ];
    }
}
