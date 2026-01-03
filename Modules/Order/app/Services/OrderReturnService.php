<?php

namespace Modules\Order\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Modules\StockAdjustment\Services\StockAdjustmentService;

class OrderReturnService
{
    public function returnOrder(Array $requestData, Order $order) {

        try{
            DB::beginTransaction();

            $order->update($requestData);

            // Adjust stocks back for each sale item
            foreach ($order->order_items as $orderItem) {
                if($orderItem->item->is_kitchen_menu){
                    foreach($orderItem->item->menu->menu_items as $menu_item){
                        $requiredQuantity = $orderItem->quantity * $menu_item->quantity;

                        $data = [
                            'type' => 'addition',
                            'item_id' => $menu_item->item_id,
                            'model' => 'order',
                            'model_id' => $order->id,
                            'quantity' => $requiredQuantity,
                            'reason' => $requestData['status'],
                            'adjusted_at' => now(),
                        ];

                        (new StockAdjustmentService())->adjust($data);
                    }
                }
                else{
                    $data = [
                        'type' => 'addition',
                        'item_id' => $orderItem->item_id,
                        'model' => 'order',
                        'model_id' => $order->id,
                        'quantity' => $orderItem->quantity,
                        'reason' => $requestData['status'],
                        'adjusted_at' => now(),
                    ];

                     (new StockAdjustmentService())->adjust($data);
                }
               
            }

        // delete associated item sales records
        $order->order_items()->delete();

        // log sale return
        activity()
            ->causedBy(auth()->user())
            ->performedOn($order)
            ->withProperties(['order_id' => $order->id])
            ->log('Order ' . $requestData['status'] . ' with ID: ' . $order->id);

            DB::commit();

    } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    

        return true;
    }
}
