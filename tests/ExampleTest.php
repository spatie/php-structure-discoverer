<?php

use Spatie\LaravelAutoDiscoverer\Discoverer;
use Spatie\LaravelAutoDiscoverer\ProfileReferences\ProfileReference;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeAsbtractClass;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeClass;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeClassExtending;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeClassImplementing;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeInterface;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\FakeTrait;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\LevelUp\FakeLevelUpClass;

beforeEach(function () {
    config()->set('auto-discoverer.base_path', __DIR__ . '/');
    config()->set('auto-discoverer.root_namespace', 'Spatie\LaravelAutoDiscoverer\Tests\\');

    Discoverer::clearProfiles();
});

it('can discover everything within a directory', function () {
    Discoverer::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discoverer::run();

    $this->assertEquals([
        FakeClass::class,
        FakeInterface::class,
        FakeLevelUpClass::class,
        FakeClassExtending::class,
        FakeClassImplementing::class,
        FakeTrait::class,
        FakeAsbtractClass::class,
    ], $found);
});

it('can discover specific class names', function () {
    Discoverer::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discoverer::run();

    $this->assertEquals([
        FakeClass::class,
    ], $found);
});

it('can discover specific classes extending another class', function () {
    Discoverer::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->extending(FakeAsbtractClass::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discoverer::run();

    $this->assertEquals([
        FakeClassExtending::class,
    ], $found);
});

it('can discover specific classes implementing an interface', function () {
    Discoverer::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->implementing(FakeInterface::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discoverer::run();

    $this->assertEquals([
        FakeClassImplementing::class,
    ], $found);
});

it('can discover specific classes based upon closure', function () {
    Discoverer::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->custom(fn(ReflectionClass $reflection) => $reflection->name === FakeClass::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discoverer::run();

    $this->assertEquals([
        FakeClass::class,
    ], $found);
});

it('can discover specific classes based upon multiple rules', function () {
    Discoverer::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->implementing(FakeInterface::class)
        ->extending(FakeAsbtractClass::class)
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discoverer::run();

    $this->assertEquals([
        FakeClassExtending::class,
        FakeClassImplementing::class,
    ], $found);
});

it('can discover specific classes based upon sets of conditions', function () {
    Discoverer::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->any(
            ProfileReference::implementing(FakeInterface::class),
            ProfileReference::named(FakeClass::class)
        )
        ->combination(
            ProfileReference::extending(FakeAsbtractClass::class),
            ProfileReference::named(FakeClass::class),
        )
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discoverer::run();

    $this->assertEquals([
        FakeClass::class,
        FakeClassImplementing::class,
    ], $found);
});

it('can discover specific classes with their reflection', function () {
    Discoverer::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->returnReflection()
        ->get(function (array $classes) use (&$found) {
            $found = $classes;
        });

    Discoverer::run();

    $this->assertEquals([
        new ReflectionClass(FakeClass::class),
    ], $found);
});

it('can have multiple discover profiles', function () {
    Discoverer::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->implementing(FakeInterface::class)
        ->get(function (array $classes) use (&$foundA) {
            $foundA = $classes;
        });

    Discoverer::classes('b')
        ->within(__DIR__ . '/Fakes')
        ->extending(FakeAsbtractClass::class)
        ->get(function (array $classes) use (&$foundB) {
            $foundB = $classes;
        });

    Discoverer::run();

    $this->assertEquals([
        FakeClassImplementing::class
    ], $foundA);

    $this->assertEquals([
        FakeClassExtending::class
    ], $foundB);
});

it('can have multiple callbacks', function () {
    Discoverer::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (array $classes) use (&$foundOne) {
            $foundOne = $classes;
        });

    Discoverer::get('a',function (array $classes) use (&$foundTwo) {
        $foundTwo = $classes;
    } );

    Discoverer::run();

    $this->assertEquals([
        FakeClass::class,
    ], $foundOne);

    $this->assertEquals([
        FakeClass::class,
    ], $foundTwo);
});

// TODO add tests for caching
// Todod add tests for caching and reflection
// TODO add tests with mutliple dirs, other root name space and base path
// Add tests with classes that cannot be initaited (no reflection possible)
// Add tests with composer helpers.php file
// TODO: add docs
// TODO: check if we van register this package earlier
// TODO: port package to settings, morph map generator, event sourcing
// TODO: add profile reference for attribute
// TODO: add profile reference that negates a group (NOT)

