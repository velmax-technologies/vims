<?php

namespace Modules\Report\Http\Controllers;

use App\Models\Sale;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseFormatTrait;
use Modules\Report\Http\Requests\ReportRequest;
use Modules\Report\Transformers\ReportResource;

class ReportController extends Controller
{

    use ApiResponseFormatTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('report::index');
    }

    
    /**
     * Store a newly created resource in storage.
     */
    public function store(ReportRequest $request) {
        try {
            $request->validated();
            
            DB::beginTransaction();

            // sales
            $sales = Sale::with('sale_items')->get();

            $report = Report::create([
                'title' => $request->title,
                'description' => $request->description,
                'report_date' => $request->report_date,
                'report_data' => $sales
            ]);

            DB::commit();

            return ReportResource::make($report)
                ->additional($this->preparedResponse('store'));

           
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponseMessage(0, $e->getMessage(), 500);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('report::show');
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
