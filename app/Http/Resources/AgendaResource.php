<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgendaResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'          => $this->id,
            'event_id'    => $this->event_id,
            'title'       => $this->title,
            'start_date'  => $this->start_date?->format('Y-m-d H:i:s'),
            'end_date'    => $this->end_date?->format('Y-m-d H:i:s'),
            'location'    => $this->location,
            'pic'         => $this->pic,
            'status'      => $this->status,
            'minutes_url' => $this->minutes_url,
            'attendances' => $this->whenLoaded('attendances'),
            'targets'     => $this->whenLoaded('targets'),
        ];
    }
}
