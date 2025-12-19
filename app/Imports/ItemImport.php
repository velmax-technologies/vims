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
use Modules\StockAdjustment\Services\StockAdjustmentService;

class ItemImport implements ToModel, WithSkipDuplicates, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $createItemService = new \Modules\Item\Services\CreateItemService();
        $createItemService->create($row);
    }
}
