<?php

namespace Modules\StockAdjustment\Services;

use App\Models\Stock;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentService
{
    public function adjust(array $adjustmentData) {
        try{
            DB::beginTransaction();
            $sa = StockAdjustment::create($adjustmentData);
            $stocks = $sa->item->stocks;
            $quantity = $sa->quantity;  
            if($sa->type == 'subtraction' && ($sa->model == 'sale' || $sa->model == 'order')){                
                foreach($stocks as $stock){ 
                    if($stock->available_quantity >= $quantity){
                        $stock->available_quantity -= $quantity;
                        $stock->save();
                    }
                    else{
                        $quantity -= $stock->available_quantity;
                        $stock->available_quantity = 0;
                        $stock->save();
                    }
                }
            }
            elseif($sa->type == 'addition' && ($sa->model == 'sale' || $sa->model == 'order')){
                $stocks = Stock::where('item_id', $sa->item_id)->orderBy('created_at', 'desc')->get();
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
            }
            elseif($sa->type == 'addition' || $sa->type == 'purchase'){

            }
            elseif($sa->type == 'subtraction' || $sa->type == 'damaged' || $sa->type == 'consumed' || $sa->type == 'expired'){

            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        
    }                   
}
