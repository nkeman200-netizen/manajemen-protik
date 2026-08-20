<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingAttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'meeting_id' => $this->meeting_id,
            'user_id'    => $this->user_id,
            'status'     => $this->status,
            'proof_url'  => $this->proof_url,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'meeting'    => $this->whenLoaded('meeting'),
            'user'       => $this->whenLoaded('user'),
        ];
    }
}
