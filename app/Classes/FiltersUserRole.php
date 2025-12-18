<?php

namespace App\Classes;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersUserRole implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->whereHas('roles', function (Builder $query) use ($value) {
            $query->where('name', $value);
        });
    }
}
