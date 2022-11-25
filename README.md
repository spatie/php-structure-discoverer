# Automatically discover classes, interfaces, enums and traits within your PHP application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/php-structure-discoverer.svg?style=flat-square)](https://packagist.org/packages/spatie/php-structure-discoverer)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spatie/php-structure-discoverer/run-tests?label=tests)](https://github.com/spatie/php-structure-discoverer/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/spatie/php-structure-discoverer/Check%20&%20fix%20styling?label=code%20style)](https://github.com/spatie/php-structure-discoverer/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/php-structure-discoverer.svg?style=flat-square)](https://packagist.org/packages/spatie/php-structure-discoverer)

With this package, you'll be able to quickly discover classes within your PHP application that fulfil certain conditions. For example, you could search for a class implementing an interface, extending another class or using an attribute.

It also has a mechanism to cache these discovered classes to minimize the performance overhead of discovering classes in your production environment.

The package is not only limited to classes but can also find enums, interfaces and traits.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-structure-discoverer.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-structure-discoverer)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/php-structure-discoverer
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Spatie\StructureDiscoverer\StructureDiscovererServiceProvider"
```

This is the contents of the published config file:

```php
return [
    /*
     *  The base path where the package (recursively) will search for classes.
     *  By default, this will be the base path of your application.
     */
    'base_path' => base_path(),

    /*
     *  When you're using another root namespace, you can define it here
     */
    'root_namespace' => '',

    /*
     *  A list of files that should be ignored during the discovering process.
     */
    'ignored_files' => [],

    /*
     *  Directory where cached discover profiles are stored
     */
    'cache_directory' => storage_path('app/structure-discoverer/'),
];
```

## Usage

You can find classes within your project by creating a discover profile. This profile should be registered within a service provider, have a unique identifier and one or more specific conditions.

The discoverer will only run once when the application boots and then process all registered profiles at once, passing the discovered classes through a closure ready to be used.

You can have as many profiles as you want within your codebase as long as they have a unique identifier.

### Creating a discover profile

```php
Discover::classes('discover-settings')
    ->extending(Settings::class)
    ->get(fn (array $classes) => dump($classes));
```

When your application is booted, the `$classes` array will contain all classes extending the `Settings` class. The `discover-settings` string is the unique identifier symbolizing the profile.

You can specify a specific directory where classes should be discovered:

```php
Discover::classes('discover-settings')
	->within(app_path('settings'))
    ->extending(Settings::class)
    ->get(fn (array $classes) => dump($classes);
```

By default, the `$classes` array contains the fully qualified names of the discovered classes. It is possible to get a `ReflectionClass` instance of the discovered classes:

```php
Discover::classes('discover-settings')
    ->extending(Settings::class)
    ->returnReflection()
    ->get(fn (array $classes) => dump($classes);
```

When you want to include a specific class, you can add the following condition:

```php
Discover::classes('discover-settings')
    ->named(GeneralSettings::class)
    ->get(fn (array $classes) => dump($classes);
```

You can discover classes implementing an interface as such:

```php
Discover::classes('discover-projectors')
    ->implementing(Projector::class)
    ->get(fn (array $classes) => dump($classes);
```

Classes using an attribute can be discovered as such:

 ```php
Discover::classes('discover-routes')
    ->attribute(Route::class)
    ->get(fn (array $classes) => dump($classes);
```

You can even check if the attribute has specific parameters:

  ```php
Discover::classes('discover-routes')
    ->attribute(Route::class, ['POST'])
    ->get(fn (array $classes) => dump($classes);
```

Or you can inspect the attribute of a class using a closure to determine if it should be discovered or not:

 ```php
Discover::classes('discover-routes')
    ->attribute(Route::class, fn(Route $route) => $route->method === 'POST')
    ->get(fn (array $classes) => dump($classes);
```

For more fine-grained control, you can use a closure that receives a `ReflectionClass` and should return `true` if the class should be included:

 ```php
Discover::classes('discover-settings')
    ->custom(fn(ReflectionClass $reflection) => str_ends_with($reflection, 'Settings'))
    ->get(fn (array $classes) => dump($classes);
```

It is possible to combine conditions. These will all be applied as an AND combination. Which means they all should be valid for the class to be discovered:

  ```php
Discover::classes('discover-routes')
    ->extending(Controller::class)
    ->attribute(Route::class)
    ->get(fn (array $classes) => dump($classes);
```

In this case, only classes extending `Controller` with a `Route` attribute will be discovered.

You can include classes that adhere to one or more conditions as such:

  ```php
Discover::classes('discover-routes')
	->any(
		ProfileCondition::extending(Controller::class),
		ProfileCondition::attribute(Route::class)
	)
    ->get(fn (array $classes) => dump($classes);
```

Now classes extending `Controller` OR classes with a `Route` attribute will be discovered.

You can mix and match these conditions in any way you want.

### Caching

The package can cache all the profiles, so they do not need to be discovered when your application runs in production. This makes the whole process a lot faster.

You can cache all profiles as such:

```bash
php artisan auto-discovered:cache
```

To clear the cached profiles, you can run:

 ```bash
php artisan auto-discovered:clear
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ruben Van Assche](https://github.com/rubenvanassche)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
