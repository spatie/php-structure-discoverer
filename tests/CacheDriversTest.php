<?php

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\FileDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\LaravelDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\StaticDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;

it('can cache something', function (
    DiscoverCacheDriver $driver,
    Closure $specificHasTest
) {
    expect($driver->has('test'))->toBeFalse();
    expect($specificHasTest())->toBeFalse();

    $driver->put('test', ['a', 'b']);

    expect($driver->has('test'))->toBeTrue();
    expect($specificHasTest())->toBeTrue();

    expect($driver->get('test'))->toBe(['a', 'b']);

    $driver->forget('test');

    expect($driver->has('test'))->toBeFalse();
    expect($specificHasTest())->toBeFalse();
})->with([
    'laravel' => [
        new LaravelDiscoverCacheDriver(),
        fn () => cache()->has('discoverer-cache-test'),
    ],
    'laravel with prefix' => [
        new LaravelDiscoverCacheDriver(prefix: 'prefixed'),
        fn () => cache()->has('prefixed-discoverer-cache-test'),
    ],
    'laravel with store' => [
        new LaravelDiscoverCacheDriver(store: 'file'),
        fn () => cache()->driver('file')->has('discoverer-cache-test'),
    ],
    'file serialized' => [
        new FileDiscoverCacheDriver(__DIR__.'/temp'),
        fn () => file_exists(__DIR__.'/temp/discoverer-cache-test'),
    ],
    'file using php' => [
        new FileDiscoverCacheDriver(__DIR__.'/temp', serialize: false),
        fn () => file_exists(__DIR__.'/temp/discoverer-cache-test'),
    ],
    'file with alternative filename' => [
        new FileDiscoverCacheDriver(__DIR__.'/temp', filename: 'discovered.php'),
        fn () => file_exists(__DIR__.'/temp/discovered.php'),
    ],
    'static' => [
        new StaticDiscoverCacheDriver(),
        fn () => array_key_exists('test', StaticDiscoverCacheDriver::$entries),
    ],
]);

it('round-trips discovered structures through the Laravel cache driver', function () {
    // Laravel 13's `cache.serializable_classes` blocks unserialization of arbitrary
    // classes. Pre-serializing inside the driver stores a plain string in the cache,
    // so retrieval yields the original objects instead of __PHP_Incomplete_Class.
    config()->set('cache.serializable_classes', []);

    $driver = new LaravelDiscoverCacheDriver(store: 'file');
    $driver->forget('test');

    $discovered = [
        new DiscoveredClass(
            name: 'TestClass',
            file: '/test.php',
            namespace: 'Test',
            isFinal: false,
            isAbstract: false,
            isReadonly: false,
            extends: null,
            implements: [],
            attributes: [],
        ),
    ];

    $driver->put('test', $discovered);

    expect(cache()->driver('file')->get('discoverer-cache-test'))->toBeString();

    $retrieved = $driver->get('test');

    expect($retrieved[0])->toBeInstanceOf(DiscoveredClass::class);
    expect($retrieved[0]->name)->toBe('TestClass');

    $driver->forget('test');
});
