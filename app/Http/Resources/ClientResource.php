<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'surnom' => $this->surname,
            'adresse' => $this->adresse,
            'telephone' => $this->telephone,
            // 'photo' => $this->user->photo,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
