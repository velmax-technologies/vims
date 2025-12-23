<?php

namespace Modules\Sale\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Database\QueryException;
use Modules\Sale\Http\Requests\SaleRequest;
use Modules\Sale\Transformers\SaleResource;
use Modules\Sale\Services\SaleCreateService;
use Modules\Sale\Services\SaleUpdateService;
use Modules\Sale\Services\SaleCompleteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Sale\Services\SaleReturnService;

class SaleController extends Controller
{
    use ApiResponseFormatTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::all();

        $sales = QueryBuilder::for(Sale::class)
        ->allowedFilters(['user.name', 'customer.name'])
        ->get();

        return SaleResource::collection($sales)
            ->additional($this->preparedResponse('index'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaleRequest $request)
    {
        // check permission
        if (!Auth::user()->can('create sale')) {
            return $this->errorResponse('Unauthorized', 403, null);
        }

        $request->validated();

        return (new SaleCreateService())->create($request);
         
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $sale = Sale::findOrFail($id);

            return (new SaleResource($sale))
                ->additional($this->preparedResponse('show'));
        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaleRequest $request, $id)
    {
         $request->validated();

        try {
            $sale = Sale::findOrFail($id);

            DB::beginTransaction();

            // completing the sale
            if ($request->status === 'completed' && $sale->status !== 'completed') {
                if (!Auth::user()->can('manage sales')) {
                    return $this->errorResponse('Unauthorized', 403, null);
                }
                (new SaleCompleteService())->complete($request->all(), $sale);
            }elseif($request->status == 'pending' && $sale->status == 'pending') {
                if (!Auth::user()->can('manage sales')) {
                    return $this->errorResponse('Unauthorized', 403, null);
                }
                (new SaleUpdateService())->update($request->all(), $sale);
            }
            elseif($request->status == 'cancelled' && $sale->status === 'completed') {
                if (!Auth::user()->can('cancel completed sale')) {
                    return $this->errorResponse('Unauthorized to cancel completed sales', 403, null);
                }
                (new SaleReturnService())->returnSale($request->all(), $sale);
            }
            elseif($request->status == 'returned' && $sale->status === 'completed') {
                if (!Auth::user()->can('return completed sale')) {
                    return $this->errorResponse('Unauthorized to return completed sales', 403, null);
                }
                (new SaleReturnService())->returnSale($request->all(), $sale);
            }
            elseif($request->status == 'cancelled' && $sale->status === 'pending') {
               
                (new SaleReturnService())->returnSale($request->all(), $sale);
            }   

             // log sale update
             activity()
             ->causedBy(Auth::user())
             ->performedOn($sale)
             ->withProperties(['sale_id' => $sale->id])
             ->log('Sale updated with ID: ' . $sale->id);


            DB::commit();
            return (new SaleResource($sale))
                ->additional($this->preparedResponse('update'));
        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
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
