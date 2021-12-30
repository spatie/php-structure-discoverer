<?php

use Illuminate\Support\Facades\Cache;
use Spatie\LaravelAutoDiscoverer\DiscoverCache;
use Spatie\LaravelAutoDiscoverer\Facades\Discover;
use Spatie\LaravelAutoDiscoverer\ProfileConditions\ProfileCondition;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\CorruptClass;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeAsbtractClass;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeAttribute;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeClass;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeClassExtending;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeClassImplementing;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeClassUsingAttribute;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeClassUsingAttributeWithArguments;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeInterface;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeTrait;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\LevelUp\FakeLevelUpClass;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\OtherLevelUp\FakeOtherLevelUpClass;

beforeEach(function () {
    config()->set('auto-discoverer.base_path', __DIR__ . '/');
    config()->set('auto-discoverer.root_namespace', 'Spatie\LaravelAutoDiscoverer\Tests\\');
    config()->set('auto-discoverer.cache_directory', __DIR__ . '/');

    app(DiscoverCache::class)->clear();
    Discover::clearProfiles();
});

it('can discover everything within a directory', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqualCanonicalizing([
        FakeClass::class,
        FakeInterface::class,
        FakeLevelUpClass::class,
        FakeAttribute::class,
        FakeClassUsingAttribute::class,
        FakeClassUsingAttributeWithArguments::class,
        FakeOtherLevelUpClass::class,
        FakeClassExtending::class,
        FakeClassImplementing::class,
        FakeTrait::class,
        FakeAsbtractClass::class,
    ]);
});

it('can discover specific class names', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqual([FakeClass::class]);
});

it('can discover specific classes extending another class', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->extending(FakeAsbtractClass::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqual([FakeClassExtending::class]);
});

it('can discover specific classes implementing an interface', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->implementing(FakeInterface::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqual([FakeClassImplementing::class]);
});

it('can discover specific classes based upon closure', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->custom(fn (ReflectionClass $reflection) => $reflection->name === FakeClass::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqual([FakeClass::class]);
});

it('can discover specific classes based upon using an attribute', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->attribute(FakeAttribute::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqual([
        FakeClassUsingAttribute::class,
        FakeClassUsingAttributeWithArguments::class,
    ]);
});

it('can discover specific classes based upon using an attribute with specific arguments', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->attribute(FakeAttribute::class, [
            'POST',
        ])
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqual([
        FakeClassUsingAttributeWithArguments::class,
    ]);
});

it('can discover specific classes based upon using an attribute by inspection via closure', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->attribute(FakeAttribute::class, fn (FakeAttribute $fakeAttribute) => $fakeAttribute->method === 'POST')
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqual([
        FakeClassUsingAttributeWithArguments::class,
    ]);
});

it('can discover specific classes based upon multiple rules', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->attribute(FakeAttribute::class)
        ->named(FakeClassUsingAttributeWithArguments::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqual([
        FakeClassUsingAttributeWithArguments::class,
    ]);
});

it('can discover specific classes based upon sets of conditions', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->any(
            ProfileCondition::any(
                ProfileCondition::implementing(FakeInterface::class),
                ProfileCondition::named(FakeClass::class)
            ),
            ProfileCondition::combination(
                ProfileCondition::extending(FakeAsbtractClass::class),
                ProfileCondition::named(FakeClass::class),
            )
        )
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqualCanonicalizing([
        FakeClass::class,
        FakeClassImplementing::class,
    ]);
});

it('can discover specific classes with their reflection', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->returnReflection()
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqual([new ReflectionClass(FakeClass::class)]);
});

it('can have multiple discover profiles', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->implementing(FakeInterface::class)
        ->get(function (array $classes) use (&$foundA) {
            $foundA = $classes;
        });

    Discover::classes('b')
        ->within(__DIR__ . '/Fakes')
        ->extending(FakeAsbtractClass::class)
        ->get(function (array $classes) use (&$foundB) {
            $foundB = $classes;
        });

    Discover::run();

    expect($foundA)->toBe([FakeClassImplementing::class]);
    expect($foundB)->toBe([FakeClassExtending::class]);
});

it('can have multiple callbacks', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (array $classes) use (&$foundOne) {
            $foundOne = $classes;
        });

    Discover::get('a', function (array $classes) use (&$foundTwo) {
        $foundTwo = $classes;
    });

    Discover::run();

    expect($foundOne)->toBe([FakeClass::class]);
    expect($foundTwo)->toBe([FakeClass::class]);
});

it('can cache the output', function () {
    $profile = Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::cache();
    Discover::run();

    expect($found)->toEqual([FakeClass::class]);
    expect(app(DiscoverCache::class)->get($profile))->toEqual([FakeClass::class]);

    // We update the cache, so we're sure the cache is being used
    setProfileInCache($profile, [FakeClassImplementing::class]);

    Discover::run();

    expect($found)->toEqual([FakeClassImplementing::class]);
});

it('can cache the output with reflection returning', function () {
    $profile = Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->returnReflection()
        ->named(FakeClass::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::cache();
    Discover::run();

    expect($found)->toEqual([new ReflectionClass(FakeClass::class)]);
    expect(app(DiscoverCache::class)->get($profile))->toEqual([FakeClass::class]);

    // We update the cache, so we're sure the cache is being used
    setProfileInCache($profile, [FakeClassImplementing::class]);

    Discover::run();

    expect($found)->toEqual([new ReflectionClass(FakeClassImplementing::class)]);
});

it('can use a cached and non cached profile next to each other', function () {
    $profileA = Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (array $classes) use (&$foundA) {
            $foundA = $classes;
        });

    Discover::cache();

    $profileB = Discover::classes('b')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClassImplementing::class)
        ->get(function (array $classes) use (&$foundB) {
            $foundB = $classes;
        });

    Discover::run();

    expect(app(DiscoverCache::class)->has($profileA))->toBeTrue();
    expect(app(DiscoverCache::class)->has($profileB))->toBeFalse();

    expect($foundA)->toEqual([FakeClass::class]);
    expect($foundB)->toEqual([FakeClassImplementing::class]);
});

it('can discover in specific directories', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes/LevelUp')
        ->get(function (array $classes) use (&$foundA) {
            $foundA = $classes;
        });

    Discover::classes('a')
        ->within(__DIR__ . '/Fakes/OtherLevelUp')
        ->get(function (array $classes) use (&$foundB) {
            $foundB = $classes;
        });

    Discover::run();

    expect($foundA)->toEqual([
        FakeLevelUpClass::class,
    ]);

    expect($foundB)->toEqual([
        FakeOtherLevelUpClass::class,
    ]);
});

it('can use a different base path and root namespace', function () {
    config()->set('auto-discoverer.base_path', __DIR__ . '/Fakes/LevelUp');
    config()->set('auto-discoverer.root_namespace', 'Spatie\LaravelAutoDiscoverer\Tests\Fakes\LevelUp\\');

    Discover::classes('a')
        ->within(__DIR__ . '/Fakes/LevelUp')
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqual([
        FakeLevelUpClass::class,
    ]);
});

it('can ignore certain files', function () {
    config()->set('auto-discoverer.ignored_files', [__DIR__ . '/Fakes/FakeClass.php']);

    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toBeEmpty();
});

it('ignores corrupt classes', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class, CorruptClass::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqual([FakeClass::class]);
});

it('can discover without specifying a directory', function () {
    Discover::classes('a')
        ->named(FakeClass::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqual([FakeClass::class]);
});

it('can discover using a Facade', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toEqual([FakeClass::class]);
});

// TODO: check if we van register this package earlier
// TODO: port package to settings, morph map generator, event sourcing
