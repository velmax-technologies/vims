<?php

namespace Modules\Sale\Services;

use App\Models\Sale;
use Symfony\Component\HttpFoundation\Request;

class UpdateSaleService
{
    public function update(Array $requestData, Sale $sale) {

        $sale->update($requestData);

        // update sale items if provided
        if (isset($requestData['sale_items'])) {
            // delete existing sale items
            $sale->item_sales()->delete();  
            // create new sale items
            foreach ($requestData['sale_items'] as $itemData) {
                $sale->item_sales()->create($itemData);
            }
        }

        return true;

    }
}
