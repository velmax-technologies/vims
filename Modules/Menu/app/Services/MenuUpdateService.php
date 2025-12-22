<?php

namespace Modules\Menu\Services;

use App\Models\Item;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuUpdateService
{
    public function update(Request $requestData, Item $item): Item
    {
       
        try {
        // Update menu basic details
            DB::beginTransaction();
            $item->update($requestData->only(['name', 'description']));
            // Sync menu items
            $menuItemsData = $requestData->input('menu_items', []);
            $item->menu_items()->delete(); // Remove existing items
            $item->menu_items()->createMany($menuItemsData); // Add new items
            DB::commit();
            return $item;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // Rethrow the exception for higher-level handling
        }

        return $item;
    }
}
