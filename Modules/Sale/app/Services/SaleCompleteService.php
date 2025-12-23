<?php

namespace Modules\Sale\Services;

use App\Models\Sale;
use App\Models\ItemSale;
use Illuminate\Support\Facades\Auth;

class SaleCompleteService
{
    public function complete(Array $requestData, Sale $sale) {
        $sale->update($requestData);

        // create item sales records
        foreach ($sale->sale_items as $saleItem) {
            $itemSaleData = [
                'sale_id' => $sale->id,
                'item_id' => $saleItem->item_id,
                'quantity' => $saleItem->quantity,
                'cost' => $saleItem->cost,
                'price' => $saleItem->price,
                'total' => $saleItem->line_total,
            ];
            ItemSale::create($itemSaleData);
        }

        // log sale completion
        activity()
            ->causedBy(Auth::user())
            ->performedOn($sale)
            ->withProperties(['sale_id' => $sale->id])
            ->log('Sale completed with ID: ' . $sale->id);

        return true;
    }
}
