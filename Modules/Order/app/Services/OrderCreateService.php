<?php

namespace Modules\Order\Services;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseFormatTrait;
use Modules\Order\Transformers\OrderResource;
use Modules\StockAdjustment\Services\StockAdjustmentService;

class OrderCreateService
{
    use ApiResponseFormatTrait;
    
    public function create(Request $request){
        // check stock availability for each sale item
         foreach ($request->order_items as $order_item) {
            $item = Item::find($order_item['item_id']);
            $availableQuantity = $item->available_quantity;

            // if requested quantity is more than available and item is not a kitchen menu
            if ($order_item['quantity'] > $availableQuantity && !$item->is_kitchen_menu) {
                return $this->errorResponse("Only ($availableQuantity) pieces of $item->name are available in stock.", 400, null);
            }

            // if item is a kitchen menu, check stock for each component
            if ($item->is_kitchen_menu) {
                foreach ($item->menu_items as $menuItem) {
                   
                    $componentItem = Item::find($menuItem->item_id);
                    $availableQuantity = $componentItem->available_quantity;
                    $requiredQuantity = $menuItem->quantity * $order_item['quantity'];
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

            // Create the sale record
            $order = $user->orders()->create($request->all());

            // Process sale items
            $order->order_items()->createMany(
                collect($request->order_items)->map(function ($item) use ($order) {
                    return [
                        'item_id' => $item['item_id'],
                        'cost' => $item['cost'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'line_total' => $item['price'] * $item['quantity'],
                        'order_id' => $order->id,
                    ];
                })->toArray()
            );

           // stock adjustment for each order item
            foreach ($order->order_items as $order_item) {
                if($order_item->item->is_kitchen_menu) {
                   
                    // for kitchen menu, adjust stock for each component
                    foreach ($order_item->item->menu->menu_items as $menuItem) {
                       
                        $componentItem = Item::find($menuItem->item_id);
                        $requiredQuantity = $menuItem->quantity * $order_item['quantity'];
                       
                        // Create stock adjustment
                        $data = [
                            'type' => 'subtraction',
                            'item_id' => $menuItem->item_id,
                            'quantity' => $requiredQuantity,
                            'model' => 'order',
                            'model_id' => $order->id,
                            'reason' => 'order',
                            'adjusted_at' => now(),
                        ];

                        (new StockAdjustmentService())->adjust($data);

                    }
                } else {
                    // for regular items, adjust stock directly
                    $data = [
                        'type' => 'subtraction',
                        'item_id' => $order_item->item_id,
                        'quantity' => $order_item->quantity,
                        'model' => 'order',
                        'model_id' => $order->id,
                        'reason' => 'order',
                        'adjusted_at' => now(),
                    ];

                    (new StockAdjustmentService())->adjust($data);
                }
            }

            // log sale creation
            activity()
                ->causedBy($user)
                ->performedOn($order)
                ->withProperties(['order_id' => $order->id])
                ->log('Order created with ID: ' . $order->id);

            // Commit the transaction
            DB::commit(); 
            
        } catch (\Exception $e) {
            DB::rollBack();
            //throw new \Exception($e->getMessage());
            //return $e->getMessage();
            return $this->errorResponse($e->getMessage(), 400, null);
        }

        return (new OrderResource($order))
            ->additional($this->preparedResponse('store'));
    }
}
