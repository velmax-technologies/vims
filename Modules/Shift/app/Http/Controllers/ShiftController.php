<?php

namespace Modules\Shift\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use App\Traits\ApiResponseFormatTrait;
use Modules\Shift\Http\Requests\ShiftRequest;
use Modules\Shift\Transformers\ShiftResource;

class ShiftController extends Controller
{
    use ApiResponseFormatTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$shifts = Shift::all();

         $shifts = QueryBuilder::for(Shift::class)
        ->allowedFilters(['user_id','is_active'])
        ->get();

        return ShiftResource::collection($shifts)
            ->additional($this->preparedResponse('index'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShiftRequest $request)
    {
        // check cashier role
        if (!$request->user()->hasRole('cashier')) {
            return $this->errorResponse('Only cashiers can start a shift.', 403, null);
        }

        // check if there's already an active shift
        if (Shift::where('is_active', true)->exists()) {
            return $this->errorResponse('error', 400, 'There is already an active shift.');
        }

        $shift = $request->user()->shifts()->create([
            'start_time' => now(),
            'end_time' => now()->addHours(24), // default 24 hours shift
            'is_active' => true,
        ]);

        return ShiftResource::make($shift)
            ->additional($this->preparedResponse('store'));
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
    public function update(ShiftRequest $request, $id)
    {
        try {
            $shift = Shift::findOrFail($id);

            // Only allow updating if the shift is active
            if (!$shift->is_active) {
                return $this->errorResponse('error', 400, 'Cannot update an inactive shift.');
            }
            $shift->is_active = $request->is_active;
            $shift->end_time = now();
            $shift->save();

            return ShiftResource::make($shift)
                ->additional($this->preparedResponse('update'));
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400, null);
        }
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
