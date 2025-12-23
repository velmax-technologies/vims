<?php

namespace Modules\Menu\Services;

use App\Models\Item;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuUpdateService
{
    public function update(Request $requestData, Item $item)
    {
       
        try {
        // Update menu basic details
            DB::beginTransaction();

            $menu = $item->menu;
            if (!$menu) {
                // If menu doesn't exist, create a new one
                $menu = Menu::create(['item_id' => $item->id]);
            }

            // Sync menu items
            $menuItemsData = $requestData->input('menu_items', []);
            $menu->menu_items()->delete(); // Remove existing items
            $menu->menu_items()->createMany($menuItemsData); // Add new items
            DB::commit();
            return $menu;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // Rethrow the exception for higher-level handling
        }

        return $menu;
    }
}
