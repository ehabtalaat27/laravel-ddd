<?php

namespace App\Shared\Base;

use App\Exceptions\CantDeleteModelException;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    // Add constants for magic strings
    protected const ORDER_BY = 'id';
    protected const ORDER_DIR = 'desc';
    protected const LIMIT = 15;

    protected Model $model;
    protected string $modelName;
    protected Builder $query;

    /**
     * BaseRepository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->query = $model->newQuery();
        $this->modelName = class_basename($this->model);
    }

    /**
     * Reset query builder to fresh state
     */
    public function freshRepo(): static
    {
        $this->query = $this->model->newQuery();
        return $this;
    }

    /**
     * Create a new model instance
     * @param array $attributes
     * @return Model|false
     */
    public function create(array $attributes = []): mixed
    {
        if (empty($attributes)) {
            return false;
        }

        $filtered = $this->cleanUpAttributes($attributes);
        return $this->query->create($filtered);
    }

    /**
     * Update an existing model
     * @param Model $model
     * @param array $attributes
     * @return Model|false
     */
    public function update(Model $model, array $attributes = []): mixed
    {
        if (empty($attributes)) {
            return false;
        }

        $filtered = $this->cleanUpAttributes($attributes);
        $model->update($filtered);
        
        return $model->fresh();
    }

    /**
     * Attach relationship records
     */
    public function attach(Model $model, string $relation, array $attributes = []): mixed
    {
        if (empty($attributes)) {
            return false;
        }
        return $model->{$relation}()->attach($attributes);
    }

    /**
     * Detach relationship records
     */
    public function detach(Model $model, string $relation, array $attributes = []): mixed
    {
        if (empty($attributes)) {
            return false;
        }
        return $model->{$relation}()->detach($attributes);
    }

    /**
     * Sync relationship records
     */
    public function sync(Model $model, string $relation, array $attributes = []): mixed
    {
        if (empty($attributes)) {
            return false;
        }
        return $model->{$relation}()->sync($attributes);
    }

    /**
     * Update all records matching current query
     */
    public function updateAll(array $attributes = []): mixed
    {
        if (empty($attributes)) {
            return false;
        }

        $filtered = $this->cleanUpAttributes($attributes);
        return $this->query->update($filtered);
    }

    /**
     * Update records by key values
     */
    public function updateAllByKey($key, array $values = [], array $attributes = []): int|bool
    {
        if (empty($attributes) || empty($values)) {
            return false;
        }

        $filtered = $this->cleanUpAttributes($attributes);
        return $this->query->whereIn($key, $values)->update($filtered);
    }

    /**
     * Create or update a model
     */
    public function createOrUpdate(array $attributes = [], $id = null): mixed
    {
        if (empty($attributes)) {
            return false;
        }

        $filtered = $this->cleanUpAttributes($attributes);
        
        if ($id) {
            $model = $this->query->find($id);
            if ($model) {
                return $this->update($model, $filtered);
            }
        }
        
        return $this->create($filtered);
    }

    /**
     * Delete a model with relationship validation
     * @throws CantDeleteModelException
     */
    public function remove(Model $model, bool $force = false): ?bool
    {
        if (!$force && method_exists($model, 'getDefinedRelations')) {
            foreach ($model->getDefinedRelations() as $relation) {
                if ($model->$relation()->exists()) {
                    throw new CantDeleteModelException(
                        __("Can't delete this :model, it has :relation. Please remove them first.", [
                            'model' => $this->modelName,
                            'relation' => $relation
                        ])
                    );
                }
            }
        }

        return $model->delete();
    }

    /**
     * Apply dynamic filters from model
     */
    public function withFilters($query, array $filters = []): Builder
    {
        if (empty($filters) || !method_exists($this->model, 'getFilters')) {
            return $query;
        }

        foreach ($this->model->getFilters() as $filter) {
            if (isset($filters[$filter])) {
                $method = "of" . ucfirst($filter);
                if (method_exists($query->getModel(), 'scope' . ucfirst($method))) {
                    $query = $query->$method($filters[$filter]);
                }
            }
        }
        
        return $query;
    }

    /**
     * Get first record
     */
    public function first(): ?object
    {
        return $this->query->first();
    }

    /**
     * Check if records exist
     */
    public function exists(): bool
    {
        return $this->query->exists();
    }

    /**
     * Find multiple records by IDs
     */
    public function findIds($ids): mixed
    {
        return $this->query->findOrFail($ids);
    }

    /**
     * Find a record by ID
     */
    public function find(int $id, array $relations = [], array $filters = []): mixed
    {
        $query = $this->applyRelations($this->query, $relations);
        return $this->withFilters($query, $filters)->find($id);
    }

    /**
     * Find a record by ID or fail
     */
    public function findOrFail(int $id, array $relations = [], array $filters = []): mixed
    {
        $query = $this->applyRelations($this->query, $relations);
        return $this->withFilters($query, $filters)->findOrFail($id);
    }

    /**
     * Find by specific field
     */
    public function findBy(string $key, mixed $value, bool $fail = true): mixed
    {
        $query = $this->query->where($key, $value);
        return $fail ? $query->firstOrFail() : $query->first();
    }

    /**
     * Find by multiple fields with AND/OR logic
     */
    public function findByFields(array $fields): mixed
    {
        $query = $this->query;
        
        if (isset($fields['and'])) {
            $query = $query->where($fields['and']);
        }
        
        if (isset($fields['or'])) {
            $query = $query->where(function (Builder $q) use ($fields) {
                foreach ($fields['or'] as $condition) {
                    $q->orWhere($condition[0], $condition[1] ?? '=', $condition[2] ?? null);
                }
            });
        }
        
        return $query->first();
    }

    /**
     * First or create record
     */
    public function whereOrCreate(array $wheres, array $data = null): mixed
    {
        return $this->query->firstOrCreate($wheres, $data ?? []);
    }

    /**
     * Apply various query conditions
     */
    protected function applyConditions(Builder $query, array $conditions): Builder
    {
        if (empty($conditions)) {
            return $query;
        }

        $conditionMethods = [
            'where' => fn($q, $field, $value) => $q->where($field, $value),
            'whereNot' => fn($q, $field, $value) => $q->where($field, '!=', $value),
            'whereDateLess' => fn($q, $field, $value) => $q->whereDate($field, '<=', Carbon::parse($value)),
            'whereDateMore' => fn($q, $field, $value) => $q->whereDate($field, '>=', Carbon::parse($value)),
            'whereIn' => fn($q, $field, $value) => $q->whereIn($field, $value),
            'whereNotIn' => fn($q, $field, $value) => $q->whereNotIn($field, $value),
            'whereLike' => fn($q, $field, $value) => $q->where($field, 'like', '%' . $value . '%'),
            'whereBetween' => fn($q, $field, $value) => $q->whereBetween($field, $value),
        ];

        foreach ($conditions as $type => $items) {
            if (isset($conditionMethods[$type]) && !empty($items)) {
                foreach ($items as $field => $value) {
                    $query = $conditionMethods[$type]($query, $field, $value);
                }
            }
        }

        return $query;
    }

    /**
     * Apply eager loading relations
     */
    protected function applyRelations(Builder $query, array $relations): Builder
    {
        return empty($relations) ? $query : $query->with($relations);
    }

    /**
     * Get records for form select dropdown
     */
    public function findAllForFormSelect(
        string $labelField = null,
        string $valueField = 'id',
        bool   $applyOrder = false,
        string $orderBy = self::ORDER_BY,
        string $orderDir = self::ORDER_DIR,
        array  $conditions = []
    ): mixed
    {
        $query = $this->query;
        
        if ($applyOrder) {
            $query = $query->orderBy($orderBy, $orderDir);
        }
        
        $query = $this->applyConditions($query, $conditions);
        
        return $query->pluck($labelField ?? $valueField, $valueField);
    }

    /**
     * Get all records
     */
    public function findAll(
        array $fields = ['*'],
        bool $applyOrder = true,
        string $orderBy = self::ORDER_BY,
        string $orderDir = self::ORDER_DIR
    ): mixed
    {
        $query = $this->query;
        
        if ($applyOrder) {
            $query = $query->orderBy($orderBy, $orderDir);
        }
        
        return $query->get($fields);
    }

    /**
     * Base search with filters and relations
     */
    public function baseSearch(
         $query,
        array $filters = [],
        array $relations = [],
        array $data = []
    ): Builder
    {
        $query = $this->applyRelations($query, $relations);
        return $this->withFilters($query, $filters);
    }

    /**
     * Search records
     */
    public function search(array $filters = [], array $relations = [], array $data = []): mixed
    {
        $query = $this->baseSearch($this->query, $filters, $relations, $data);
        return $this->getQueryResult($query, $data);
    }

    /**
     * Search including trashed records
     */
    public function searchWithTrashed(array $filters = [], array $relations = [], array $data = []): mixed
    {
        $query = $this->baseSearch($this->query->withTrashed(), $filters, $relations, $data);
        return $this->getQueryResult($query, $data);
    }

    /**
     * Execute query with pagination/ordering
     */
    public function getQueryResult($query, array $data = []): mixed
    {
        $page = $data['page'] ?? true;
        $limit = $data['limit'] ?? self::LIMIT;
        $order = $data['order'] ?? [];
        $groupBy = $data['groupBy'] ?? null;

        // Apply ordering
        if (!empty($order)) {
            foreach ($order as $orderBy => $orderDir) {
                $query = $query->orderBy($orderBy, $orderDir);
            }
        } else {
            $query = $query->latest();
        }

        // Group by
        if ($groupBy) {
            return $query->get()->groupBy($groupBy);
        }

        // Custom pagination URI
        if (!empty($data['customizePaginationUri']) && !empty($data['paginationUri'])) {
            return $query->paginate($limit)->withPath($data['paginationUri']);
        }

        // Standard pagination
        if ($page) {
            return $query->paginate($limit);
        }

        // Limited results
        if ($limit) {
            return $query->limit($limit)->get();
        }

        return $query->get();
    }

    /**
     * Filter attributes to only fillable fields
     */
    protected function cleanUpAttributes(array $attributes): array
    {
        return collect($attributes)
            ->filter(fn($value, $key) => $this->model->isFillable($key))
            ->toArray();
    }

    /**
     * Manual pagination from array/collection
     */
    public function paginate(
        array|Collection $items,
        int $perPage = 15,
        int $page = null,
        array $options = []
    ): LengthAwarePaginator
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        
        return new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options
        );
    }

    /**
     * Create related model
     */
    public function relationCreate(Model $model, string $relation, array $attributes = []): mixed
    {
        if (empty($attributes)) {
            return false;
        }
        return $model->{$relation}()->create($attributes);
    }

    /**
     * Toggle boolean field
     */
    public function toggleField($model, string $field): mixed
    {
        $newValue = $model[$field] == 1 ? 0 : 1;
        return $this->update($model, [$field => $newValue]);
    }
      public function getAllWithSelectOrdered(
        array $columns = ['id', 'name'],
        array $filters = [],
        array $conditions = [],
        string $orderBy = self::ORDER_BY,
        string $orderDir = self::ORDER_DIR
    ): Collection
    {
        $query = $this->query->select($columns);
        $query = $this->withFilters($query, $filters);
        $query = $this->applyConditions($query, $conditions);
        
        return $query->orderBy($orderBy, $orderDir)->get();
    }
}