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
- [Base Structure Generation](#base-structure-generation)
- [Full Structure Generation](#create-full-structure-model--repo--service)
- [Create Simple Service](#create-simple-service-interface)
- [Bindings](#bindings)
- [Unbind](#unbind-bindings)
- [Remove Structures](#remove-structures)
- [BaseService Methods](#baseservice-methods)
- [Service Usage](#service-usage-example)
- [Folder Structure](#folder-structure)
- [Donate](#donate)
- [Contact](#contact)
- [License](#license)

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
composer require cuongnx/laravel-repo-service
```

> 📦 For MongoDB support:

```bash
composer require mongodb/laravel-mongodb
```

---

<h2 id="base-structure-generation">📦 Base Structure Generation</h2>

Create base repository/service interfaces and classes:

```bash
php artisan cuongnx:make-base
```

### Options:

| Flag      | Description              |
| --------- | ------------------------ |
| `--f`     | Overwrite existing files |
| `--force` | Alias for `--f`          |

This command will generate the following files and structure:

```
app/
├── Repositories/
│   ├── Contracts/
│   │   └── BaseRepositoryInterface.php
│   └── BaseRepository.php
└── Services/
    ├── Contracts/
    │   └── BaseServiceInterface.php
    └── BaseService.php
```

### File Descriptions:

- `app/Repositories/Contracts/BaseRepositoryInterface.php`  
  → Interface that defines common methods for a repository.

- `app/Repositories/Eloquent/BaseRepository.php`  
  → Implements basic data access methods (CRUD, conditions, pagination...).

- `app/Services/Contracts/BaseServiceInterface.php`  
  → Interface that defines common methods for a service layer.

- `app/Services/BaseService.php`  
  → Implements business logic on top of the repository layer.

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

<h2 id="baseservice-methods">🧩 BaseService Methods</h2>
All services extend `BaseService` and automatically gain access to these common data methods.

### 🔍 Read Methods

| Method | Description |
|--------|-------------|
| `getAll(array $relations = [])` | Get all records with optional relationships |
| `get(?array $fields = null, array $relations = [])` | Get all records with selected fields & relationships |
| `find($id, ?array $fields = null, array $relations = [])` | Find by ID |
| `findBy(string $key, $value, ?array $fields = null, array $relations = [])` | Find a record by key-value |
| `findByAttributes(array $conditions, ?array $fields = null, array $relations = [])` | Find a single record by multiple attributes |
| `getBy(string $key, $value, ?array $fields = null, array $relations = [])` | Get multiple records by key-value |
| `getByAttributes(array $conditions, ?array $fields = null, array $relations = [])` | Get multiple records by attributes |
| `withTrashed(array $conditions = [], ?array $fields = null, array $relations = [])` | Get including soft-deleted |
| `onlyTrashed(array $conditions = [], ?array $fields = null, array $relations = [])` | Get only soft-deleted |

### 📊 Paginate

| Method | Description |
|--------|-------------|
| `paginate(int $perPage = 15, array $conditions = [], ?array $fields = null, array $relations = [])` | Paginated list with filters and relationships |

### ✅ Existence Checks

| Method | Description |
|--------|-------------|
| `existsBy(string $field, $value): bool` | Check if a value exists for a field |
| `existsByAttributes(array $conditions): bool` | Check existence by multiple attributes |

### 📝 Create/Update

| Method | Description |
|--------|-------------|
| `create(array $data)` | Create a new record |
| `update($id, array $data)` | Update by ID |
| `updateFields($model, array $fields, array $except = [])` | Update specific fields on a model |
| `createOrUpdate(array $attributes, array $values = [], array $checkFields = [])` | Create or update a record by match conditions |

### ❌ Delete / Restore

| Method | Description |
|--------|-------------|
| `delete($id)` | Soft delete by ID |
| `deleteBy(array $conditions)` | Soft delete by conditions |
| `restore($id): bool` | Restore soft-deleted record |
| `forceDelete($id): bool` | Permanently delete record |

---

<h2 id="service-usage-example">🧠 Service Usage Example</h2>

```php
use App\Services\UserService;

class PostService extends BaseService
{
    public function __construct(protected UserService $userService) {}

    protected function getRepository()
    {
        return $this->postRepo;
    }

    public function assignAuthor($postId, $userId)
    {
        $user = $this->userService->find($userId);
        $post = $this->find($postId);
        $post->author_id = $user->id;
        $post->save();

        return $post;
    }
}
```

### Controller:

```php
use App\Services\PostService;

class PostController extends Controller
{
    public function __construct(protected PostService $postService) {}

    public function index()
    {
        return $this->postService->getAll();
    }
}
```

---

<h2 id="folder-structure">📁 Folder Structure</h2>

```
app/
├── Repositories/
│   ├── Contracts/
│   │   └── BaseRepositoryInterface.php
│   │   └── UserRepositoryInterface.php
│   │   └── PostRepositoryInterface.php
│   └── Eloquent/
│       └── BaseRepository.php
│       └── UserRepository.php
│       └── PostRepository.php
├── Services/
│   ├── Contracts/
│   │   └── BaseServiceInterface.php
│   │   └── CustomServiceInterface.php
│   │   └── PostServiceInterface.php
│   │   └── BaseServiceInterface.php
│   ├── BaseService.php
│   └── CustomService.php
│   └── UserService.php
│   └── PostService.php
└── Models/
    └── User.php
    └── Post.php    
```
---


## 🧩 Extending BaseService
You can override or extend any of these methods in your custom service classes.

---

<h2 id="donate">💖 Donate</h2>
If you find this package useful, feel free to support the development:

### ☕ Coffee & Support

* [https://coff.ee/xuancuong2f](https://coff.ee/xuancuong2f)
* [https://paypal.me/cuongnx91](https://paypal.me/cuongnx91)

### 🏦 Bank (VIETQR)

> ![QR Code Techcombank](https://img.vietqr.io/image/970407-1368686856-print.png?accountName=Nguyen%20Xuan%20Cuong)
>
> **Account Holder**: NGUYEN XUAN CUONG  
> **Account Number**: `1368686856`  
> **Bank**: Techcombank


---

<h2 id="contact">📬 Contact</h2>

* Email: [xuancuong220691@gmail.com](mailto:xuancuong220691@gmail.com)

---

<h2 id="license">📝 License</h2>

* MIT License © [Cuong Nguyen](mailto:xuancuong220691@gmail.com)


