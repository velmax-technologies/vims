<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->id,
                'username' => $this->username,
                'email' => $this->email,
                'name' => $this->name,
                'phone' => $this->phone,
                'is_active' => $this->is_active,
                'is_admin' => $this->is_admin,
                'roles' => $this->roles->pluck('name'),
                'permissions' => $this->getAllPermissions()->pluck('name'),
            ],
        ];
    }
}
