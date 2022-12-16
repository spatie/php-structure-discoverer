<?php

use Spatie\StructureDiscoverer\Cache\FileDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\StaticDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Data\DiscoveredAttribute;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\DiscoverConditions\DiscoverCondition;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;
use Spatie\StructureDiscoverer\Support\StructureScoutManager;
use Spatie\StructureDiscoverer\Tests\Fakes\Dependers\FakeClassDepender;
use Spatie\StructureDiscoverer\Tests\Fakes\Dependers\FakeInterfaceDepender;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeAttribute;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeChildClass;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeEnum;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeChildInterface;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeRootClass;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeRootInterface;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeTrait;
use Spatie\StructureDiscoverer\Tests\Fakes\Nested\FakeNestedClass;
use Spatie\StructureDiscoverer\Tests\Fakes\Nested\FakeNestedInterface;
use Spatie\StructureDiscoverer\Tests\Fakes\OtherNested\FakeOtherNestedClass;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeSubChildClass;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeSubChildInterface;
use Spatie\StructureDiscoverer\Tests\Stubs\StubStructureScout;

it('can discover everything within a directory', function () {
    $found = Discover::in(__DIR__ . '/Fakes')->get();

    expect($found)->toEqualCanonicalizing([
        FakeAttribute::class,
        FakeChildClass::class,
        FakeEnum::class,
        FakeChildInterface::class,
        FakeRootClass::class,
        FakeRootInterface::class,
        FakeTrait::class,
        FakeNestedClass::class,
        FakeNestedInterface::class,
        FakeOtherNestedClass::class,
        FakeSubChildInterface::class,
        FakeSubChildClass::class,
        FakeClassDepender::class,
        FakeInterfaceDepender::class
    ]);
});

it('can only discover certain types', function (
    Discover $profile,
    array $expected,
) {
    $found = $profile->inDirectories(__DIR__ . '/Fakes')->get();

    expect($found)->toEqualCanonicalizing($expected);
})->with(
    [
        'classes' => [
            Discover::in()->classes(),
            [
                FakeAttribute::class,
                FakeChildClass::class,
                FakeRootClass::class,
                FakeNestedClass::class,
                FakeOtherNestedClass::class,
                FakeSubChildClass::class,
                FakeClassDepender::class,
            ],
        ],
        'interfaces' => [
            Discover::in()->interfaces(),
            [
                FakeChildInterface::class,
                FakeRootInterface::class,
                FakeNestedInterface::class,
                FakeSubChildInterface::class,
                FakeInterfaceDepender::class,
            ],
        ],
        'enums' => [
            Discover::in()->enums(),
            [
                FakeEnum::class,
            ],
        ],
        'traits' => [
            Discover::in()->traits(),
            [
                FakeTrait::class,
            ],
        ],
        'multiple' => [
            Discover::in()->types(
                DiscoveredStructureType::Enum,
                DiscoveredStructureType::Trait
            ),
            [
                FakeEnum::class,
                FakeTrait::class,
            ],
        ],
    ]
);

it('can discover using a name', function () {
    $found = Discover::in(__DIR__ . '/Fakes')->named('FakeChildClass', 'FakeChildInterface')->get();

    expect($found)->toEqualCanonicalizing([
        FakeChildClass::class,
        FakeChildInterface::class,
    ]);
});

it('can discover everything implementing an interface', function () {
    $found = Discover::in(__DIR__ . '/Fakes')->implementing(FakeChildInterface::class)->get();

    expect($found)->toEqualCanonicalizing([
        FakeSubChildClass::class,
        FakeChildClass::class,
        FakeEnum::class,
        FakeSubChildInterface::class,
    ]);
});

it('can discover everything implementing an interface without chains', function () {
    $found = Discover::in(__DIR__ . '/Fakes')->implementingWithoutChain(FakeChildInterface::class)->get();

    expect($found)->toEqualCanonicalizing([
        FakeChildClass::class,
        FakeEnum::class,
        FakeSubChildInterface::class,
    ]);
});

it('can discover everything implementing a non included interface', function () {
    $found = Discover::in(__DIR__ . '/Fakes/Dependers')->implementingWithoutChain(FakeRootInterface::class)->get();

    expect($found)->toEqualCanonicalizing([
        FakeInterfaceDepender::class,
    ]);
});

it('can discover interfaces extending by using implementing', function () {
    $found = Discover::in(__DIR__ . '/Fakes')
        ->interfaces()
        ->implementing(FakeRootInterface::class)
        ->get();

    expect($found)->toEqualCanonicalizing([
        FakeChildInterface::class,
        FakeSubChildInterface::class,
        FakeInterfaceDepender::class,
    ]);
});

it('can discover classes extending', function () {
    $found = Discover::in(__DIR__ . '/Fakes')->extending(FakeRootClass::class)->get();

    expect($found)->toEqualCanonicalizing([
        FakeChildClass::class,
        FakeSubChildClass::class,
        FakeClassDepender::class,
    ]);
});

it('can discover classes extending without chain', function () {
    $found = Discover::in(__DIR__ . '/Fakes')->extendingWithoutChain(FakeRootClass::class)->get();

    expect($found)->toEqualCanonicalizing([
        FakeChildClass::class,
        FakeClassDepender::class,
    ]);
});


it('can discover classes extending a non included class', function () {
    $found = Discover::in(__DIR__ . '/Fakes/Dependers')->extendingWithoutChain(FakeRootClass::class)->get();

    expect($found)->toEqualCanonicalizing([
        FakeClassDepender::class,
    ]);
});

it('can discover classes with an attribute', function () {
    $found = Discover::in(__DIR__ . '/Fakes')->withAttribute(FakeAttribute::class)->get();

    expect($found)->toEqualCanonicalizing([
        FakeChildClass::class,
    ]);
});

it('can discover using complex conditions', function () {
    $found = Discover::in(__DIR__ . '/Fakes')
        ->any(
            DiscoverCondition::create()
                ->types(DiscoveredStructureType::ClassDefinition)
                ->implementing(FakeChildInterface::class),
            DiscoverCondition::create()
                ->types(DiscoveredStructureType::Enum)
        )
        ->get();

    expect($found)->toEqualCanonicalizing([
        FakeChildClass::class,
        FakeEnum::class,
        FakeSubChildClass::class,
    ]);
});

it('can discover using a custom condition', function () {
    $condition = new class () extends DiscoverCondition {
        public function satisfies(DiscoveredStructure $discoveredData): bool
        {
            return $discoveredData instanceof DiscoveredClass && $discoveredData->name === 'FakeChildClass';
        }
    };

    $found = Discover::in(__DIR__ . '/Fakes')
        ->custom($condition)
        ->get();

    expect($found)->toEqualCanonicalizing([
        FakeChildClass::class,
    ]);
});

it('can discover using a custom closure condition', function () {
    $found = Discover::in(__DIR__ . '/Fakes')
        ->custom(fn(DiscoveredStructure $discoveredData) => $discoveredData instanceof DiscoveredClass && $discoveredData->name === 'FakeChildClass')
        ->get();

    expect($found)->toEqualCanonicalizing([
        FakeChildClass::class,
    ]);
});

it('can discover and receive the complete structure', function () {
    $found = Discover::in(__DIR__ . '/Fakes')->named('FakeChildClass')->full()->get();

    expect($found)->toEqualCanonicalizing([
        new DiscoveredClass(
            name: 'FakeChildClass',
            file: 'Spatie\StructureDiscoverer\Tests\Fakes',
            namespace: '/Users/ruben/Spatie/structure-discoverer/tests/Fakes/FakeChildClass.php',
            isFinal: false,
            isAbstract: false,
            isReadonly: false,
            extends: FakeRootClass::class,
            implements: [FakeChildInterface::class],
            attributes: [new DiscoveredAttribute(FakeAttribute::class)],
            extendsChain: [FakeRootClass::class],
            implementsChain: [
                FakeChildInterface::class,
                FakeRootInterface::class,
                FakeNestedInterface::class,
            ]
        ),
    ]);
});

it('can discover in multiple directories', function () {
    $found = Discover::in(
        __DIR__ . '/Fakes/Nested',
        __DIR__ . '/Fakes/OtherNested'
    )->get();

    expect($found)->toEqualCanonicalizing([
        FakeNestedClass::class,
        FakeNestedInterface::class,
        FakeOtherNestedClass::class,
    ]);
});

it('can ignore files when discovering', function () {
    $found = Discover::in(__DIR__ . '/Fakes/Nested')
        ->ignoreFiles(__DIR__ . '/Fakes/Nested/FakeNestedInterface.php')
        ->get();

    expect($found)->toEqualCanonicalizing([
        FakeNestedClass::class,
    ]);
});

it('can discover a lot of files using an async discoverer', function () {
    $found = Discover::in(__DIR__ . '/../vendor')
        ->parallel()
        ->get();

    expect(count($found))->toBeGreaterThan(5000);
});

it('can cache discovered settings', function () {
    $cache = new FileDiscoverCacheDriver(__DIR__ . '/temp/');

    $found = Discover::in(__DIR__ . '/Fakes')
        ->cache('cached', $cache)
        ->get();

    expect($cache->has('cached'))->toBeTrue();
    expect($found)->toEqual($cache->get('cached'));

    $cache->forget('cached');

    expect($cache->has('cached'))->toBeFalse();
});

it('can use cached discovered settings', function () {
    $nestedFound = Discover::in(__DIR__ . '/Fakes/Nested')->get();
    $expectedFound = Discover::in(__DIR__ . '/Fakes')->get();

    $cache = new FileDiscoverCacheDriver(__DIR__ . '/temp/');

    $cache->put('cached', $nestedFound);

    $fakeFound = Discover::in(__DIR__ . '/Fakes')
        ->cache('cached', new FileDiscoverCacheDriver(__DIR__ . '/temp/'))
        ->get();

    expect($fakeFound)->not()->toEqual($expectedFound);
    expect($fakeFound)->toEqual($nestedFound);

    $cache->forget('cached');
});

it('can disable chains completely', function () {
    $foundClassesA = Discover::in(__DIR__ . '/Fakes')
        ->withoutChains()
        ->extendingWithoutChain(FakeRootClass::class)
        ->get();

    $foundClassesB = Discover::in(__DIR__ . '/Fakes')
        ->withoutChains()
        ->extending(FakeRootClass::class)
        ->get();

    expect($foundClassesA)
        ->toEqualCanonicalizing([FakeChildClass::class, FakeClassDepender::class,])
        ->toEqualCanonicalizing($foundClassesB);

    $foundInterfacesA = Discover::in(__DIR__ . '/Fakes')
        ->withoutChains()
        ->implementingWithoutChain(FakeChildInterface::class)
        ->get();

    $foundInterfacesB = Discover::in(__DIR__ . '/Fakes')
        ->withoutChains()
        ->implementing(FakeChildInterface::class)
        ->get();

    expect($foundInterfacesA)
        ->toEqualCanonicalizing([
            FakeChildClass::class,
            FakeEnum::class,
            FakeSubChildInterface::class,
        ])
        ->toEqualCanonicalizing($foundInterfacesB);
});

it('can use a discoverer with cache', function () {
    StaticDiscoverCacheDriver::clear();

    $found = StubStructureScout::create()->get();

    expect($found)->toBe([FakeEnum::class]);

    // Replace cache
    StaticDiscoverCacheDriver::$entries['stub'] = [FakeRootClass::class];

    $found = StubStructureScout::create()->get();

    expect($found)->toBe([FakeRootClass::class]);
});

it('can warm and clear discoverers', function () {
    StructureScoutManager::cache([__DIR__ . '/Stubs']);

    expect(StaticDiscoverCacheDriver::$entries)->toHaveKey('stub');
    expect(StaticDiscoverCacheDriver::$entries['stub'])->toBe([FakeEnum::class]);

    StructureScoutManager::clear([__DIR__ . '/Stubs']);

    expect(StaticDiscoverCacheDriver::$entries)->not()->toHaveKey('stub');
});
