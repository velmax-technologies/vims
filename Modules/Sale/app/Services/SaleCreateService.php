<?php

namespace Modules\Sale\Services;

use App\Models\Item;
use App\Models\Sale;
use App\Models\Stock;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\Sale\Transformers\SaleResource;
use Modules\StockAdjustment\Services\StockAdjustmentService;
use Symfony\Component\HttpFoundation\JsonResponse;

class SaleCreateService
{
    use ApiResponseFormatTrait;

    public function create(Request $request){
        // check stock availability for each sale item
         foreach ($request->sale_items as $sale_item) {
            $item = Item::find($sale_item['item_id']);
            $availableQuantity = $item->available_quantity;

            // if requested quantity is more than available and item is not a kitchen menu
            if ($sale_item['quantity'] > $availableQuantity && !$item->is_kitchen_menu) {
                return $this->errorResponse("Only ($availableQuantity) pieces of $item->name are available in stock.", 400, null);
            }

            // if item is a kitchen menu, check stock for each component
            if ($item->is_kitchen_menu) {
                foreach ($item->menu_items as $menuItem) {
                   
                    $componentItem = Item::find($menuItem->item_id);
                    $availableQuantity = $componentItem->available_quantity;
                    $requiredQuantity = $menuItem->quantity * $sale_item['quantity'];
                    //return $availableQuantity;
                    if ($requiredQuantity > $availableQuantity) {
                        return $this->errorResponse("Only ($availableQuantity) pieces of $componentItem->name are available in stock for the menu item $item->name.", 400, null);
                    }
                }
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
                        'cost' => $item['cost'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'line_total' => $item['price'] * $item['quantity'],
                        'sale_id' => $sale->id,
                    ];
                })->toArray()
            );

           // stock adjustment for each sale item
            foreach ($sale->sale_items as $sale_item) {
                if($sale_item->item->is_kitchen_menu) {
                   
                    // for kitchen menu, adjust stock for each component
                    foreach ($sale_item->item->menu->menu_items as $menuItem) {
                       
                        $componentItem = Item::find($menuItem->item_id);
                        $requiredQuantity = $menuItem->quantity * $sale_item['quantity'];
                       
                        // Create stock adjustment
                        $data = [
                            'type' => 'sale',
                            'item_id' => $menuItem->item_id,
                            'quantity' => $requiredQuantity,
                            'note' => 'Stock adjusted for Sale ID: ' . $sale->id,
                            'adjusted_at' => now(),
                        ];

                        (new StockAdjustmentService())->adjust($data);

                    }
                } else {
                    // for regular items, adjust stock directly
                    $data = [
                        'type' => 'sale',
                        'item_id' => $sale_item->item_id,
                        'quantity' => $sale_item->quantity,
                        'note' => 'Stock adjusted for Sale ID: ' . $sale->id,
                        'adjusted_at' => now(),
                    ];

                    (new StockAdjustmentService())->adjust($data);
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
            
        } catch (\Exception $e) {
            DB::rollBack();
            //return $e->getMessage();
            //return $this->errorResponse($e->getMessage(), 400, null);
        }

        return (new SaleResource($sale))
            ->additional($this->preparedResponse('store'));
    }
}
