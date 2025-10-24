<?php

namespace App\Traits;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Foundation\Application;
use ReflectionClass;

trait ModelTrait
{
    public function __construct()
    {
        parent::__construct();

    }
    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters ?? [];
    }

    public function getDefinedRelations()
    {
        return $this->definedRelations ?? [];
    }
public function scopeOfKeyword($query, $keyword)
{
    $columns = $this->searchable ?? [];

    if (empty($keyword) || empty($columns)) {
        return $query;
    }

   return $query->where(function ($q) use ($columns, $keyword) {
        foreach ($columns as $column) {
            // Check if it's a relationship column (contains a dot)
            if (str_contains($column, '.')) {
                [$relation, $relatedColumn] = explode('.', $column, 2);
                
                $q->orWhereHas($relation, function ($relationQuery) use ($relatedColumn, $keyword) {
                    $relationQuery->whereRaw("LOWER($relatedColumn) LIKE ?", ['%' . strtolower($keyword) . '%']);
                });
            } else {
                // Regular column search
                $q->orWhereRaw("LOWER($column) LIKE ?", ['%' . strtolower($keyword) . '%']);
            }
        }
    });
}

}