<?php

namespace CuongNX\RepoServiceGenerator\Base;

use CuongNX\RepoServiceGenerator\Base\Contracts\BaseRepositoryInterface;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function getAll(array $relations = [], array|string|null $orderBy = null)
    {
        $query = $this->model;

        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        $query = $this->applyOrder($query, $orderBy);

        return $query->get();
    }

    public function get(array|null $fields = null, array $relations = [], array|string|null $orderBy = null)
    {
        $query = $this->model->select($this->normalizeFields($fields));

        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        $query = $this->applyOrder($query, $orderBy);

        return $query->get();
    }

    public function getBy(string $key, $value, array|null $fields = null, array $relations = [], array|string|null $orderBy = null)
    {
        $query = $this->model->select($this->normalizeFields($fields))->where($key, $value);

        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        $query = $this->applyOrder($query, $orderBy);

        return $query->get();
    }

    public function getByAttributes(array $conditions, array|null $fields = null, array $relations = [], array|string|null $orderBy = null)
    {
        $query = $this->applyConditions($this->model, $conditions);

        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        $query = $this->applyOrder($query, $orderBy);

        return $query->select($this->normalizeFields($fields))->get();
    }

    public function find($id, array|null $fields = null, array $relations = [], array|string|null $orderBy = null)
    {
        $query = $this->model->select($this->normalizeFields($fields));

        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        $query = $this->applyOrder($query, $orderBy);

        return $query->find($id);
    }

    public function findBy(string $key, $value, array|null $fields = null, array $relations = [], array|string|null $orderBy = null)
    {
        $query = $this->model->where($key, $value)->select($this->normalizeFields($fields));

        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        $query = $this->applyOrder($query, $orderBy);

        return $query->first();
    }

    public function findByAttributes(array $conditions, array|null $fields = null, array $relations = [], array|string|null $orderBy = null)
    {
        $query = $this->applyConditions($this->model, $conditions);

        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        $query = $this->applyOrder($query, $orderBy);

        return $query->select($this->normalizeFields($fields))->first();
    }

    public function pluck(string $column, ?string $key = null, array $conditions = [])
    {
        $query = $this->applyConditions($this->model, $conditions);
        return $key ? $query->pluck($column, $key) : $query->pluck($column);
    }

    public function countBy(array $conditions = []): int
    {
        $query = $this->applyConditions($this->model, $conditions);
        return $query->count();
    }

    public function sum(string $column, array $conditions = []): float|int
    {
        return $this->applyConditions($this->model, $conditions)->sum($column);
    }

    public function avg(string $column, array $conditions = []): ?float
    {
        return $this->applyConditions($this->model, $conditions)->avg($column);
    }

    public function max(string $column, array $conditions = []): float|int|null
    {
        return $this->applyConditions($this->model, $conditions)->max($column);
    }

    public function min(string $column, array $conditions = []): float|int|null
    {
        return $this->applyConditions($this->model, $conditions)->min($column);
    }

    public function increment(string $column, int $amount = 1, array $conditions = [], array $extra = []): int
    {
        return $this->applyConditions($this->model, $conditions)->increment($column, $amount, $extra);
    }

    public function decrement(string $column, int $amount = 1, array $conditions = [], array $extra = []): int
    {
        return $this->applyConditions($this->model, $conditions)->decrement($column, $amount, $extra);
    }

    public function chunk(int $count, callable $callback, array $conditions = []): bool
    {
        return $this->applyConditions($this->model, $conditions)->chunk($count, $callback);
    }

    public function existsBy(string $field, $value): bool
    {
        return $this->model->where($field, $value)->exists();
    }

    public function existsByAttributes(array $conditions): bool
    {
        $query = $this->applyConditions($this->model, $conditions);
        return $query->exists();
    }

    public function paginate(int $perPage = 15, array $conditions = [], array|null $fields = null, array $relations = [])
    {
        $query = $this->applyConditions($this->model, $conditions);

        if (!empty($relations)) {
            $query = $query->with($relations);
        }

        return $query->select($this->normalizeFields($fields))->paginate($perPage);
    }

    /**
     * Phân trang tùy chỉnh dựa trên page và limit
     *
     * @param array $conditions Conditions cho query (where)
     * @param array|null $fields Các fields cần select
     * @param array $relations Các relations cần eager load (with)
     * @param array|string|null $orderBy Order by (ví dụ: ['created_at' => 'desc'])
     * @param int $page Số trang (mặc định 1)
     * @param int $limit Số bản ghi mỗi trang (mặc định 10)
     * @return array Dữ liệu phân trang: ['data', 'current_page', 'per_page', 'total', 'last_page']
     */
    public function paginateCustom(
        array $conditions = [],
        ?array $fields = null,
        array $relations = [],
        array|string|null $orderBy = null,
        int $page = 1,
        int $limit = 10
    ): array {
        $query = $this->applyConditions($this->model, $conditions)
            ->select($this->normalizeFields($fields));

        if (!empty($relations)) {
            $query = $query->with($relations);
        }

        $query = $this->applyOrder($query, $orderBy);

        $total = $query->count();
        $offset = ($page - 1) * $limit;

        $data = $query->skip($offset)
            ->take($limit)
            ->get();

        $lastPage = ceil($total / $limit);

        return [
            'data' => $data,
            'current_page' => $page,
            'per_page' => $limit,
            'total' => $total,
            'last_page' => $lastPage,
        ];
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $item = $this->find($id);
        return $item && $item->update($data) ? $item : false;
    }

    public function updateFields($model, array $fields, array $except = []): mixed
    {
        $fillable = $model->getFillable();
        $before = clone $model;

        foreach ($fields as $key => $value) {
            if (in_array($key, $fillable) && !in_array($key, $except)) {
                $model->{$key} = $value;
            }
        }

        $dirtyFields = $model->getDirty();

        try {
            $success = $model->save();
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }

        return [
            'success' => $success,
            'model' => $model,
            'changed' => $dirtyFields,
            'old' => $before->only(array_keys($dirtyFields)),
        ];
    }

    /**
     * Create or update a model with given attributes and values.
     *
     * @param  array  $attributes
     * @param  array  $values
     * @param  array|null  $checkFields
     * @return array [Model $model, bool $wasCreated, bool $wasUpdated, array $changedFields]
     */
    public function createOrUpdate(array $attributes, array $values = [], array|null $checkFields = null): array
    {
        $model = $this->model->firstOrNew($attributes);
        $wasRecentlyCreated = !$model->exists;

        $model->fill($values);

        $dirty = $model->getDirty();

        $importantChanges = ($checkFields !== null && !empty($checkFields))
            ? array_intersect_key($dirty, array_flip($checkFields))
            : $dirty;

        $wasUpdated = !$wasRecentlyCreated && !empty($importantChanges);

        if ($wasRecentlyCreated || $wasUpdated) {
            $model->save();
        }

        return [$model, $wasRecentlyCreated, $wasUpdated, $importantChanges];
    }

    public function delete($id)
    {
        $item = $this->find($id);
        return $item ? $item->delete() : false;
    }

    public function deleteBy(array $conditions): int
    {
        return $this->model->where($conditions)->delete();
    }

    public function withTrashed(array $conditions = [], array|null $fields = null, array $relations = [])
    {
        $query = $this->applyConditions($this->model->withTrashed(), $conditions);

        if (!empty($relations)) {
            $query = $query->with($relations);
        }

        return $query->select($this->normalizeFields($fields))->get();
    }

    public function onlyTrashed(array $conditions = [], array|null $fields = null, array $relations = [])
    {
        $query = $this->applyConditions($this->model->onlyTrashed(), $conditions);

        if (!empty($relations)) {
            $query = $query->with($relations);
        }

        return $query->select($this->normalizeFields($fields))->get();
    }

    public function restore($id): bool
    {
        $item = $this->model->withTrashed()->find($id);
        return $item ? $item->restore() : false;
    }

    public function forceDelete($id): bool
    {
        $item = $this->model->withTrashed()->find($id);
        return $item ? $item->forceDelete() : false;
    }


    protected function applyConditions($query, array $conditions)
    {
        foreach ($conditions as $key => $value) {
            if (is_array($value) && isset($value['$elemMatch'])) {
                $query = $query->where($key, 'elemMatch', $value['$elemMatch']);
            } else {
                $query = $query->where($key, $value);
            }
        }

        return $query;
    }

    /**
     * @param array|string|null $orderBy  ['field' => 'asc'] or [['field', 'asc'], ['field2', 'desc']]
     */
    protected function applyOrder($query, array|string|null $orderBy)
    {
        if (empty($orderBy)) {
            return $query;
        }

        if (is_string($orderBy)) {
            // default asc
            $query->orderBy($orderBy, 'asc');
        } elseif (is_array($orderBy)) {
            foreach ($orderBy as $key => $direction) {
                if (is_numeric($key) && is_array($direction)) {
                    // [['field', 'desc']]
                    [$column, $dir] = $direction;
                    $query->orderBy($column, $dir);
                } else {
                    // ['field' => 'asc']
                    $query->orderBy($key, $direction);
                }
            }
        }

        return $query;
    }

    protected function normalizeFields(array|null $fields): array
    {
        return is_null($fields) ? ['*'] : $fields;
    }
}
