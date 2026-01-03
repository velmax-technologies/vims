<?php

namespace Modules\Sale\Http\Controllers;

use App\Models\ItemSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use App\Traits\ApiResponseFormatTrait;
use Modules\Sale\Transformers\ItemSaleResource;

class ItemSalesController extends Controller
{
    use ApiResponseFormatTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $itemSales = DB::table('item_sales')
        // ->select(
        //     'item_id',
        //     'cost',
        //     'price',
        //     DB::raw('SUM(quantity) as qty'), // The sum
            
        //     DB::raw('SUM(total) as total') // The sum
        // )
        // ->groupBy('item_id', 'cost', 'price') // Group by all non-aggregated columns
        // ->get();

        // return $itemSales;

        // return ItemSaleResource::collection($itemSales)
        //     ->additional($this->preparedResponse('index'));

        $itemSales = QueryBuilder::for(ItemSale::class)
        ->allowedFilters(['item.name','sale.shift_id'])
        ->get();

        return ItemSaleResource::collection($itemSales)
            ->additional($this->preparedResponse('index'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sale::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('sale::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('sale::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
