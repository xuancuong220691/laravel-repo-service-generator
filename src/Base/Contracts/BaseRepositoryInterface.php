<?php

namespace CuongNX\RepoServiceGenerator\Base\Contracts;

interface BaseRepositoryInterface
{
    public function getAll(array $relations = [], array|string|null $orderBy = null);
    public function get(array|null $fields = null, array $relations = [], array|string|null $orderBy = null);
    public function getBy(string $key, $value, array|null $fields = null, array $relations = [], array|string|null $orderBy = null);
    public function getByAttributes(array $conditions, array|null $fields = null, array $relations = [], array|string|null $orderBy = null);
    public function find($id, array|null $fields = null, array $relations = [], array|string|null $orderBy = null);
    public function findBy(string $key, $value, array|null $fields = null, array $relations = [], array|string|null $orderBy = null);
    public function findOneByAttributes(array $conditions, array|null $fields = null, array $relations = [], array|string|null $orderBy = null);
    public function pluck(string $column, ?string $key = null, array $conditions = []);
    public function countBy(array $conditions = []): int;
    public function sum(string $column, array $conditions = []): float|int;
    public function avg(string $column, array $conditions = []): ?float;
    public function max(string $column, array $conditions = []): float|int|null;
    public function min(string $column, array $conditions = []): float|int|null;
    public function increment(string $column, int $amount = 1, array $conditions = [], array $extra = []): int;
    public function decrement(string $column, int $amount = 1, array $conditions = [], array $extra = []): int;
    public function chunk(int $count, callable $callback, array $conditions = []);
    public function existsBy(string $field, $value);
    public function existsByAttributes(array $conditions);
    public function paginate(int $perPage = 15, array $conditions = [], array|null $fields = null, array $relations = []);
    public function paginateCustom(array $conditions = [], ?array $fields = null, array $relations = [], array|string|null $orderBy = null, int $page = 1, int $limit = 10);
    public function create(array $data);
    public function update($id, array $data);
    public function updateFields($model, array $fields, array $except = []): mixed;
    public function createOrUpdate(array $attributes, array $values = [], array|null $checkFields = null): array;
    public function delete($id);
    public function deleteBy(array $conditions): int;
    public function withTrashed(array $conditions = [], array|null $fields = null, array $relations = []);
    public function onlyTrashed(array $conditions = [], array|null $fields = null, array $relations = []);
    public function restore($id): bool;
    public function forceDelete($id): bool;
}
