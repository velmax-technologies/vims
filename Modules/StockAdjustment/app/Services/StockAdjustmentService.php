<?php

namespace Modules\StockAdjustment\Services;

use App\Models\StockAdjustment;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentService
{
    public function adjust(array $adjustmentData) {
        StockAdjustment::create($adjustmentData);
    }
}
