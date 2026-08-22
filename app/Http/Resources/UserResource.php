<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'email'    => $this->email,
            'nim'      => $this->nim,
            'phone'    => $this->phone,
            'prodi'    => $this->prodi,
            'angkatan' => $this->angkatan,
            'address'  => $this->address,
            'status'   => $this->status,
            'division' => $this->whenLoaded('division'),
            'roles'    => $this->whenLoaded('roles'),
        ];
    }
}
