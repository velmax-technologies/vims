<?php

namespace Modules\Order\Services;

use App\Models\Sale;
use App\Models\Order;
use App\Models\ItemSale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponseFormatTrait;
use Illuminate\Support\Facades\Auth;
use Modules\Sale\Transformers\SaleResource;

class OrderCompleteService
{
    use ApiResponseFormatTrait;
    public function complete(Array $requestData, Order $order) {
        try {
            DB::beginTransaction();

            $order->update($requestData);
             
            // create sale
            $sale = Sale::create([
                'user_id' => $order->user_id,
                'customer_id' => $order->customer_id,
                'total_amount' => $order->total_amount,
                'sold_at' => now(),
                'status' => 'completed',
            ]);

            // create sale items
            foreach ($order->order_items as $orderItem) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_id' => $orderItem->item_id,
                    'quantity' => $orderItem->quantity,
                    'cost' => $orderItem->cost,
                    'price' => $orderItem->price,
                    'line_total' => $orderItem->line_total,
                ]);
            }

            // create item sale
            foreach ($order->order_items as $orderItem) {
                ItemSale::create([
                    'sale_id' => $sale->id,
                    'item_id' => $orderItem->item_id,
                    'quantity' => $orderItem->quantity,
                    'cost' => $orderItem->cost,
                    'price' => $orderItem->price,
                    'total' => $orderItem->line_total,
                ]);
            }
       
            // log sale completion
            activity()
                ->causedBy(Auth::user())
                ->performedOn($sale)
                ->withProperties(['sale_id' => $sale->id])
                ->log('Sale completed with ID: ' . $sale->id);

            DB::commit();
            return SaleResource::make($sale)->additional($this->preparedResponse('store'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 400, null);
        }
        
    }
}
