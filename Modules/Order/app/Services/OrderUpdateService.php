<?php

namespace Modules\Order\Services;

use App\Models\Order;
use App\Models\Stock;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\StockAdjustment\Services\StockAdjustmentService;

class OrderUpdateService
{
      public function update(Array $requestData, Order $order) {

        try{
            
            DB::beginTransaction();

            $order->update($requestData);

            // update order items if provided
            if (isset($requestData['order_items'])) {

                

                // delete order items
                $order->order_items()->delete();               

                // delete order item sales
                //$order->item_sales()->delete();

                // delete order stock adjustments
                $stockAdjustments = StockAdjustment::where([['model', 'order'],['model_id', $order->id]])->get();

                foreach($stockAdjustments as $stockAdjustment){
                    
                    // adjust stock

                    $stocks = Stock::where('item_id', $stockAdjustment->item_id)->orderBy('created_at', 'desc')->get();
                    $quantity = $stockAdjustment->quantity;
                    
                    foreach($stocks as $stock){
                        
                        if($stock->quantity - $stock->available_quantity >= $quantity){
                            $stock->available_quantity += $quantity;
                            $stock->save();
                        }
                        else{
                            $quantity = $stock->quantity - $stock->available_quantity;
                            $stock->available_quantity = $stock->quantity;
                            $stock->save();
                        }
                    }

                    $stockAdjustment->delete();
                }

                // create order items
                foreach ($requestData['order_items'] as $itemData) {
                    $order->order_items()->create($itemData);
                }
                
                // create new item sales
                // foreach ($requestData['sale_items'] as $itemData) {
                //     $sale->item_sales()->create($itemData);
                // }

                // create stock adjustments
                // stock adjustment for each order item
            foreach ($order->order_items as $order_item) {
                 
                if($order_item->item->is_kitchen_menu) {
                    
                    // for kitchen menu, adjust stock for each component
                    foreach ($order_item->item->menu->menu_items as $menuItem) {

                        
                       
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
                ->causedBy(Auth::user())
                ->performedOn($order)
                ->withProperties(['order_id' => $order->id])
                ->log('Order updated with ID: ' . $order->id);
            }


            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }

        return true;

    }
}
