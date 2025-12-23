<?php

namespace Modules\Sale\Services;

use App\Models\Item;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;
use Modules\StockAdjustment\Services\StockAdjustmentService;

class SaleUpdateService
{
    public function update(Array $requestData, Sale $sale) {

        try{
            
            DB::beginTransaction();

            $sale->update($requestData);

            // update sale items if provided
            if (isset($requestData['sale_items'])) {

                

                // delete sale items
                $sale->sale_items()->delete();               

                // delete sale item sales
                //$sale->item_sales()->delete();

                // delete sale stock adjustments
                $stockAdjustments = StockAdjustment::where([['model', 'sale'],['model_id', $sale->id]])->get();

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

                // create sale items
                foreach ($requestData['sale_items'] as $itemData) {
                    $sale->sale_items()->create($itemData);
                }
                
                // create new item sales
                // foreach ($requestData['sale_items'] as $itemData) {
                //     $sale->item_sales()->create($itemData);
                // }

                // create stock adjustments
                // stock adjustment for each sale item
            foreach ($sale->sale_items as $sale_item) {
                 
                if($sale_item->item->is_kitchen_menu) {
                    
                    // for kitchen menu, adjust stock for each component
                    foreach ($sale_item->item->menu->menu_items as $menuItem) {

                        
                       
                        $requiredQuantity = $menuItem->quantity * $sale_item['quantity'];
                       
                        // Create stock adjustment
                        $data = [
                            'type' => 'subtraction',
                            'item_id' => $menuItem->item_id,
                            'quantity' => $requiredQuantity,
                            'model' => 'sale',
                            'model_id' => $sale->id,
                            'reason' => 'sale',
                            'adjusted_at' => now(),
                        ];

                        (new StockAdjustmentService())->adjust($data);

                    }
                } else {
                    // for regular items, adjust stock directly
                    $data = [
                        'type' => 'subtraction',
                        'item_id' => $sale_item->item_id,
                        'quantity' => $sale_item->quantity,
                        'model' => 'sale',
                        'model_id' => $sale->id,
                        'reason' => 'sale',
                        'adjusted_at' => now(),
                    ];

                    (new StockAdjustmentService())->adjust($data);
                }
            }

            // log sale creation
            activity()
                ->causedBy(Auth::user())
                ->performedOn($sale)
                ->withProperties(['sale_id' => $sale->id])
                ->log('Sale updated with ID: ' . $sale->id);
            }


            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }

        return true;

    }
}
