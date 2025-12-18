<?php

namespace App\Filters;
use Illuminate\Database\Eloquent\Builder;


class SaleItemFilter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->whereHas('item', function (Builder $query) use ($value) {
            $query->where('name', $value);
        });
    }
}
