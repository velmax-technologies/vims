<?php

namespace App\Imports;

use App\Models\Item;
use Spatie\Tags\Tag;
use App\Models\ItemPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;

class ItemImport implements ToModel, WithSkipDuplicates, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {

            // create or update the item 
            $item = Item::firstOrCreate([
                'name' => $row['item'],
                'alias' => $row['alias'] ?? null,
            ]);

            $tags = explode(',', $row['tags'] ?? '');
            $tags = array_map('trim', $tags); // Trim whitespace from each tag
            
            // Ensure tags are unique and not empty
            $tags = array_filter(array_unique($tags), function ($tag) {
                return !empty($tag);
            });

            // Create tags if they don't exist
            foreach ($tags as $tag) {
                $item->attachTag($tag, 'itemCategoryTag'); // Replace 'type' with your desired tag type
            }

            $stock_note = $row['note'] ?? 'initial stock - ' . now()->toDateString();

            // update or create related stocks
            $item->stocks()->updateOrCreate(
                
                [
                    'item_id' => $item->id, 
                    'note' => $stock_note,
                ], 

                [
                    'quantity' => $row['qty'] ?? 0,
                    'expiry_date' => $row['expiry_date'] ?? null,
                    'is_expired' => isset($row['is_expired']) ? (bool)$row['is_expired'] : false,
                ]
            );

            // update or create related item costs
            $item->costs()->updateOrCreate(
                [
                    'stock_id' => $item->stocks->where('note', $stock_note)->first()->id,
                ],
                [
                    'cost' => $row['cost'] ?? 0.00,
                ]
            );

            
 
            if (isset($row['wholesale']) && !empty($row['wholesale'])) {
                $priceTag = 'wholesale';

                $itemPrice = $item->item_prices()->updateOrCreate(
                    [
                        'price' => $row['wholesale'],
                    ]
                );

               if(!$itemPrice->hasTag($priceTag, 'priceTag')){
                    $itemPrice->attachTag($priceTag, 'priceTag');
                }
               }
                
            // retail price tag
            if (isset($row['retail']) && !empty($row['retail'])) {
                $priceTag = 'retail';

                $itemPrice = $item->item_prices()->updateOrCreate(
                    [
                        'price' => $row['retail'],
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
                ->withProperties(['action' => 'import'])
                ->log('Item(s) imported or updated');

            return $item;
        });
    }
}
