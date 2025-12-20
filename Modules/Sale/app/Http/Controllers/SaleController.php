<?php

namespace Modules\Sale\Http\Controllers;

use App\Models\Item;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\ItemSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\QueryBuilder;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Database\QueryException;
use Modules\Sale\Http\Requests\SaleRequest;
use Modules\Sale\Transformers\SaleResource;
use Modules\Sale\Services\ReturnSaleService;
use Modules\Sale\Services\CompleteSaleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\StockAdjustment\Services\StockAdjustmentService;

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
        // user can create sale
        if (!Auth::user()->can('create sale')) {
            return $this->errorResponse('Unauthorized', 403, null);
        }

         $request->validated();

         // check stock availability for each sale item
         foreach ($request->sale_items as $item) {
            $itemModel = Item::find($item['item_id']);
            $availableQuantity = $itemModel->available_quantity;

            if ($item['quantity'] > $availableQuantity) {
                return $this->errorResponse("Only ($availableQuantity) pieces of $itemModel->name are available in stock.", 400, null);
            }
         }

        try {
            DB::beginTransaction();

            // get the authenticated user
            $user = Auth::user();

            // merge the user_id into the request data
            $request->merge(['user_id' => $user->id]);

            // Create the sale record
            $sale = Sale::create($request->all());

            // Process sale items
            $sale->sale_items()->createMany(
                collect($request->sale_items)->map(function ($item) use ($sale) {
                    return [
                        'item_id' => $item['item_id'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'line_total' => $item['price'] * $item['quantity'],
                        'sale_id' => $sale->id,
                    ];
                })->toArray()
            );

            // stock adjustment data
            foreach ($sale->sale_items as $saleItem) {
                $data = [
                    'type' => 'sale',
                    'item_id' => $saleItem->item_id,
                    'quantity' => $saleItem->quantity,
                    'note' => 'Stock adjusted for Sale ID: ' . $sale->id,
                    'adjusted_at' => now(),
                ];

                // create stock adjustment
                (new StockAdjustmentService())->adjust($data);

                // update available quantity in stocks
                $quantity = $saleItem->quantity;
                foreach (Stock::where('item_id', $saleItem->item_id)->get() as $stock) {
                    if ($quantity <= 0) {
                        break;
                    }

                    if ($stock->available_quantity >= $quantity) {
                        $stock->available_quantity -= $quantity;
                        $stock->save();
                        $quantity = 0;
                    } else {
                        $quantity -= $stock->available_quantity;
                        $stock->available_quantity = 0;
                        $stock->save();
                    }
                }             
                
            }

            // log sale creation
            activity()
                ->causedBy($user)
                ->performedOn($sale)
                ->withProperties(['sale_id' => $sale->id])
                ->log('Sale created with ID: ' . $sale->id);

            // Commit the transaction
            DB::commit(); 
            return(new SaleResource($sale))
                ->additional($this->preparedResponse('store'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 400, null);
        }
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
        // user can create sale
        if (!Auth::user()->can('manage sales')) {
            return $this->errorResponse('Unauthorized', 403, null);
        }

         $request->validated();

        try {
            $sale = Sale::findOrFail($id);

            DB::beginTransaction();

            // completing the sale
            if ($request->status === 'completed' && $sale->status !== 'completed') {
                (new CompleteSaleService())->completeSale($request->all(), $sale);
            }elseif($request->status == 'pending' && $sale->status == 'pending') {
                $sale->update($request->all());
            }
            elseif($request->status == 'cancelled' || $request->status == 'returned') {
                (new ReturnSaleService())->returnSale($request->all(), $sale);
            }
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
