<?php

namespace Modules\Sale\Services;

use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

class SaleUpdateService
{
    public function update(Array $requestData, Sale $sale) {

        try{
            DB::beginTransaction();

            $sale->update($requestData);

            // update sale items if provided
            if (isset($requestData['sale_items'])) {

                $sale->sale_items()->delete();
                foreach ($requestData['sale_items'] as $itemData) {
                    $sale->sale_items()->create($itemData);
                }
                

                // delete existing item sales
                $sale->item_sales()->delete();  
                // create new item sales
                foreach ($requestData['sale_items'] as $itemData) {
                    $sale->item_sales()->create($itemData);
                }
            }


            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }

        return true;

    }
}
