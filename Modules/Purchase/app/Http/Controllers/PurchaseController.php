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
use Modules\Purchase\Http\Requests\PurchaseRequest;
use Modules\Purchase\Transformers\PurchaseResource;

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
        //

        return response()->json([]);
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

                // Clear existing items and add new ones

                //$purchase->purchase_items()->delete(); // Clear existing items
                //$purchase->purchase_items()->createMany($request->purchase_items);
            }

            // purchase status
            if($purchase->status == 'completed') {
                // Update stock quantities
                foreach ($purchase->purchase_items as $purchaseItem) {
                    $item = $purchaseItem->item;
                    $stock = $item->stocks()->create(
                        [
                            'note' => 'Purchase - ' . $purchase->id],
                        [
                            'quantity' => $purchaseItem->quantity
                        ]
                    );
                    
                    $stock->increment('quantity', $purchaseItem->quantity);

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
