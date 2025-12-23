<?php

namespace Modules\StockAdjustment\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseFormatTrait;
use Modules\StockAdjustment\Http\Requests\StockAdjustmentRequest;
use Modules\StockAdjustment\Services\StockAdjustmentCreateService;
use Modules\StockAdjustment\Transformers\StockAdjustmentResource;

class StockAdjustmentController extends Controller
{
    use ApiResponseFormatTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       
        // Fetch all stock adjustments
        $stockAdjustments = StockAdjustment::all();

        return (StockAdjustmentResource::collection($stockAdjustments))
            ->additional($this->preparedResponse('index'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StockAdjustmentRequest $request) {
       if (!Auth::user()->can('manage sales')) {
            return $this->errorResponse('Unauthorized', 403, null);
        }

        $request->validated();

        try {
           
            $stockAdjustment = (new StockAdjustmentCreateService())->create($request);

            return (new StockAdjustmentResource($stockAdjustment))
                ->additional($this->preparedResponse('store'));
        } catch (\Exception $e) {
            return response([
                'message' => $e->getMessage(),
                'status' => 'failed'
            ], 400); 
        }   
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        //

        return response()->json([]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //

        return response()->json([]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //

        return response()->json([]);
    }
}
