<?php

namespace Modules\File\Http\Controllers;

use Maatwebsite\Excel\Row;
use App\Imports\ItemImport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\ApiResponseFormatTrait;
use Modules\File\Transformers\FileImportResource;

class ExcelController extends Controller
{
    use ApiResponseFormatTrait;
    /**
     * Display a listing of the resource.
     */
     public function import_items() {
        
        Excel::import(new ItemImport, 'storage/uploads/items.xlsx') ;

        return new FileImportResource(null);
    }

    public function import_menu() {
        
        Excel::import(new ItemImport, 'storage/uploads/menu.xlsx') ;

        return new FileImportResource(null);
    }

    
}
