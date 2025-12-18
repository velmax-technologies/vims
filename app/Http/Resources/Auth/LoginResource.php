<?php

namespace App\Http\Resources\Auth;

use App\Enums\Messages;
use App\Enums\ApiStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'response' => [
                'status'      => ApiStatus::SUCCESS,
                'status_code' => Response::HTTP_OK,
                'message'     => Messages::LOGIN_SUCCESSFUL, 
            ],
            'data' => [
                'token_type'   => 'bearer',
                'access_token' => $this->resource,
                'expires_in'   => JWTAuth::factory()->getTTL() * 60,
                'user'         => Auth::user(),
                //'roles'        => Auth::user()->getRoleNames(),
            ],
        ];
    }
}
