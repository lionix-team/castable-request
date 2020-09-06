# Laravel Castable Request

This package applies eloquent model casts to the request input.

## Installation

```
composer require lionix/castable-request
```

## Usage

Implement `Lionix\CastableRequest\Contracts\CastableRequestInterface` in your Request class.
You will have to declare `casts` method that will return the attributes that should be casted just like you would do it with [eloquent attribute casting](https://laravel.com/docs/7.x/eloquent-mutators#attribute-casting).

```php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Lionix\CastableRequest\Contracts\CastableRequestInterface;

class PostsIndexRequest extends FormRequest implements CastableRequestInterface
{
    /**
     * Get request casts.
     *
     * @return array
     */
    public function casts(): array
    {
        return [
            'created_after' => 'date',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'created_after' => 'date',
        ];
    }
}
```

The package will do all the magic and when you access the request `created_after` attribute, in this case, it will be casted to an `Illuminate\Support\Carbon` instance.

```php
namespace App\Http\Controllers;

use App\Http\Requests\PostsIndexRequest;

class PostsController extends Controller
{
    public function index(PostsIndexRequest $request)
    {
        $createdAfterDiff = $request->input('created_after')->diffForHumans();
        // Example value: 1 month from now
    }
}
```

All default eloquent models castings are available.

Starting from the **Laravel 7.x** you can define your own [custom casts](https://laravel.com/docs/7.x/eloquent-mutators#custom-casts) and use it in the request as well.

### Global request casts

If you want to declare casts that will be applied globally without having to define it in each Request class you can use the `Lionix\CastableRequest\Contracts\CastsRegistryInterface` in your service provider and register global casts.

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Lionix\CastableRequest\Contracts\CastsRegistryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param \Lionix\CastableRequest\Contracts\CastsRegistryInterface $castRegistry
     *
     * @return void
     */
    public function boot(CastsRegistryInterface $castRegistry)
    {
        $castRegistry->register('created_after', 'date');
    }
}
```

## Todo

- [x] Setup composer dependencies and testing environment.
- [x] Implement input caster with casts given as string or classname.
- [x] Implement request input caster.
- [x] Implement cast registry in which user can register global casts that will apply to all requests.
- [x] Implement service provider that will apply cast registry casts to the requests.
- [x] Complete [Installation](#installation) readme section.
- [x] Complete [Usage](#usage) readme section.
- [ ] Publish composer package.
- [ ] Intergate all fancy readme badges.
- [ ] Create [Medium](https://medium.com/) article with brief package review.

## Credits

- [Stas Vartanyan](https://github.com/vaawebdev)
- [Lionix Team](https://github.com/lionix-team)
