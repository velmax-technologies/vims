<?php

namespace Modules\Order\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use App\Traits\ApiResponseFormatTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\QueryException;
use Modules\Order\Http\Requests\OrderRequest;
use Modules\Order\Transformers\OrderResource;
use Modules\Order\Services\OrderCreateService;
use Modules\Order\Services\OrderReturnService;
use Modules\Order\Services\OrderUpdateService;
use Modules\Order\Services\OrderCompleteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    use ApiResponseFormatTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $orders = QueryBuilder::for(Order::class)
            ->allowedFilters([
                'user.id', 'status',
                // Filters for 'created_at' matching today
                AllowedFilter::callback('created_today', function ($query, $value) {
                    $query->whereDate('created_at', today()); // 'today()' gets the current date
                }),
                // Example: Filter for a specific date range
                AllowedFilter::callback('created_between', function ($query, $value) {
                    $query->whereBetween('created_at', $value);
                }),
            ])
            ->get();

        return OrderResource::collection($orders)
            ->additional($this->preparedResponse('index'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderRequest $request)
    {
       // check permission
        if (!Auth::user()->can('create sale')) {
            return $this->errorResponse('Unauthorized', 403, null);
        }
       
        $request->validated();

        return (new OrderCreateService())->create($request);
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
    public function update(OrderRequest $request, $id)
    {
         // check permission
        if (!Auth::user()->can('create sale')) {
            return $this->errorResponse('Unauthorized', 403, null);
        }

        $request->validated();

        try {
            $order = Order::findOrFail($id);

            DB::beginTransaction();

            // completing the order

            if ($request->status === 'completed' && $order->status !== 'completed') {
                if (!Auth::user()->can('manage sales')) {
                    return $this->errorResponse('Unauthorized', 403, null);
                }
                (new OrderCompleteService())->complete($request->all(), $order);
            }elseif($request->status == 'pending' && $order->status == 'pending') {
                if (!Auth::user()->can('manage sales')) {
                    return $this->errorResponse('Unauthorized', 403, null);
                }
                (new OrderUpdateService())->update($request->all(), $order);
            }
            elseif($request->status == 'cancelled' && $order->status === 'completed') {
                if (!Auth::user()->can('cancel completed sale')) {
                    return $this->errorResponse('Unauthorized to cancel completed sales', 403, null);
                }
                (new OrderReturnService())->returnOrder($request->all(), $order);
            }
            elseif($request->status == 'returned' && $order->status === 'completed') {
                if (!Auth::user()->can('return completed sale')) {
                    return $this->errorResponse('Unauthorized to return completed sales', 403, null);
                }
                (new OrderReturnService())->returnOrder($request->all(), $order);
            }
            elseif($request->status == 'cancelled' && $order->status === 'pending') {
               
                (new OrderReturnService())->returnOrder($request->all(), $order);
            }   

             // log sale update
             activity()
             ->causedBy(Auth::user())
             ->performedOn($order)
             ->withProperties(['order_id' => $order->id])
             ->log('Order updated with ID: ' . $order->id);


            DB::commit();
            return (new OrderResource($order))
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
