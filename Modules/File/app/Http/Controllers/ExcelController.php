<?php

namespace Modules\File\Http\Controllers;

use Maatwebsite\Excel\Row;
use App\Imports\ItemImport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function import(Row $row) {
        
        Excel::import(new ItemImport, 'storage/uploads/items.xlsx') ;
    }

    
}
