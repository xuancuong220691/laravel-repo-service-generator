# 🧱 Laravel Repo-Service Struct Generator

✅ Generate clean Repository-Service structure with interfaces, bindings, and optional MongoDB support for Laravel 11 and 12+

---

This library provides a powerful artisan command set to generate and manage Repository-Service architecture with optional binding, base classes, and multi-model support. It helps you follow a clean, testable architecture in Laravel projects.

---

## 🧾 Version Information

|**Library Version** | v1.0.0                                         |
| ------------------- | ---------------------------------------------- |
| **Laravel**         | ^11.0, ^12.0                                   |
| **PHP Version**     | >= 8.1                                         |
| **MongoDB Support** | Optional (`--type=m`) via [`mongodb/laravel-mongodb`](https://github.com/mongodb/laravel-mongodb) |

---

## 📚 Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Full Structure Generation](#create-full-structure-model--repo--service)
- [Create Simple Service](#create-simple-service-interface)
- [Bindings](#bindings)
- [Unbind](#unbind-bindings)
- [Remove Structures](#remove-structures)
- [BaseRepository Methods](#baserepository-methods)
- [BaseService Methods](#baseservice-methods)
- [Advanced Query Conditions](#advanced-query-conditions)
- [Folder Structure](#folder-structure)


---

<h2 id="features">⚙️ ✅ Features</h2>

- Generate Repository, Service, Interface automatically.
- Optional Model generation (Eloquent or MongoDB).
- Auto-bind/unbind to `AppServiceProvider`.
- Middleware-safe service usage.
- Reversible file generation (remove struct).
- Extendable base classes.
- Fast CLI operations.

---

<h2 id="installation">⚙️ Installation</h2>

```bash
composer require cuongnx/laravel-repo-service-generator
```

> 📦 For MongoDB support:

```bash
composer require mongodb/laravel-mongodb
```

---

<h2 id="create-full-structure-model--repo--service">🧱 Create Full Structure (Model + Repo + Service)</h2>

```bash
php artisan cuongnx:make-struct Post
```

### Options:

| Flag             | Description                                         |
| ---------------- | --------------------------------------------------- |
| `--model`, `--m`  | Also generate the model class                       |
| `--type=`        | Model type: `d` = Eloquent (default), `m` = MongoDB |
| `--no-bind`      | Skip automatic binding in `AppServiceProvider`      |
| `--f`, `--force` | Overwrite existing files                            |

📌 Example with MongoDB:

```bash
php artisan cuongnx:make-struct Product --m --type=m
```

This command will generate the following files and structure:

```
app/
├── Models/
│   └── Post.php
├── Repositories/
│   ├── Contracts/
│   │   └── PostRepositoryInterface.php
│   └── PostRepository.php
└── Services/
    ├── Contracts/
    │   └── PostServiceInterface.php
    └── PostService.php
```

### File Descriptions:

- `app/Models/Post.php`  
  → The Eloquent or MongoDB model class for `Post`.

- `app/Repositories/Contracts/PostRepositoryInterface.php`  
  → Interface defining methods specific to the `Post` repository.

- `app/Repositories/PostRepository.php`  
  → Repository class implementing data access logic for `Post`.  
  Extends `BaseRepository` and implements `PostRepositoryInterface`.

- `app/Services/Contracts/PostServiceInterface.php`  
  → Interface defining service-level business methods for `Post`.

- `app/Services/PostService.php`  
  → Service class implementing business logic related to `Post`.  
  Extends `BaseService` and implements `PostServiceInterface`.

📌 If `--no-bind` is not provided, the following bindings will be added to `AppServiceProvider`:

```php
$this->app->bind(
    \App\Repositories\Contracts\PostRepositoryInterface::class,
    \App\Repositories\PostRepository::class
);

$this->app->bind(
    \App\Services\Contracts\PostServiceInterface::class,
    \App\Services\PostService::class
);
```

---

<h2 id="create-simple-service-interface">🧱 Create Simple Service & Interface (Without implement BaseService)</h2>

```bash
php artisan cuongnx:make-service Custom
```

### Options:

| Flag             | Description                                         |
| ---------------- | --------------------------------------------------- |
| `--no-bind`      | Skip automatic binding in `AppServiceProvider`      |
| `--f`, `--force` | Overwrite existing files                            |

This command will generate the following files and structure:

```
app/
└── Services/
    ├── Contracts/
    │   └── CustomServiceInterface.php
    └── CustomService.php
```

### File Descriptions:

- `app/Services/Contracts/CustomServiceInterface.php`  
  → Defines custom methods for your `Custom` service logic.

- `app/Services/CustomService.php`  
  → Contains business logic related to `Custom`, implements `CustomServiceInterface`.

---

<h2 id="bindings">🔌 Bindings</h2>
Bind both repository & service:

```bash
php artisan cuongnx:bind-model User
```

### Options:

| Flag             | Description          |
| ---------------- | -------------------- |
| `--only=repo`    | Bind only repository |
| `--only=service` | Bind only service    |

Bind individually:

```bash
php artisan cuongnx:bind-repo User
php artisan cuongnx:bind-service User
```

---

<h2 id="unbind-bindings">❌ Unbind Bindings</h2>
```bash
php artisan cuongnx:unbind-model User
```

### Options:

| Flag             | Description            |
| ---------------- | ---------------------- |
| `--only=repo`    | Unbind only repository |
| `--only=service` | Unbind only service    |

Or directly:

```bash
php artisan cuongnx:unbind-repo User
```
```bash
php artisan cuongnx:unbind-service User
```

---

<h2 id="remove-structures">🧹 Remove Structures</h2>
Remove all (repo + service + optional model):

```bash
php artisan cuongnx:remove-struct Post --model
```

### Options:

| Flag            | Description                           |
| --------------- | ------------------------------------- |
| `--model`, `-m` | Also remove model                     |
| `--no-unbind`   | Do not unbind from AppServiceProvider |

Remove only service:

```bash
php artisan cuongnx:remove-service Post
```

---

<h2 id="baserepository-methods">🗄️ BaseRepository Methods</h2>

All repositories extend `BaseRepository` and automatically gain access to these common data methods.

### 🔍 Read Methods

| Method | Description |
|--------|-------------|
| `getAll(array $relations = [], array\|string\|null $orderBy = null)` | Get all records with optional relationships and ordering |
| `get(?array $fields = null, array $relations = [], array\|string\|null $orderBy = null)` | Get all records with selected fields, relationships, and ordering |
| `find($id, ?array $fields = null, array $relations = [])` | Find by ID with optional field selection and relationships |
| `findBy(string $key, $value, ?array $fields = null, array $relations = [], array\|string\|null $orderBy = null)` | Find a single record by key-value |
| `findByAttributes(array $conditions, ?array $fields = null, array $relations = [], array\|string\|null $orderBy = null)` | Find a single record by multiple attributes |
| `getBy(string $key, $value, ?array $fields = null, array $relations = [], array\|string\|null $orderBy = null)` | Get multiple records by key-value |
| `getByAttributes(array $conditions, ?array $fields = null, array $relations = [], array\|string\|null $orderBy = null)` | Get multiple records by attributes |
| `withTrashed(array $conditions = [], ?array $fields = null, array $relations = [])` | Get including soft-deleted records |
| `onlyTrashed(array $conditions = [], ?array $fields = null, array $relations = [])` | Get only soft-deleted records |

### 📊 Pagination

| Method | Description |
|--------|-------------|
| `paginate(int $perPage = 15, array $conditions = [], ?array $fields = null, array $relations = [])` | Laravel paginated list with filters and relationships |
| `paginateCustom(array $conditions = [], ?array $fields = null, array $relations = [], array\|string\|null $orderBy = null, int $page = 1, int $limit = 10)` | Custom pagination returning array with data, current_page, per_page, total, last_page |

### 📈 Aggregate Methods

| Method | Description |
|--------|-------------|
| `countBy(array $conditions = []): int` | Count records by conditions |
| `sum(string $column, array $conditions = []): float\|int` | Sum a column value with optional conditions |
| `avg(string $column, array $conditions = []): ?float` | Average of a column value |
| `max(string $column, array $conditions = []): float\|int\|null` | Maximum value of a column |
| `min(string $column, array $conditions = []): float\|int\|null` | Minimum value of a column |

### 🔧 Utility Methods

| Method | Description |
|--------|-------------|
| `pluck(string $column, ?string $key = null, array $conditions = [])` | Pluck values from a column |
| `chunk(int $count, callable $callback, array $conditions = []): bool` | Process records in chunks |
| `increment(string $column, int $amount = 1, array $conditions = [], array $extra = []): int` | Increment column value |
| `decrement(string $column, int $amount = 1, array $conditions = [], array $extra = []): int` | Decrement column value |

### ✅ Existence Checks

| Method | Description |
|--------|-------------|
| `existsBy(string $field, $value): bool` | Check if a value exists for a field |
| `existsByAttributes(array $conditions): bool` | Check existence by multiple attributes |

### 📝 Create/Update Methods

| Method | Description |
|--------|-------------|
| `create(array $data)` | Create a new record |
| `update($id, array $data)` | Update by ID |
| `updateFields($model, array $fields, array $except = [])` | Update specific fields on a model instance |
| `firstOrCreate(array $attributes, array $values = [], array $relations = [])` | Find or create a record, returns `[Model, bool $wasCreated]` |
| `firstOrNew(array $attributes, array $values = [], array $relations = [])` | Find or instantiate (without saving), returns `[Model, bool $isNew]` |
| `createOrUpdate(array $attributes, array $values = [], ?array $checkFields = null)` | Create or update with field tracking, returns `[Model, bool $wasCreated, bool $wasUpdated, array $changedFields]` |
| `updateOrCreate(array $attributes, array $values = [], array $relations = [])` | Find and update or create, returns `[Model, bool $wasCreated]` |

### ❌ Delete / Restore Methods

| Method | Description |
|--------|-------------|
| `delete($id)` | Soft delete by ID |
| `deleteBy(array $conditions): int` | Delete by conditions, returns number of deleted records |
| `restore($id): bool` | Restore soft-deleted record |
| `forceDelete($id): bool` | Permanently delete record |

---

<h2 id="baseservice-methods">🧠 BaseService Methods</h2>

All services extend `BaseService` and delegate to the repository methods. The service layer is where you implement business logic on top of the repository.

Services have access to all BaseRepository methods through their repository instance. You can add custom business logic methods in your service classes.

---

<h2 id="advanced-query-conditions">🔍 Advanced Query Conditions</h2>

The `BaseRepository` supports flexible query conditions for both Eloquent and MongoDB.

### 1️⃣ Simple Conditions

```php
$conditions = [
    'status' => 'active',
    'user_id' => 123
];

$posts = $postRepo->getByAttributes($conditions);
```

### 2️⃣ Comparison Operators

```php
$conditions = [
    'price' => ['>=', 100],
    'stock' => ['<', 50],
    'rating' => ['>', 4.5]
];

$products = $productRepo->getByAttributes($conditions);
```

### 3️⃣ MongoDB Operators

```php
// Using MongoDB $gte, $lte operators
$conditions = [
    'expired_at' => [
        '$gte' => now(),
        '$lte' => now()->addDays(30)
    ]
];

// Using MongoDB $elemMatch for array fields
$conditions = [
    'tags' => [
        '$elemMatch' => ['name' => 'Laravel', 'type' => 'framework']
    ]
];
```

### 4️⃣ Date Range Queries

```php
// Using from/to syntax
$conditions = [
    'created_at' => [
        'from' => now()->subDays(7),
        'to' => now()
    ]
];

// Using min/max syntax
$conditions = [
    'price' => [
        'min' => 100,
        'max' => 1000
    ]
];

// Using between operator
$conditions = [
    'age' => ['between', [18, 65]]
];
```

### 5️⃣ IN / NOT IN Queries

```php
$conditions = [
    'status' => ['in', ['active', 'pending', 'processing']],
    'category_id' => ['not_in', [5, 10, 15]]
];
```

### 6️⃣ NULL Checks

```php
$conditions = [
    'deleted_at' => ['null'],
    'email_verified_at' => ['not_null']
];
```

### 7️⃣ Combined Complex Queries

```php
$conditions = [
    'status' => 'active',
    'price' => [
        '$gte' => 100,
        '$lte' => 1000
    ],
    'category_id' => ['in', [1, 2, 3]],
    'created_at' => [
        'from' => now()->subMonth(),
        'to' => now()
    ],
    'tags' => [
        '$elemMatch' => ['featured' => true]
    ]
];

$products = $productRepo->getByAttributes(
    conditions: $conditions,
    fields: ['id', 'name', 'price'],
    relations: ['category', 'images'],
    orderBy: ['created_at' => 'desc']
);
```

### 8️⃣ Ordering Results

```php
// Simple ordering (string)
$orderBy = 'created_at'; // defaults to 'asc'

// Single field ordering (array)
$orderBy = ['created_at' => 'desc'];

// Multiple field ordering
$orderBy = [
    'status' => 'asc',
    'created_at' => 'desc'
];

// Alternative syntax with indexed arrays
$orderBy = [
    ['status', 'asc'],
    ['created_at', 'desc']
];
```

### Real-World Examples

#### Get Active Products Expiring Soon
```php
$products = $productRepo->getByAttributes([
    'status' => 'active',
    'expired_at' => [
        '$gte' => now(),
        '$lte' => now()->addDays(30)
    ]
], orderBy: ['expired_at' => 'asc']);
```

#### Get Orders from Last Month in Price Range
```php
$orders = $orderRepo->getByAttributes([
    'total_amount' => [
        'min' => 1000,
        'max' => 5000
    ],
    'created_at' => [
        'from' => now()->subMonth()->startOfMonth(),
        'to' => now()->subMonth()->endOfMonth()
    ],
    'status' => ['in', ['completed', 'shipped']]
], relations: ['user', 'items']);
```

#### MongoDB Array Query with Tags
```php
$posts = $postRepo->getByAttributes([
    'published' => true,
    'tags' => [
        '$elemMatch' => [
            'name' => 'Laravel',
            'type' => 'framework'
        ]
    ],
    'views' => ['>=', 1000]
], orderBy: ['views' => 'desc']);
```
<h2 id="folder-structure">📁 Folder Structure</h2>

```
app/
├── Repositories/
│   ├── Contracts/
│   │   ├── BaseRepositoryInterface.php
│   │   ├── UserRepositoryInterface.php
│   │   └── PostRepositoryInterface.php
│   ├── BaseRepository.php
│   ├── UserRepository.php
│   └── PostRepository.php
├── Services/
│   ├── Contracts/
│   │   ├── BaseServiceInterface.php
│   │   ├── CustomServiceInterface.php
│   │   ├── UserServiceInterface.php
│   │   └── PostServiceInterface.php
│   ├── BaseService.php
│   ├── CustomService.php
│   ├── UserService.php
│   └── PostService.php
└── Models/
    ├── User.php
    └── Post.php
```


<h2 id="contact">📬 Contact</h2>

* Email: [xuancuong220691@gmail.com](mailto:xuancuong220691@gmail.com)

---

<h2 id="license">📝 License</h2>

* MIT License © [Cuong Nguyen](mailto:xuancuong220691@gmail.com)


