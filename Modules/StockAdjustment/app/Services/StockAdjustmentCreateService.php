<?php

namespace Modules\StockAdjustment\Services;

use App\Models\Stock;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class StockAdjustmentCreateService
{
    public function create(Request $request) {

        try {
            DB::beginTransaction();
                    
            $stockAdjustment = StockAdjustment::create($request->all());

            // adjust stock
           
            if ($stockAdjustment->type === 'correction') {
                $stock = Stock::find($stockAdjustment->model_id);
                $stockDiff = $stockAdjustment->quantity - $stock->quantity;
                $stock->quantity = $stockAdjustment->quantity;
                $stock->available_quantity += $stockDiff;
                $stock->save();
            } elseif ($stockAdjustment->type === 'addition') {
                Stock::create([
                    'item_id' => $stockAdjustment->item_id,
                    'quantity' => $stockAdjustment->quantity,
                    'available_quantity' => $stockAdjustment->quantity,
                ]);
                //$stock->quantity += $stockAdjustment->quantity;
            } elseif ($stockAdjustment->type === 'subtraction') {
                
                $stock = Stock::find($stockAdjustment->model_id);
                // check available stock
                if ($stock->available_quantity < $stockAdjustment->quantity) {
                    return $this->errorResponse('Insufficient available stock for subtraction.', 400, null);
                }
                $stock->quantity -= $stockAdjustment->quantity;
                $stock->available_quantity -= $stockAdjustment->quantity;
                $stock->save();
            }

            DB::commit();
            return $stockAdjustment;
        } catch (\Exception $e) {
            throw $e;
        }

    }
}
