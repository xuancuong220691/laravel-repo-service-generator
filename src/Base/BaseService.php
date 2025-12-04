<?php

namespace CuongNX\RepoServiceGenerator\Base;

use CuongNX\RepoServiceGenerator\Base\Contracts\BaseServiceInterface;

abstract class BaseService implements BaseServiceInterface
{
    abstract protected function getRepository();

    public function getAll(array $relations = [], array|string|null $orderBy = null)
    {
        return $this->getRepository()->getAll($relations, $orderBy);
    }

    public function get(?array $fields = null, array $relations = [], array|string|null $orderBy = null)
    {
        return $this->getRepository()->get($fields, $relations, $orderBy);
    }

    public function getBy(string $key, $value, array|null $fields = null, array $relations = [], array|string|null $orderBy = null)
    {
        return $this->getRepository()->getBy($key, $value, $fields, $relations, $orderBy);
    }


    public function getByAttributes(array $conditions, ?array $fields = null, array $relations = [], array|string|null $orderBy = null)
    {
        return $this->getRepository()->getByAttributes($conditions, $fields, $relations, $orderBy);
    }

    public function find($id, array|null $fields = null, array $relations = [], array|string|null $orderBy = null)
    {
        return $this->getRepository()->find($id, $fields, $relations, $orderBy);
    }

    public function findBy(string $key, $value, ?array $fields = null, array $relations = [], array|string|null $orderBy = null)
    {
        return $this->getRepository()->findBy($key, $value, $fields, $relations, $orderBy);
    }

    public function findByAttributes(array $conditions, ?array $fields = null, array $relations = [], array|string|null $orderBy = null)
    {
        return $this->getRepository()->findOneByAttributes($conditions, $fields, $relations, $orderBy);
    }

    public function pluck(string $column, ?string $key = null, array $conditions = [])
    {
        return $this->getRepository()->pluck($column, $key, $conditions);
    }

    public function countBy(array $conditions = []): int
    {
        return $this->getRepository()->countBy($conditions);
    }

    public function sum(string $column, array $conditions = []): float|int
    {
        return $this->getRepository()->sum($column, $conditions);
    }

    public function avg(string $column, array $conditions = []): ?float
    {
        return $this->getRepository()->avg($column, $conditions);
    }

    public function max(string $column, array $conditions = []): float|int|null
    {
        return $this->getRepository()->max($column, $conditions);
    }

    public function min(string $column, array $conditions = []): float|int|null
    {
        return $this->getRepository()->min($column, $conditions);
    }

    public function increment(string $column, int $amount = 1, array $conditions = [], array $extra = []): int
    {
        return $this->getRepository()->increment($column, $amount, $conditions, $extra);
    }

    public function decrement(string $column, int $amount = 1, array $conditions = [], array $extra = []): int
    {
        return $this->getRepository()->decrement($column, $amount, $conditions, $extra);
    }

    public function chunk(int $count, callable $callback, array $conditions = [])
    {
        return $this->getRepository()->chunk($count, $callback, $conditions);
    }

    public function existsBy(string $field, $value): bool
    {
        return $this->getRepository()->existsBy($field, $value);
    }

    public function existsByAttributes(array $conditions): bool
    {
        return $this->getRepository()->existsByAttributes($conditions);
    }

    public function paginate(int $perPage = 15, array $conditions = [], ?array $fields = null, array $relations = [])
    {
        return $this->getRepository()->paginate($perPage, $conditions, $fields, $relations);
    }

    public function paginateCustom(array $conditions = [], ?array $fields = null, array $relations = [], array|string|null $orderBy = null, int $page = 1, int $limit = 10)
    {
        return $this->getRepository()->paginateCustom($conditions, $fields, $relations, $orderBy, $page, $limit);
    }

    public function create(array $data)
    {
        return $this->getRepository()->create($data);
    }

    public function update($id, array $data)
    {
        return $this->getRepository()->update($id, $data);
    }

    public function updateFields($model, array $fields, array $except = []): mixed
    {
        return $this->getRepository()->updateFields($model, $fields, $except);
    }

    public function createOrUpdate(array $attributes, array $values = [], array|null $checkFields = null): array
    {
        return $this->getRepository()->createOrUpdate($attributes, $values, $checkFields);
    }

    public function delete($id)
    {
        return $this->getRepository()->delete($id);
    }

    public function deleteBy(array $conditions): int
    {
        return $this->getRepository()->deleteBy($conditions);
    }

    public function withTrashed(array $conditions = [], ?array $fields = null, array $relations = [])
    {
        return $this->getRepository()->withTrashed($conditions, $fields, $relations);
    }

    public function onlyTrashed(array $conditions = [], ?array $fields = null, array $relations = [])
    {
        return $this->getRepository()->onlyTrashed($conditions, $fields, $relations);
    }

    public function restore($id): bool
    {
        return $this->getRepository()->restore($id);
    }

    public function forceDelete($id): bool
    {
        return $this->getRepository()->forceDelete($id);
    }
}
