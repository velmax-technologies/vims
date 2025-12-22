<?php

namespace Modules\Menu\Services;

use App\Models\Item;
use Illuminate\Support\Facades\DB;

class MenuDeleteService
{
    public function delete(Item $item): Item
    {
        
        try {
            DB::beginTransaction();
            // Delete associated menu items
            //$item->menu_items()->delete();
            // Delete the menu item itself
            $item->delete();
            DB::commit();
            return $item;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; // Rethrow the exception for higher-level handling
        }
        return $item;
    }
}
