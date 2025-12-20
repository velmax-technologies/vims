<?php

namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FilterItemStock implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->whereHas('stocks', function (Builder $query) use ($value) {
            $query->where('available_quantity', '>', $value);
        });
    }
}
