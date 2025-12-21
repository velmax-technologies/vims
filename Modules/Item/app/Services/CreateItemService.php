<?php

namespace Modules\Item\Services;

use App\Models\Item;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\StockAdjustment\Services\StockAdjustmentService;

class CreateItemService
{
    public function create(Array $data):bool {
        return DB::transaction(function () use ($data) {
            // if unit is set
            if (isset($data['unit']) && !empty($data['unit'])) {
                $unit = Unit::whereName($data['unit'])->first();
                if ($unit) {
                    $data['unit_id'] = $unit->id;
                }
            }

            // create or update the item 
            $item = Item::firstOrCreate([
                'name' => $data['item'],
                'alias' => $data['alias'] ?? null,
                //'quantity' => $data['quantity'] ?? 0,
                'description' => $data['description'] ?? null,
                'sku' => $data['sku'] ?? null,
                'upc' => $data['upc'] ?? null,
                'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : true,
                'unit_id' => $data['unit_id'] ?? null,
            ]);

            $tags = explode(',', $data['tags'] ?? '');
            $tags = array_map('trim', $tags); // Trim whitespace from each tag
            
            // Ensure tags are unique and not empty
            $tags = array_filter(array_unique($tags), function ($tag) {
                return !empty($tag);
            });

            // Create tags if they don't exist
            foreach ($tags as $tag) {
                $item->attachTag($tag, 'itemCategoryTag'); // Replace 'type' with your desired tag type
            }

            $stock_note = $data['note'] ?? 'initial stock';

            // update or create related stocks
            $item->stocks()->updateOrCreate(
                [
                    'item_id' => $item->id, 
                    'note' => $stock_note,
                ], 

                [
                    'quantity' => $data['quantity'] ?? 0,
                    'available_quantity' => $data['quantity'] ?? 0,
                    'expiry_date' => $data['expiry_date'] ?? null,
                    'is_expired' => isset($data['is_expired']) ? (bool)$data['is_expired'] : false,
                ]
            );

            // stock adjustment
            $adjustmentData = [
                'item_id' => $item->id,
                'quantity' => $data['quantity'] ?? 0,
                'note' => $stock_note,
                'type' => 'addition',
                'adjusted_at' => now(),
            ];


            (new StockAdjustmentService())->adjust($adjustmentData);


            // update or create related item costs
            $item->costs()->updateOrCreate(
                [
                    'stock_id' => $item->stocks->where('note', $stock_note)->first()->id,
                ],
                [
                    'cost' => $data['cost'] ?? 0.00,
                ]
            );

            
 
            if (isset($data['wholesale']) && !empty($data['wholesale'])) {
                $priceTag = 'wholesale';

                $itemPrice = $item->item_prices()->updateOrCreate(
                    [
                        'price' => $data['wholesale'],
                    ]
                );

               if(!$itemPrice->hasTag($priceTag, 'priceTag')){
                    $itemPrice->attachTag($priceTag, 'priceTag');
                }
               }
                
            // retail price tag
            if (isset($data['retail']) && !empty($data['retail'])) {
                $priceTag = 'retail';

                $itemPrice = $item->item_prices()->updateOrCreate(
                    [
                        'price' => $data['retail'],
                    ]
                );

                if(!$itemPrice->hasTag($priceTag, 'priceTag')){
                    $itemPrice->attachTag($priceTag, 'priceTag');
                }
            }               

            
            // log
            activity()
                ->performedOn($item)
                ->causedBy(Auth::user())
                ->withProperties(['action' => 'create'])
                ->log('Item(s) created/updated');

            return true;
        });
    }
}
