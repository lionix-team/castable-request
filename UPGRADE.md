# Upgrade Guide

## From 1.x to 2.0

### Requirements

- PHP 8.2 or newer (8.3+ for Laravel 13).
- Laravel 12.x or 13.x.

```bash
composer require lionix/castable-request:^2.0
```

### Form requests

No changes are needed if you only implement `CastableRequestInterface` and register global casts through `CastsRegistryInterface::register()`. Adding the `array` return type to `casts()` is still required, as it was in 1.x.

**Behavior change: validation now runs before casting.** In 1.x the input was casted first, so your rules validated the casted values. In 2.0 the rules validate the raw input and the casts are applied once validation passes. Review rules that relied on casted values. For example, with a `'count' => 'integer'` cast, a custom rule that checks `is_int($value)` will now receive the raw string `"5"`; use the built-in `integer` rule instead. Rules such as `date`, `boolean`, `integer`, `numeric` and `Rule::enum()` accept both raw and casted values, so they need no change.

`$request->validated()` and `$request->safe()` still return the casted values.

**Behavior change: request casts no longer leak.** In 1.x a request's casts were stored in the global registry, so they were also applied to every form request resolved later in the same process. In 2.0 a request's casts only apply to that request. If you relied on that side effect, register those casts globally instead.

### Renamed classes

| 1.x | 2.0 |
|-----|-----|
| `Lionix\CastableRequest\ImMemoryCastsRegistry` | `Lionix\CastableRequest\InMemoryCastsRegistry` |

### Removed classes

- `Lionix\CastableRequest\Handlers\RequestResolvingHandler`: `RequestAfterResolvingHandler` now applies both the global casts and the request's own casts.

### Custom implementations of the contracts

If you bound your own implementations of the package contracts, update their signatures:

```php
// CasterInterface
public function cast(mixed $value, string $cast): mixed;

// CastsRegistryInterface
public function register(string $attribute, string $cast): void;
public function forget(string $attribute): void; // new
public function all(): array;

// RequestInputCasterInterface
public function castAttribute(Request $request, string $attribute, string $cast): void;
public function castAttributes(Request $request, array $casts): void; // new
```

### `EloquentModelCaster`

`EloquentModelCaster` no longer extends `Illuminate\Database\Eloquent\Model`. If you extended it to change casting, implement `CasterInterface` instead, or wrap `EloquentModelCaster`, and bind your class in the container.
