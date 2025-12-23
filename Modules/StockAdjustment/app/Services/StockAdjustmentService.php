<?php

namespace Modules\StockAdjustment\Services;

use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentService
{
    public function adjust(array $adjustmentData) {
        try{
            DB::beginTransaction();
            $sa = StockAdjustment::create($adjustmentData);
            
            if($sa->type == 'sale'){
                $stocks = $sa->item->stocks;
                       
                foreach($stocks as $stock){ 
                    if($stock->available_quantity >= $sa->quantity){
                        $stock->available_quantity -= $sa->quantity;
                        $stock->save();
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        
    }                   
}
