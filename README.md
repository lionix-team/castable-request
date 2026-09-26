# Laravel Castable Request

[![Latest Version](https://img.shields.io/packagist/v/lionix/castable-request.svg)](https://packagist.org/packages/lionix/castable-request)
[![Tests](https://github.com/lionix-team/castable-request/actions/workflows/php.yml/badge.svg)](https://github.com/lionix-team/castable-request/actions/workflows/php.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/lionix/castable-request.svg)](https://packagist.org/packages/lionix/castable-request)
[![License](https://img.shields.io/packagist/l/lionix/castable-request.svg)](LICENSE)

Apply [Eloquent attribute casts](https://laravel.com/docs/eloquent-mutators#attribute-casting) to your form request input. Dates become `Carbon` instances, `"1"` becomes `true`, enum values become enum cases, and so on, before the input reaches your controller.

> Laravel SaaS Boilerplate - [Larafast](https://larafast.com)

## Requirements

| Package | PHP        | Laravel        |
|---------|------------|----------------|
| 2.x     | 8.2 – 8.5  | 11.x, 12.x, 13.x |
| 1.x     | 7.2 – 8.0  | 6.x – 8.x      |

Laravel 13 requires PHP 8.3 or newer.

## Installation

```bash
composer require lionix/castable-request
```

The service provider is registered automatically through package discovery.

## Usage

Implement `Lionix\CastableRequest\Contracts\CastableRequestInterface` on your form request and return the casts from a `casts()` method, just like you would on an Eloquent model.

```php
namespace App\Http\Requests;

use App\Enums\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Lionix\CastableRequest\Contracts\CastableRequestInterface;

class PostsIndexRequest extends FormRequest implements CastableRequestInterface
{
    public function casts(): array
    {
        return [
            'created_after' => 'date',
            'status' => PostStatus::class,
            'with_trashed' => 'boolean',
            'per_page' => 'integer',
            'tags.*' => 'string',
            'filters.*.value' => 'float',
        ];
    }

    public function rules(): array
    {
        return [
            'created_after' => ['sometimes', 'date'],
            'status' => ['sometimes', Rule::enum(PostStatus::class)],
            'with_trashed' => ['sometimes', 'boolean'],
            'per_page' => ['sometimes', 'integer', 'max:100'],
        ];
    }
}
```

The casted values are available through the usual request methods (`input()`, `all()`, `get()`, property access, …):

```php
namespace App\Http\Controllers;

use App\Http\Requests\PostsIndexRequest;

class PostsController extends Controller
{
    public function index(PostsIndexRequest $request)
    {
        $request->input('created_after');   // Illuminate\Support\Carbon
        $request->input('status');          // App\Enums\PostStatus
        $request->input('with_trashed');    // bool
        $request->validated('per_page');    // int
    }
}
```

### How it works

1. The form request is resolved and **validated against the raw input**, so invalid values produce the usual `422` validation errors instead of casting exceptions.
2. Once validation has passed, the casts are applied to the request input.
3. The validator's data is updated too, so `$request->validated()` and `$request->safe()` also return the casted values.

Because casting happens after validation, write your rules for the raw input, e.g. `Rule::enum(PostStatus::class)` for an enum cast or `date` for a date cast.

### Supported casts

Every cast Eloquent supports works here, including:

- Primitives: `int` / `integer`, `float` / `double` / `real`, `decimal:<precision>`, `string`, `bool` / `boolean`
- Dates: `date`, `datetime`, `immutable_date`, `immutable_datetime`, `timestamp`
- JSON-like: `array`, `json`, `object`, `collection` (both JSON strings and already-decoded arrays are accepted)
- Backed enums: `App\Enums\Status::class`
- [Custom casts](https://laravel.com/docs/eloquent-mutators#custom-casts): any class implementing `CastsAttributes` or `Castable`, including arguments (`Money::class.':EUR'`)

Casts are only applied to attributes that are present in the input. `null` values of present attributes are passed through the cast as Eloquent would (most built-in casts keep `null` as `null`).

### Nested input and wildcards

Attributes use "dot" notation and support `*` wildcards at any level:

```php
public function casts(): array
{
    return [
        'author.birthday' => 'date',
        'items.*.price' => 'decimal:2',
        'items.*.options.*.enabled' => 'boolean',
        '*.id' => 'integer', // top-level list payloads
    ];
}
```

### Global casts

To apply casts to **every** form request, register them on the `CastsRegistryInterface` singleton, e.g. in a service provider:

```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Lionix\CastableRequest\Contracts\CastsRegistryInterface;

class AppServiceProvider extends ServiceProvider
{
    public function boot(CastsRegistryInterface $casts): void
    {
        $casts->register('created_after', 'date');
        $casts->register('page', 'integer');
    }
}
```

Global casts are applied to all form requests, whether or not they implement `CastableRequestInterface`. When a request defines a cast for the same attribute, the request's cast wins.

Use `$casts->forget('page')` to remove a global cast and `$casts->all()` to list them.

### Customization

All the moving parts are bound to interfaces in the container, so you can swap any of them:

| Contract | Default implementation | Responsibility |
|----------|------------------------|----------------|
| `Contracts\CasterInterface` | `EloquentModelCaster` | Casts a single value |
| `Contracts\RequestInputCasterInterface` | `RequestInputCaster` | Resolves attribute paths and writes values back to the request |
| `Contracts\CastsRegistryInterface` | `InMemoryCastsRegistry` (singleton) | Stores global casts |

```php
$this->app->bind(
    \Lionix\CastableRequest\Contracts\CasterInterface::class,
    \App\Support\MyCaster::class,
);
```

## Upgrading

See [UPGRADE.md](UPGRADE.md) for upgrading from 1.x to 2.x, and [CHANGELOG.md](CHANGELOG.md) for all notable changes.

## Testing

```bash
composer test      # PHPUnit
composer lint      # Laravel Pint (code style)
composer analyse   # PHPStan / Larastan
```

## Credits

* [Stas Vartanyan](https://github.com/vaawebdev)
* [Lionix Team](https://github.com/lionix-team)

## License

The MIT License (MIT). See [LICENSE](LICENSE) for more information.
