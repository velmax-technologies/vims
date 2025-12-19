<?php

namespace Modules\Purchase\Http\Controllers;

use App\Models\User;
use App\Models\ItemCost;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Database\QueryException;
use Modules\Purchase\Http\Requests\PurchaseRequest;
use Modules\Purchase\Transformers\PurchaseResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\StockAdjustment\Services\StockAdjustmentService;

class PurchaseController extends Controller
{

    use ApiResponseFormatTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $purchases = Purchase::all();
        return (PurchaseResource::collection($purchases))->additional($this->preparedResponse('index'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PurchaseRequest $request)
    {
        $request->validated();
       
        try {
            DB::beginTransaction();

            $user = User::find(Auth::id());

            $purchase = $user->purchases()->create($request->all());

            // Attach items if provided
            if ($request->has('purchase_items')) {
                $purchase->purchase_items()->createMany($request->purchase_items);
            }

            // log
            activity()
                ->causedBy($user)
                ->performedOn($purchase)
                ->log('Purchase created - Invoice #' . $purchase->invoice_number);

            DB::commit();
            return (new PurchaseResource($purchase))->additional($this->preparedResponse('store'));
        } catch (\Exception $e) {
            DB::rollBack();
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
        try {
            $purchase = Purchase::with('purchase_items')->findOrFail($id);
            
            return (new PurchaseResource($purchase))->additional($this->preparedResponse('show'));
        } catch (ModelNotFoundException $modelException) {
            return $this->recordNotFoundResponse($modelException);
        } catch (QueryException $queryException) {
            return $this->queryExceptionResponse($queryException);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PurchaseRequest $request, $id)
    {
        $request->validated();

        try {
            DB::beginTransaction();
            $purchase = Purchase::findOrFail($id);
            if($purchase->status == 'completed') {
                return $this->errorResponse("Can not update a completed purchase", 400, null);
            }

            $purchase->update($request->all());

            // Update purchase items
            if ($request->has('purchase_items')) {

                foreach ($request->purchase_items as $item) {
                    PurchaseItem::updateOrCreate(
                        [
                            'purchase_id' => $purchase->id,
                            'item_id' => $item['item_id'],
                        ],
                        [
                            'quantity' => $item['quantity'],
                            'cost' => $item['cost'],
                            'line_total' => $item['line_total']
                        ]
                    );  
                }

                
            }

            // purchase status
            if($purchase->status == 'completed') {
                // Update stock quantities
                foreach ($purchase->purchase_items as $purchaseItem) {
                    $item = $purchaseItem->item;
                    $stock = $item->stocks()->create(
                        [
                            'note' => 'Purchase - ' . $purchase->invoice_number,
                            'quantity' => $purchaseItem->quantity,
                            'available_quantity' => $purchaseItem->quantity,
                        ],
                       
                    );

                    // stock adjustment
                    $adjustmentData = [
                        'item_id' => $purchaseItem->item_id,
                        'quantity' => $purchaseItem->quantity,
                        'note' => 'Purchase - Invoice #' . $purchase->invoice_number,
                        'type' => 'addition',
                        'adjusted_at' => now(),
                    ];

                    //$stock->increment('quantity', $purchaseItem->quantity);

                    (new StockAdjustmentService())->adjust($adjustmentData);

                    // Update item cost
                    ItemCost::updateOrCreate(
                        [
                            'item_id' => $purchaseItem->item_id,
                            'stock_id' => $stock->id, 
                        ],
                        [
                            'cost' => $purchaseItem->cost,
                        ]
                    );

                }
                 
            }

            DB::commit();
            return (new PurchaseResource($purchase))->additional($this->preparedResponse('update'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 400, null);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $purchase = Purchase::findOrFail($id);
            if($purchase->status == 'completed') {
                return response()->json([
                    'message' => 'Cannot delete a completed purchase.',
                    'status' => 'failed'
                ], 400);
            }

            $purchase->delete();
            DB::commit();

            return (PurchaseResource::make($purchase))->additional($this->preparedResponse('destroy'));

            
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 400, null);
        }
    }
}
