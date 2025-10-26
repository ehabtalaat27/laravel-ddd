<?php

namespace App\Shared\Base;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    public function freshRepo(): static;

    public function create(array $attributes = []): mixed;

    public function update(Model $model, array $attributes = []): mixed;

    public function createOrUpdate(array $attributes = [], $id = null): mixed;

    public function remove(Model $model, bool $force = false);

    public function attach(Model $model, string $relation, array $attributes = []): mixed;

    public function detach(Model $model, string $relation, array $attributes = []): mixed;

    public function sync(Model $model, string $relation, array $attributes = []): mixed;

    public function updateAll(array $attributes = []): mixed;

    public function updateAllByKey($key, array $values = [], array $attributes = []): int|bool;

    public function find(int $id, array $relations = [], array $filters = []): mixed;

    public function findOrFail(int $id, array $relations = [], array $filters = []): mixed;

    public function findBy(string $key, mixed $value, bool $fail = true): mixed;

    public function findByFields(array $fields): mixed;

    public function findIds($ids): mixed;

    public function first(): null|object;

    public function exists(): bool;

    public function whereOrCreate(array $wheres, array $data = null): mixed;

    public function withFilters($query, array $filters = []): Builder;

    public function findAll(array $fields = ['*'], bool $applyOrder = true, string $orderBy = 'id', string $orderDir = 'desc'): mixed;

    public function findAllForFormSelect(
        string $labelField = null,
        string $valueField = 'id',
        bool   $applyOrder = false,
        string $orderBy = 'id',
        string $orderDir = 'desc',
        array  $conditions = []
    ): mixed;

    public function baseSearch($query, array $filters = [], array $relations = [], array $data = []): mixed;

    public function search(array $filters = [], array $relations = [], array $data = []): mixed;

    public function searchWithTrashed(array $filters = [], array $relations = [], array $data = []): mixed;

    public function getQueryResult($query, array $data = []): mixed;

    public function paginate(array|Collection $items, int $perPage = 15, int $page = null, array $options = []): LengthAwarePaginator;

    public function relationCreate(Model $model, string $relation, array $attributes = []): mixed;

    public function toggleField($model, string $field): mixed;
    public function getAllWithSelectOrdered(
        array $columns = ['id', 'name'],
        array $filters = [],
        array $conditions = [],
        string $orderBy = 'id',
        string $orderDir = 'desc'
    ): Collection;
}
