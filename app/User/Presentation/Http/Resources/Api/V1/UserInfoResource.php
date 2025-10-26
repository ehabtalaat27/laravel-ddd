<?php

namespace App\User\Presentation\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'height' => $this->height,
            'weight' => $this->weight,
            'gender' => $this->gender?->label(),
            'fitness_level' => $this->fitness_level?->label()
        ];
    }
}
