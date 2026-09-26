# Changelog

All notable changes to this project are documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and this project adheres to [Semantic Versioning](https://semver.org/).

## [2.0.0] - 2026-09-26

A full modernization of the package for current PHP and Laravel releases.
See [UPGRADE.md](UPGRADE.md) for migration steps.

### Added

- Support for PHP 8.2, 8.3, 8.4 and 8.5.
- Support for Laravel 12.x and 13.x.
- `CastsRegistryInterface::forget()` to remove a global cast.
- `RequestInputCasterInterface::castAttributes()` to cast several attributes in one pass.
- Top-level wildcards (e.g. `*.id`) for list payloads.
- JSON-like casts (`array`, `json`, `object`, `collection`) now accept already-decoded input arrays instead of failing on them.
- `$request->validated()` and `$request->safe()` return the casted values.
- Test suite covering real HTTP requests, enum casts, date casts and custom casts; Laravel Pint and PHPStan (Larastan) checks; CI matrix for every supported PHP/Laravel combination.
- `LICENSE` and `CHANGELOG.md` files.

### Changed

- **Breaking:** requires PHP `^8.2` and Laravel `^12.0|^13.0`.
- **Breaking:** the package now depends on `illuminate/database`, `illuminate/http` and `illuminate/support` instead of the whole `laravel/framework`.
- **Breaking:** native parameter and return types added to all contracts:
  - `CasterInterface::cast(mixed $value, string $cast): mixed`
  - `CastsRegistryInterface::register(string $attribute, string $cast): void`
  - `RequestInputCasterInterface::castAttribute(Request $request, string $attribute, string $cast): void`
- **Breaking:** `ImMemoryCastsRegistry` was renamed to `InMemoryCastsRegistry`.
- **Breaking:** `EloquentModelCaster` no longer extends `Illuminate\Database\Eloquent\Model`; it uses an internal model for each cast.
- **Breaking:** casts are applied **after** validation instead of before it. Rules now validate the raw input, and invalid values (e.g. an unknown enum value or an unparsable date) produce a `422` validation error instead of an exception thrown by the cast.
- Request casts are no longer written to the global registry. They are merged with the global casts at cast time, and a request's own cast wins over a global cast for the same attribute.
- Request input is replaced once per request instead of once per casted attribute.
- Date casts no longer need a database connection to work out the date format.

### Removed

- **Breaking:** `Handlers\RequestResolvingHandler`. `Handlers\RequestAfterResolvingHandler` now applies both global and request casts.
- Support for PHP < 8.2 and Laravel < 12. Laravel 11 is end-of-life and every release has open security advisories, so it is not supported.
- Committed `composer.lock` and the PHP_CodeSniffer config (replaced by Laravel Pint).

### Fixed

- Casting to an enum or other non-stringable object threw an `InvalidArgumentException` on Laravel 9+ (Symfony `InputBag` rejects such values).
- Casts declared by one form request leaked into every form request resolved after it in the same process (queues, Laravel Octane, tests), because they were stored in the global singleton registry.
- Custom (class-based) casts returned the first casted value for every item matched by a wildcard, because Eloquent's class-cast cache was shared across values.
- Date casts (`date`, `datetime`, …) needed a configured database connection to cast non-standard date strings.

## [1.0.1] - 2021-01-21

### Added

- PHP 8 support.

## [1.0.0] - 2020-09-07

- Initial release.

[2.0.0]: https://github.com/lionix-team/castable-request/compare/v1.0.1...v2.0.0
[1.0.1]: https://github.com/lionix-team/castable-request/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/lionix-team/castable-request/releases/tag/v1.0.0
