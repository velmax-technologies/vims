<?php

namespace Modules\StockAdjustment\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockAdjustment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseFormatTrait;
use Modules\StockAdjustment\Http\Requests\StockAdjustmentRequest;
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
        $request->validated();

        try {
             // get the authenticated user
            $user = Auth::user();

            // merge the user_id into the request data
            $request->merge(['user_id' => $user->id]);

            $stockAdjustment = StockAdjustment::create($request->all());

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
