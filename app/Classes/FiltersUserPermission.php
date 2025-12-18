<?php

namespace App\Classes;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersUserPermission implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $query->whereHas('permissions', function (Builder $query) use ($value) {
            $query->where('name', $value);
        });
    }
}
