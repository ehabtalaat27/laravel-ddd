<?php

namespace App\User\Presentation\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'active' => (bool) $this->active,
            'info' => new UserInfoResource($this->info),
        ];
    }
}
