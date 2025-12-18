<?php

namespace Modules\Activity\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseFormatTrait;
use Spatie\Activitylog\Models\Activity;
use Modules\Activity\Transformers\ActivityResource;

class ActivityController extends Controller
{
        use ApiResponseFormatTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activities = Activity::all();
        return (ActivityResource::collection($activities))->additional($this->preparedResponse('index'));
    }

    
}
