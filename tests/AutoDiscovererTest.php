<?php

use Spatie\StructureDiscoverer\Cache\FileDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Data\DiscoveredAttribute;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredData;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\DiscoverConditions\DiscoverCondition;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeAttribute;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeClass;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeEnum;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeInterface;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeOtherClass;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeOtherInterface;
use Spatie\StructureDiscoverer\Tests\Fakes\FakeTrait;
use Spatie\StructureDiscoverer\Tests\Fakes\Nested\FakeNestedClass;
use Spatie\StructureDiscoverer\Tests\Fakes\Nested\FakeNestedInterface;
use Spatie\StructureDiscoverer\Tests\Fakes\OtherNested\OtherNestedClass;

beforeEach(function () {
});

it('can discover everything within a directory', function () {
    $found = Discover::in(__DIR__ . '/Fakes')->asString()->get();

    expect($found)->toEqualCanonicalizing([
        FakeAttribute::class,
        FakeClass::class,
        FakeEnum::class,
        FakeInterface::class,
        FakeOtherClass::class,
        FakeOtherInterface::class,
        FakeTrait::class,
        FakeNestedClass::class,
        FakeNestedInterface::class,
        OtherNestedClass::class,
    ]);
});

it('can only discover certain types', function (
    Discover $profile,
    array $expected,
) {
    $found = $profile->inDirectories(__DIR__ . '/Fakes')->asString()->get();

    expect($found)->toEqualCanonicalizing($expected);
})->with(
    [
        'classes' => [
            Discover::in()->classes(),
            [
                FakeAttribute::class,
                FakeClass::class,
                FakeOtherClass::class,
                FakeNestedClass::class,
                OtherNestedClass::class,
            ],
        ],
        'interfaces' => [
            Discover::in()->interfaces(),
            [
                FakeInterface::class,
                FakeOtherInterface::class,
                FakeNestedInterface::class,
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
    $found = Discover::in(__DIR__ . '/Fakes')->named('FakeClass', 'FakeInterface')->asString()->get();

    expect($found)->toEqualCanonicalizing([
        FakeClass::class,
        FakeInterface::class,
    ]);
});

it('can discover classes implementing', function () {
    $found = Discover::in(__DIR__ . '/Fakes')->implementing(FakeInterface::class)->asString()->get();

    expect($found)->toEqualCanonicalizing([
        FakeClass::class,
        FakeEnum::class,
    ]);
});

it('can discover interfaces extending by using implementing', function () {
    $found = Discover::in(__DIR__ . '/Fakes')
        ->interfaces()
        ->implementing(FakeOtherInterface::class)
        ->asString()
        ->get();

    expect($found)->toEqualCanonicalizing([
        FakeInterface::class,
    ]);
});

it('can discover classes extending', function () {
    $found = Discover::in(__DIR__ . '/Fakes')->extending(FakeOtherClass::class)->asString()->get();

    expect($found)->toEqualCanonicalizing([
        FakeClass::class,
    ]);
});

it('can discover classes with an attribute', function () {
    $found = Discover::in(__DIR__ . '/Fakes')->withAttribute(FakeAttribute::class)->asString()->get();

    expect($found)->toEqualCanonicalizing([
        FakeClass::class,
    ]);
});

it('can discover using complex conditions', function () {
    $found = Discover::in(__DIR__ . '/Fakes')
        ->any(
            DiscoverCondition::create()
                ->types(DiscoveredStructureType::ClassDefinition)
                ->implementing(FakeInterface::class),
            DiscoverCondition::create()
                ->types(DiscoveredStructureType::Enum)
        )
        ->asString()
        ->get();

    expect($found)->toEqualCanonicalizing([
        FakeClass::class,
        FakeEnum::class,
    ]);
});

it('can discover using a custom condition', function () {
    $condition = new class () extends DiscoverCondition {
        public function satisfies(DiscoveredData $discoveredData): bool
        {
            return $discoveredData instanceof DiscoveredClass && $discoveredData->name === 'FakeClass';
        }
    };

    $found = Discover::in(__DIR__ . '/Fakes')
        ->custom($condition)
        ->asString()
        ->get();

    expect($found)->toEqualCanonicalizing([
        FakeClass::class,
    ]);
});

it('can discover and receive the complete structure', function () {
    $found = Discover::in(__DIR__ . '/Fakes')->named('FakeClass')->get();

    expect($found)->toEqualCanonicalizing([
        new DiscoveredClass(
            name: 'FakeClass',
            file: 'Spatie\StructureDiscoverer\Tests\Fakes',
            namespace: '/Users/ruben/Spatie/structure-discoverer/tests/Fakes/FakeClass.php',
            isFinal: false,
            isAbstract: false,
            isReadonly: false,
            extends: FakeOtherClass::class,
            implements: [FakeInterface::class],
            attributes: [new DiscoveredAttribute(FakeAttribute::class)],
            extendsChain: [FakeOtherClass::class],
            implementsChain: [
                FakeInterface::class,
                FakeOtherInterface::class,
                FakeNestedInterface::class,
            ]
        ),
    ]);
});

it('can discover in multiple directories', function () {
    $found = Discover::in(
        __DIR__ . '/Fakes/Nested',
        __DIR__ . '/Fakes/OtherNested'
    )->asString()->get();

    expect($found)->toEqualCanonicalizing([
        FakeNestedClass::class,
        FakeNestedInterface::class,
        OtherNestedClass::class,
    ]);
});

it('can ignore files when discovering', function () {
    $found = Discover::in(__DIR__ . '/Fakes/Nested')
        ->ignoreFiles(__DIR__ . '/Fakes/Nested/FakeNestedInterface.php')
        ->asString()
        ->get();

    expect($found)->toEqualCanonicalizing([
        FakeNestedClass::class,
    ]);
});

it('can discover a lot of files using an async discoverer', function () {
    $found = Discover::in(__DIR__ . '/../vendor')
        ->async()
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
