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
                //$sa->item->stocks;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        
    }                   
}
