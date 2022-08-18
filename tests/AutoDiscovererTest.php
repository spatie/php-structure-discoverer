<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\LaravelAutoDiscoverer\Discover;
use Spatie\LaravelAutoDiscoverer\DiscoverCache;
use Spatie\LaravelAutoDiscoverer\Exceptions\CallbackRequired;
use Spatie\LaravelAutoDiscoverer\ProfileConditions\ProfileCondition;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\Failing\CorruptClass;
use Spatie\LaravelAutoDiscoverer\Tests\Fakes\Failing\FailingClass;
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
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->basePath(__DIR__ . '/')
        ->within(__DIR__ . '/Fakes')
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqualCanonicalizing([
        FailingClass::class,
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
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqual([FakeClass::class]);
});

it('can discover specific classes extending another class', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->extending(FakeAsbtractClass::class)
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqual([FakeClassExtending::class]);
});

it('can discover specific classes implementing an interface', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->implementing(FakeInterface::class)
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqual([FakeClassImplementing::class]);
});

it('can discover specific classes based upon closure', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->custom(fn (ReflectionClass $reflection) => $reflection->name === FakeClass::class)
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqual([FakeClass::class]);
});

it('can discover specific classes based upon using an attribute', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->attribute(FakeAttribute::class)
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqual([
        FakeClassUsingAttribute::class,
        FakeClassUsingAttributeWithArguments::class,
    ]);
});

it('can discover specific classes based upon using an attribute with specific arguments', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->attribute(FakeAttribute::class, [
            'POST',
        ])
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqual([
        FakeClassUsingAttributeWithArguments::class,
    ]);
});

it('can discover specific classes based upon using an attribute by inspection via closure', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->attribute(FakeAttribute::class, fn (FakeAttribute $fakeAttribute) => $fakeAttribute->method === 'POST')
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqual([
        FakeClassUsingAttributeWithArguments::class,
    ]);
});

it('can discover specific classes based upon multiple rules', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->attribute(FakeAttribute::class)
        ->named(FakeClassUsingAttributeWithArguments::class)
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqual([
        FakeClassUsingAttributeWithArguments::class,
    ]);
});

it('can discover specific classes based upon sets of conditions', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
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
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqualCanonicalizing([
        FakeClass::class,
        FakeClassImplementing::class,
    ]);
});

it('can discover specific classes with their reflection', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->returnReflection()
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqual([new ReflectionClass(FakeClass::class)]);
});

it('can have multiple discover profiles', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->implementing(FakeInterface::class)
        ->get(function (Collection $classes) use (&$foundA) {
            $foundA = $classes;
        });

    Discover::classes('b')
        ->within(__DIR__ . '/Fakes')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->extending(FakeAsbtractClass::class)
        ->get(function (Collection $classes) use (&$foundB) {
            $foundB = $classes;
        });

    Discover::run();

    expect($foundA->all())->toBe([FakeClassImplementing::class]);
    expect($foundB->all())->toBe([FakeClassExtending::class]);
});

it('can have multiple callbacks', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (Collection $classes) use (&$foundOne) {
            $foundOne = $classes;
        });

    Discover::addCallback('a', function (Collection $classes) use (&$foundTwo) {
        $foundTwo = $classes;
    });

    Discover::run();

    expect($foundOne->all())->toBe([FakeClass::class]);
    expect($foundTwo->all())->toBe([FakeClass::class]);
});

it('can get the discovered classes later on in the process', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(fn(Collection $classes) => $classes);

    Discover::run();

    expect(Discover::get('a')->all())->toBe([FakeClass::class]);
});

it('cannot get the discovered classes before the discoverer has ran', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class);

    expect(Discover::get('a')->all())->toBe([FakeClass::class]);
})->expectException(CallbackRequired::class);

it('can cache the output', function () {
    $profile = Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::cache();
    Discover::run();

    expect($found->all())->toEqual([FakeClass::class]);
    expect(app(DiscoverCache::class)->get($profile))->toEqual([FakeClass::class]);

    // We update the cache, so we're sure the cache is being used
    setProfileInCache($profile, [FakeClassImplementing::class]);

    Discover::run();

    expect($found->all())->toEqual([FakeClassImplementing::class]);
});

it('can cache the output with reflection returning', function () {
    $profile = Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->returnReflectionWhenCached()
        ->named(FakeClass::class)
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::cache();
    Discover::run();

    expect($found->all())->toEqual([FakeClass::class]);
    expect(app(DiscoverCache::class)->get($profile))->toEqual([FakeClass::class]);

    // We update the cache, so we're sure the cache is being used
    setProfileInCache($profile, [FakeClassImplementing::class]);

    Discover::run();

    expect($found->all())->toEqual([new ReflectionClass(FakeClassImplementing::class)]);
});

it('can use a cached and non cached profile next to each other', function () {
    $profileA = Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (Collection $classes) use (&$foundA) {
            $foundA = $classes;
        });

    Discover::cache();

    $profileB = Discover::classes('b')
        ->within(__DIR__ . '/Fakes')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->named(FakeClassImplementing::class)
        ->get(function (Collection $classes) use (&$foundB) {
            $foundB = $classes;
        });

    Discover::run();

    expect(app(DiscoverCache::class)->has($profileA))->toBeTrue();
    expect(app(DiscoverCache::class)->has($profileB))->toBeFalse();

    expect($foundA->all())->toEqual([FakeClass::class]);
    expect($foundB->all())->toEqual([FakeClassImplementing::class]);
});

it('can discover in specific directories', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes/LevelUp')
        ->get(function (Collection $classes) use (&$foundA) {
            $foundA = $classes;
        });

    Discover::classes('b')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes/OtherLevelUp')
        ->get(function (Collection $classes) use (&$foundB) {
            $foundB = $classes;
        });

    Discover::run();

    expect($foundA->all())->toEqual([FakeLevelUpClass::class])
        ->and($foundB->all())->toEqual([FakeOtherLevelUpClass::class]);
});

it('can use a different base path and root namespace', function () {
    Discover::classes('a')
        ->basePath(__DIR__ . '/Fakes/LevelUp')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\Fakes\LevelUp\\')
        ->within(__DIR__ . '/Fakes/LevelUp')
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqual([
        FakeLevelUpClass::class,
    ]);
});

it('can ignore certain files', function () {
    config()->set('auto-discoverer.ignored_files', [__DIR__ . '/Fakes/FakeClass.php']);

    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found)->toBeEmpty();
});

it('ignores corrupt classes', function () {
    Discover::classes('a')
        ->within(__DIR__ . '/Fakes')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->named(FakeClass::class, CorruptClass::class)
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqual([FakeClass::class]);
});

it('can discover using a Facade', function () {
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->named(FakeClass::class)
        ->get(function (Collection $classes) use (&$found) {
            $found = $classes;
        });

    Discover::run();

    expect($found->all())->toEqual([FakeClass::class]);
});

it('can discover a profile instantly without affecting others', function (){
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->implementing(FakeInterface::class)
        ->get(function (Collection $classes) use (&$foundA) {
            $foundA = $classes;
        });

    Discover::classes('b')
        ->within(__DIR__ . '/Fakes')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->extending(FakeAsbtractClass::class)
        ->get(function (Collection $classes) use (&$foundB) {
            $foundB = $classes;
        });

    expect($foundA)->toBeNull();
    expect(Discover::getInstantly('b')->all())->toBe([FakeClassExtending::class]);
    expect($foundB->all())->toBe([FakeClassExtending::class]);

    Discover::run();

    expect($foundA->all())->toBe([FakeClassImplementing::class]);
    expect($foundB->all())->toBe([FakeClassExtending::class]);
});

it('only discovers profiles with registered callbacks', function (){
    Discover::classes('a')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->within(__DIR__ . '/Fakes')
        ->implementing(FakeInterface::class);

    Discover::classes('b')
        ->within(__DIR__ . '/Fakes')
        ->rootNamespace('Spatie\LaravelAutoDiscoverer\Tests\\')
        ->extending(FakeAsbtractClass::class)
        ->get(fn (Collection $classes) => $classes);

    Discover::run();

    /** @var \Spatie\LaravelAutoDiscoverer\DiscoverProfilesCollection $profiles */
    $profiles = invade(Discover::$manager)->profiles;

    expect($profiles->get('a')->isDiscovered())->toBeFalse();
    expect($profiles->get('b')->isDiscovered())->toBeTrue();

    Discover::addCallback('a', fn (Collection $classes) => $classes);
    Discover::run();

    expect($profiles->get('a')->isDiscovered())->toBeTrue();
    expect($profiles->get('b')->isDiscovered())->toBeTrue();
});
