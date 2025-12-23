<?php

namespace Modules\Sale\Services;

use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Modules\StockAdjustment\Services\StockAdjustmentService;

class SaleReturnService
{
    public function returnSale(Array $requestData, Sale $sale) {

        try{
            DB::beginTransaction();

            $sale->update($requestData);

            // Adjust stocks back for each sale item
            foreach ($sale->sale_items as $saleItem) {
                if($saleItem->item->is_kitchen_menu){
                    foreach($saleItem->item->menu->menu_items as $menu_item){
                        $requiredQuantity = $saleItem->quantity * $menu_item->quantity;

                        $data = [
                            'type' => 'addition',
                            'item_id' => $menu_item->item_id,
                            'model' => 'sale',
                            'model_id' => $sale->id,
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
                        'item_id' => $saleItem->item_id,
                        'model' => 'sale',
                        'model_id' => $sale->id,
                        'quantity' => $saleItem->quantity,
                        'reason' => $requestData['status'],
                        'adjusted_at' => now(),
                    ];

                     (new StockAdjustmentService())->adjust($data);
                }
               
            }

        // delete associated item sales records
        $sale->item_sales()->delete();

        // log sale return
        activity()
            ->causedBy(auth()->user())
            ->performedOn($sale)
            ->withProperties(['sale_id' => $sale->id])
            ->log('Sale ' . $requestData['status'] . ' with ID: ' . $sale->id);

            DB::commit();

    } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    

        return true;
    }
}
