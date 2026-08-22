<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'event_id'       => $this->event_id,
            'type'           => $this->type,
            'category'       => $this->category,
            'funding_source' => $this->funding_source,
            'title'          => $this->title ?? $this->description,
            'description'    => $this->description ?? $this->title,
            'qty'            => (float) $this->qty,
            'unit'           => $this->unit,
            'unit_price'     => (float) $this->unit_price,
            'amount'         => (float) $this->amount,
            'pic'            => $this->pic,
            'payment_method' => $this->payment_method,
            'notes'          => $this->notes,
            'receipt_url'    => $this->receipt_url,
            'date'           => $this->date?->format('Y-m-d'),
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
            'user'           => $this->whenLoaded('user'),
            'event'          => $this->whenLoaded('event'),
        ];
    }
}
