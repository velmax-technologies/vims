<?php

namespace Modules\Sale\Services;

use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class CompleteSaleService
{
    public function completeSale(Array $requestData, Sale $sale): bool {

        $sale->update($requestData);

        // log sale completion
        activity()
            ->causedBy(Auth::user())
            ->performedOn($sale)
            ->withProperties(['sale_id' => $sale->id])
            ->log('Sale completed with ID: ' . $sale->id);

        return true;
    }
}
