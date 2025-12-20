<?php

namespace Modules\Sale\Services;

use App\Models\Sale;
use App\Models\Stock;

class ReturnSaleService
{
    public function returnSale(Array $requestData, Sale $sale) {

        $sale->update($requestData);

        // Adjust stocks back for each sale item
        foreach ($sale->sale_items as $saleItem) {
            $quantity = $saleItem->quantity;
            foreach (Stock::where('item_id', $saleItem->item_id)->orderBy('created_at', 'desc')->get() as $stock) {
                if ($quantity <= 0) {
                    break;
                }

                $soldStockQuantity = $stock->quantity - $stock->available_quantity;
                if ($soldStockQuantity >= $quantity) {
                    $stock->available_quantity += $quantity;
                    $stock->save();
                    $quantity = 0;
                } else {
                    $stock->available_quantity += $soldStockQuantity;
                    $stock->save();
                    $quantity -= $soldStockQuantity;    
                }

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

        return true;
    }
}
