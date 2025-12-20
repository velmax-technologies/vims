<?php

namespace Modules\File\Transformers;

use App\Enums\Messages;
use App\Enums\ApiStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\JsonResource;

class FileUploadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'response' => [
                'status'      => ApiStatus::SUCCESS,
                'status_code' => Response::HTTP_OK,
                'message'     => Messages::UPLOAD_SUCCESSFUL, 
            ],
            
        ];
    }
}
