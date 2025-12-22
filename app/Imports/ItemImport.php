<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;
use Modules\Item\Services\ItemCreateService;

class ItemImport implements ToModel, WithSkipDuplicates, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // $createItemService = new \Modules\Item\Services\CreateItemService();
        // $createItemService->create($row);

        (new ItemCreateService())->create($row);
    }
}
