# Automatically discover classes, interfaces, enums, and traits within your PHP application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/php-structure-discoverer.svg?style=flat-square)](https://packagist.org/packages/spatie/php-structure-discoverer)
[![run-tests](https://github.com/spatie/php-structure-discoverer/actions/workflows/run-tests.yml/badge.svg)](https://github.com/spatie/php-structure-discoverer/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/spatie/php-structure-discoverer/actions/workflows/phpstan.yml/badge.svg)](https://github.com/spatie/php-structure-discoverer/actions/workflows/phpstan.yml)
[![Check & fix styling](https://github.com/spatie/php-structure-discoverer/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/spatie/php-structure-discoverer/actions/workflows/php-cs-fixer.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/php-structure-discoverer.svg?style=flat-square)](https://packagist.org/packages/spatie/php-structure-discoverer)

With this package, you'll be able to discover structures in your PHP application that fulfill certain conditions quickly. For example, you could search for classes implementing an interface:

```php
use Spatie\StructureDiscoverer\Discover;

// PostModel::class, Collection::class, ...
Discover::in(__DIR__)->classes()->implementing(Arrayable::class)->get(); 
```

As an added benefit, it has a built-in cache functionality that makes the whole process fast in production.

The package is not only limited to classes but can also find enums, interfaces, and traits and has extra metadata for each structure.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/php-structure-discoverer.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/php-structure-discoverer)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can
support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.
You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards
on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/php-structure-discoverer
```

If you're using Laravel, then you can also publish the config file with the following command:

```bash
php artisan vendor:publish --tag="structure-discoverer-config"
```

This is the contents of the published config file:

```php
return [
    /*
     *  A list of files that should be ignored during the discovering process.
     */
    'ignored_files' => [

    ],

    /**
     * The directories where the package should search for structure scouts
     */
    'structure_scout_directories' => [
        app_path(),
    ],

    /*
     *  Configure the cache driver for discoverers
     */
    'cache' => [
        'driver' => \Spatie\StructureDiscoverer\Cache\LaravelDiscoverCacheDriver::class,
        'store' => null,
    ]
];
```

## Usage

You always need to define in which directories you want to look for structures:

```php
Discover::in(__DIR__)->...
```

It is possible to look in multiple directories like this:

```php
Discover::in(app_path('models'), app_path('enums'))->...
```

You can get the structures as such:

```php
Discover::in(__DIR__)->get();
```

Which will return an array of class FCQN, and because no conditions were added, the package will return all classes, enums, interfaces, and traits.

You only discover classes like this:

```php
Discover::in(__DIR__)->classes()->get();
```

Interfaces like this:

```php
Discover::in(__DIR__)->interfaces()->get();
```

Enums like this:

```php
Discover::in(__DIR__)->enums()->get();
```

And traits like this:

```php
Discover::in(__DIR__)->traits()->get();
```

When you want to include a specific named structure, you can do the following:

```php
Discover::in(__DIR__)->named('MyAwesomeClass')->get();
```

You can discover classes extending another class as such:

```php
Discover::in(__DIR__)->extending(Model::class)->get();
```

Discovering classes, interfaces, or enums implementing an interface can be done like this:

```php
Discover::in(__DIR__)->implementing(Arrayable::class)->get();
```

Be aware that although interfaces extend another interface, in this context, the implements keyword seemed a more logical choice to find interfaces extended by another interface. Using the `extends` method for such a filter won't work!

Classes, interfaces, or traits using an attribute can be discovered as such:

```php
Discover::in(__DIR__)->withAttribute(Cast::class)->get();
```

For more fine-grained control, you can use a closure that receives a `DiscoveredStructure` object (more on that later) and should return `true` if the structure should be included:

 ```php
Discover::in(__DIR__)
    ->custom(fn(DiscoveredStructure $structure) => $structure->namespace === 'App')
    ->get()
```

More complex custom conditions can be embedded in a class:

```php
class AppDiscoverCondition extends DiscoverCondition 
{
    public function satisfies(DiscoveredStructure $discoveredData): bool
    {
        return $structure->namespace === 'App';
    }
};
```

This condition can now be used like this:

 ```php
Discover::in(__DIR__)
    ->custom(new AppDiscoverCondition())
    ->get()
```

### Combining conditions

By default, all conditions will work like an AND operation, so in this case:

```php
Discover::in(__DIR__)->classes()->implementing(Arrayable::class)->get();
```

The package will only look for structures that are a class **and** implement `Arrayble`.

You can create an OR combination of conditions like this:

```php
Discover::in(__DIR__)
    ->any(
        ConditionBuilder::create()->classes(),
        ConditionBuilder::create()->enums()
    )
    ->get();
```

Now, the package will only discover classes **or** enum structures.

You can also create more complex operations like an or of and's:

```php
Discover::in(__DIR__)
    ->any(
        ConditionBuilder::create()->exact(
            ConditionBuilder::create()->classes(),
            ConditionBuilder::create()->implementing(Arrayble::class),
        ),
        ConditionBuilder::create()->exact(
            ConditionBuilder::create()->enums(),
            ConditionBuilder::create()->implementing(Stringable::class),
        )
    )
    ->get();
```

This example can be written shorter like this:

```php
Discover::in(__DIR__)
    ->any(
        ConditionBuilder::create()->exact(
            ConditionBuilder::create()->classes()->implementing(Arrayble::class),
        ),
        ConditionBuilder::create()->exact(
            ConditionBuilder::create()->enums()->implementing(Stringable::class),
        )
    )
    ->get();
```

### Sorting

By default, the discovered structures will be sorted according to the OS' default.

You can change the sorting like this:

```php
use Spatie\StructureDiscoverer\Enums\Sort;

Discover::in(__DIR__)->sortBy(Sort::Name)->get();
```

Here are all the available sorting options:

```php
use Spatie\StructureDiscoverer\Enums\Sort;

Discover::in(__DIR__)->sortBy(Sort::Name);
Discover::in(__DIR__)->sortBy(Sort::Size);
Discover::in(__DIR__)->sortBy(Sort::Type);
Discover::in(__DIR__)->sortBy(Sort::Extension);
Discover::in(__DIR__)->sortBy(Sort::ChangedTime);
Discover::in(__DIR__)->sortBy(Sort::ModifiedTime);
Discover::in(__DIR__)->sortBy(Sort::AccessedTime);
Discover::in(__DIR__)->sortBy(Sort::CaseInsensitiveName);
```

### Caching

This package can cache all discovered structures, so no performance-heavy operations are required in production.

The fastest way to start caching is by creating a structure scout, which is a class that describes what you want to discover:

```php
class EnumsStructureScout extends StructureScout
{
    protected function definition(): Discover|DiscoverConditionFactory
    {
        return Discover::in(__DIR__)->enums();
    }

    public function cacheDriver(): DiscoverCacheDriver
    {
        return new FileDiscoverCacheDriver('/path/to/temp/directory');
    }
}
```

Each structure scout extends from `StructureScout` and should have

- a definition where you describe what to discover and where. Just like we did inline earlier
- a driver to be used for the cache. When you're using Laravel, this method is not required since it is already defined in the config file

Within your application, you can use the discoverer as such:

```php
EnumsStructureScout::create()->get();
```

The first time this method is called, the whole discovery process will run taking a bit more time. The second call will skip the discovery process and use the cached version, making a call to this method amazingly fast!

#### In production

When you're deploying to production, you can warm all your structure scout caches as such:

```php
StructureScoutManager::cache([__DIR__]); 
```

You should provide a directory where the structure scouts are stored.

If you're using Laravel, you can run the following command:``

````bash
php artisan structure-scouts:cache
````

It is also possible to clear all caches for structure scouts as such:

```php
StructureScoutManager::clear([__DIR__]); 
```

Or, if you're using Laravel:

````bash
php artisan structure-scouts:clear
````

##### For packages

Since an individual user defines the directories where structure scouts can be found, packages can't ensure their structure scouts will be discovered with the cache commands.

It is possible to add structure scouts like this manually:

```php
StructureScoutManager::add(SettingsStructureScout::class); 
```

In a Laravel application, you typically do this within the package ServiceProvider.

#### Cache drivers

##### File

The `FileDiscoverCacheDriver` allows you to cache discovered structures in a file. You should provide a `directory` parameter where all the cache files should be stored.

##### Laravel

The `LaravelDiscoverCacheDriver` will use the default Laravel cache. You can provide an optional `store` parameter to define the store to be used and an optional `prefix` parameter for the cache key.

##### Null

The `NullDiscoverCacheDriver` will not cache anything and can be used for testing purposes.

##### Your own

A cache driver can be built by extending the `DiscoverCacheDriver` interface:

```php
interface DiscoverCacheDriver
{
    public function has(string $id): bool;

    public function get(string $id): array;

    public function put(string $id, array $discovered): void;

    public function forget(string $id): void;
}
```

#### Without structure scouts

You can also use caching inline without the use of scouts, be aware warming up these caches in production is not possible:

```php
Discover::in(__DIR__)
   ->withCache(
      'Some identifier',
      new FileDiscoverCacheDriver('/path/to/temp/directory')
   )
    ->get();
```

### Parallel

Getting all structures in a bigger application can be slow due to many files being scanned.

Before running in parallel, make sure to install `amphp/parallel`

```shell
composer require amphp/parallel
```

The process can be sped up by parallelized scanning. You can enable this as such:

```php
Discover::in(__DIR__)->parallel()->get();
```

It is possible to set the number of files each process will scan:

```php
Discover::in(__DIR__)->parallel(100)->get();
```

By default, each process will scan 50 files.

### Chains

Often structures inherit other structures with extends and implementations. The package automatically includes these structures when discovering them. So for example

```php
class Request
{
}

class FormRequest extends Request
{
}

class UserFormRequest extends FormRequest
{
}
```

When using:

```php
Discover::in(__DIR__)->extending(Request::class)->get();
```

Both `FormRequest` and `UserFormRequest` will be found, and although `UserFormRequest` is not a direct descendant of `Request`, it is one through `FormRequest`.

You can disable this behavior for extending as such:

```php
Discover::in(__DIR__)->extendingWithoutChain(Request::class)
```

Or for implementing as such:

```php
Discover::in(__DIR__)->implementingWithoutChain(Request::class)
```

Resolving chains is a complicated and resource-heavy process. It can be completely disabled as such:

```php
Discover::in(__DIR__)->withoutChains()->extending(Request::class)->get();
```

### Full information

The output will be a reference string to the structure when discovering structures. Internally the package keeps track of a lot more information which can be helpful for all purposes. You can also retrieve this information as such:

```php
Discover::in(__DIR__)->full()->get();
```

Instead of returning an array of strings, now an array of `DiscoveredStructure` objects is returned. Let's go through the different types:

#### DiscoveredClass

Represents a class, the `$extends` and `$implements` properties address the direct extend and implements of the class. The `$extendsChain` and `$implementsChain` properties contain all extends and implements for the complete inheritance chain.

```php
class DiscoveredClass extends DiscoveredStructure
{
    public function __construct(
        string $name,
        string $file,
        string $namespace,
        public bool $isFinal,
        public bool $isAbstract,
        public bool $isReadonly,
        public ?string $extends,
        public array $implements,
        public array $attributes,
        public ?array $extendsChain = null,
        public ?array $implementsChain = null,
    ) {
    }
}
```

#### DiscoveredInterface

Represents a class, the `$extends` property addresses the direct extends of the interface. The `$extendsChain` property contains all extends for the whole inheritance chain.

```php
class DiscoveredInterface extends DiscoveredStructure
{
    public function __construct(
        string $name,
        string $file,
        string $namespace,
        public array $extends,
        public array $attributes,
        public ?array $extendsChain = null,
    ) {
    }
```

#### DiscoveredEnum

Represents an enum, the `$implements` property addresses the direct extends of the enum. The `$implementsChain` property contains all implements for the full inheritance chain. The `$type` property is an enum describing the type: `Unit`, `String`, and `Int`.

```php
class DiscoveredEnum extends DiscoveredStructure
{
    public function __construct(
        public string $name,
        public string $namespace,
        public string $file,
        public DiscoveredEnumType $type,
        public array $implements,
        public array $attributes,
        public ?array $implementsChain = null,
    ) {
    }
}
```

### DiscoveredTrait

Represents a discovered trait within the application.

```php
class DiscoveredTrait extends DiscoveredStructure
{
    public function __construct(
        public string $name,
        public string $namespace,
        public string $file,
    ) {
    }
}
```

### Parsers

The parser is responsible for parsing a file and returning a list of structures. The package comes with two parsers out of the box:

- `PhpTokenStructureParser`: Reads a PHP file, tokenizes it, and parses the tokens into structures.
- `ReflectionStructureParser`: Uses the PHP reflection API to read a file and parse it into structures.

By default, the `PhpTokenStructureParser` is used due to it being more robust, the `ReflectionStructureParser` is quite a bit faster but can completely fail the PHP process.

You can enable the `ReflectionStructureParser` as such:

```php
Discover::in(__DIR__)
   ->useReflection(
      basePath: '/path/to/project/root',
      rootNamespace: null
   )
   ->get();
```

You'll likely need to set the basePath to the root of your project, and optionally the root namespace of your project which will be prepended.

For default Laravel projects this would be:

```php
Discover::in(__DIR__)
   ->useReflection(basePath: base_path())
   ->get();
```

### Help? My structure cannot be found!

The internals of this package will scan all files within a directory and try to make a virtual map linking all structures with their extends, uses, and implementations.

Due to this file scanning, this map is incomplete if referenced structures are not being scanned.

For example, we scan for all classes extending Laravel's `Model` in our app directory, a lot of models have been found, but the `User` model is missing.

The reason why this is happening is that:

- The package searches in the app directory for classes extending `Model`
- `User` extends `Authenticatable`, which itself extends `Model`
- `Authenticatable` is stored within the `vendor/laravel/...` directory, which isn't being scanned
- The package does not know that `Authenticatable` extends `Model`
- `User` will not be found

A solution to this problem is to include the `laravel` directory in the scanning process.

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
- [Construct Finder](https://github.com/thephpleague/construct-finder) a big influence for this package
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
