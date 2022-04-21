<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Gets and returns the columns to be used for filtering.
     *
     * @return array
     */
    public function getFilterableColumns(): array
    {
        return $this->filterableColumns ?? [];
    }

    /**
     * Gets and returns the columns to be used for searching.
     *
     * @return array
     */
    public function getSearchableColumns(): array
    {
        return $this->searchableColumns ?? [];
    }

    /**
     * Adds additional where clauses to filter through the data using the model's filterable columns.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeFilter(Builder $query): void
    {
        $columns = $this->getFilterableColumns();

        foreach ($columns as $column) {
            if (request()->has($column)) {
                $query->where($column, request($column));
            }
        }
    }

    /**
     * Adds additional where clauses to search through the data using the model's searchable columns.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeSearch(Builder $query): void
    {
        $columns = $this->getSearchableColumns();
        $q = request('q');
        if (!$q) {
            return;
        }

        $query->where(function ($query) use ($columns, $q) {
            foreach ($columns as $column) {
                $query->orWhere($column, 'LIKE', "%{$q}%");
            }
        });
    }
}
