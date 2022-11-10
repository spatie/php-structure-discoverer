<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Arr;
use Spatie\StructureDiscoverer\Data\DiscoveredAttribute;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredData;
use Spatie\StructureDiscoverer\Data\DiscoveredEnum;
use Spatie\StructureDiscoverer\Data\DiscoveredInterface;
use Spatie\StructureDiscoverer\Data\DiscoveredTrait;
use Spatie\StructureDiscoverer\Enums\DiscoveredEnumType;
use Spatie\StructureDiscoverer\TokenParsers\FileTokenParser;

function getDiscovered(
    string $definition,
    string $filename = 'file.php',
): array {
    return app(FileTokenParser::class)->execute($filename, "<?php{$definition}");
}

function getDiscoveredStructure(
    string $definition,
    string $filename = 'file.php',
    ?string $structure = null,
): DiscoveredEnum|DiscoveredClass|DiscoveredInterface|DiscoveredTrait {
    return Arr::first(
        getDiscovered($definition, $filename),
        $structure ? fn(DiscoveredData $discovered) => $discovered->name === $structure : null,
    );
}

/**
 * Namespaces
 */

it('can resolve a namespace', function () {
    $definition = <<<'PHP'
    namespace Spatie;

    class BaseClass{}
PHP;

    expect(getDiscoveredStructure($definition))->namespace->toBe('Spatie');
});

it('can resolve a multi segment namespace', function () {
    $definition = <<<'PHP'
    namespace Spatie\PhpAutoDiscoverer;

    class BaseClass{}
PHP;

    expect(getDiscoveredStructure($definition))->namespace->toBe("Spatie\PhpAutoDiscoverer");
});

/**
 * Imports
 */

it('can import a reference', function () {
    $definition = <<<'PHP'
    namespace Spatie\PhpAutoDiscoverer;

    use Illuminate\Contracts\Support\Arrayable;

    class BaseClass extends Arrayable{}
PHP;

    expect(getDiscoveredStructure($definition))->extends->toBe(Arrayable::class);
});

it('can import a global reference', function () {
    $definition = <<<'PHP'
    namespace Spatie\PhpAutoDiscoverer;

    use Exception;

    class BaseClass extends Exception{}
PHP;

    expect(getDiscoveredStructure($definition))->extends->toBe(Exception::class);
});


it('can import as an alias', function () {
    $definition = <<<'PHP'
    namespace Spatie\PhpAutoDiscoverer;

    use Illuminate\Contracts\Support\Arrayable as OtherArrayable;

    class BaseClass extends OtherArrayable{}
PHP;

    expect(getDiscoveredStructure($definition))->extends->toBe(Arrayable::class);
});

it('can have multiple import statements', function () {
    $definition = <<<'PHP'
    namespace Spatie\PhpAutoDiscoverer;

    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Contracts\Support\Jsonable;

    class BaseClass implements Arrayable, Jsonable {}
PHP;

    expect(getDiscoveredStructure($definition))->implements->toBe([
        Arrayable::class,
        Jsonable::class,
    ]);
});

it('can combine import statements', function () {
    $definition = <<<'PHP'
    namespace Spatie\PhpAutoDiscoverer;

    use Illuminate\Contracts\Support\Arrayable, Illuminate\Contracts\Support\Jsonable;

    class BaseClass implements Arrayable, Jsonable {}
PHP;

    expect(getDiscoveredStructure($definition))->implements->toBe([
        Arrayable::class,
        Jsonable::class,
    ]);
});

it('can combine import statements with aliases', function () {
    $definition = <<<'PHP'
    namespace Spatie\PhpAutoDiscoverer;

    use Illuminate\Contracts\Support\Arrayable as OtherArrayable, Illuminate\Contracts\Support\Responsable , Illuminate\Contracts\Support\Jsonable as OtherJsonAble;

    class BaseClass implements OtherArrayable, Responsable, OtherJsonAble {}
PHP;

    expect(getDiscoveredStructure($definition))->implements->toBe([
        Arrayable::class,
        Responsable::class,
        Jsonable::class,
    ]);
});

/**
 * References
 */

it('can reference in the same namespace', function () {
    $definition = <<<'PHP'
    namespace Spatie\PhpAutoDiscoverer;

    class BaseClass extends ParentClass{}
PHP;

    expect(getDiscoveredStructure($definition))->extends->toBe('Spatie\PhpAutoDiscoverer\ParentClass');
});

it('can reference in a sub namespace', function () {
    $definition = <<<'PHP'
    namespace Spatie\PhpAutoDiscoverer;

    class BaseClass extends Parents\ParentClass{}
PHP;

    expect(getDiscoveredStructure($definition))->extends->toBe('Spatie\PhpAutoDiscoverer\Parents\ParentClass');
});

it('can reference an external class', function () {
    $definition = <<<'PHP'
    namespace Spatie\PhpAutoDiscoverer;

    class BaseClass extends \Illuminate\Contracts\Support\Arrayable{}
PHP;

    expect(getDiscoveredStructure($definition))->extends->toBe(Arrayable::class);
});

it('can reference the global namespace', function () {
    $definition = <<<'PHP'
    namespace Spatie\PhpAutoDiscoverer;

    class BaseClass extends \Exception{}
PHP;

    expect(getDiscoveredStructure($definition))->extends->toBe(Exception::class);
});

it('can reference using the namespace keyword', function () {
    $definition = <<<'PHP'
    namespace Spatie\PhpAutoDiscoverer;

    class BaseClass extends namespace\OtherClass{}
PHP;

    expect(getDiscoveredStructure($definition))->extends->toBe('Spatie\PhpAutoDiscoverer\OtherClass');
});

/**
 * Enums
 */

it('can resolve unit enums', function () {
    $definition = <<<'PHP'
    enum UnitEnum{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredEnum::class)
        ->type->toEqual(DiscoveredEnumType::Unit)
        ->name->toEqual('UnitEnum')
        ->implements->toBeEmpty();
});

it('can resolve string enums', function () {
    $definition = <<<'PHP'
    enum StringEnum: string{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredEnum::class)
        ->type->toEqual(DiscoveredEnumType::String)
        ->name->toEqual('StringEnum');
});

it('can resolve int enums', function () {
    $definition = <<<'PHP'
    enum IntEnum: int{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredEnum::class)
        ->type->toEqual(DiscoveredEnumType::Int)
        ->name->toEqual('IntEnum');
});

it('can resolve implemented interfaces for an enum', function () {
    $definition = <<<'PHP'
    use Illuminate\Contracts\Support\Arrayable;

    enum UnitEnum implements EnumInterface, Arrayable {}
PHP;

    $discovered = getDiscoveredStructure($definition, structure: 'UnitEnum');

    expect($discovered)
        ->toBeInstanceOf(DiscoveredEnum::class)
        ->implements->toContain('EnumInterface', Arrayable::class);
});

/**
 * Traits
 */

it('can resolve traits', function () {
    $definition = <<<'PHP'
    trait SomeTrait{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredTrait::class)
        ->name->toEqual('SomeTrait');
});

/**
 * Interfaces
 */

it('can resolve interfaces', function () {
    $definition = <<<'PHP'
    interface BaseInterface{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredInterface::class)
        ->name->toEqual('BaseInterface')
        ->extends->toBeEmpty();
});

it('can resolve extended interfaces for an interface', function () {
    $definition = <<<'PHP'
    use Illuminate\Contracts\Support\Arrayable;

    interface BaseInterface extends AnotherInterface, Arrayable {};
PHP;

    expect(getDiscoveredStructure($definition, structure: 'BaseInterface'))
        ->toBeInstanceOf(DiscoveredInterface::class)
        ->extends->toContain('AnotherInterface', Arrayable::class);
});

/**
 * Classes
 */

it('can resolve classes', function () {
    $definition = <<<'PHP'
    class BaseClass{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredClass::class)
        ->name->toEqual('BaseClass')
        ->extends->toBeNull()
        ->implements->toBeEmpty()
        ->isFinal->toBeFalse();
});

it('can resolve an extended class for a class', function () {
    $definition = <<<'PHP'
    class BaseClass extends InheritedClass{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredClass::class)
        ->extends->toEqual('InheritedClass');
});

it('can resolve implemented interfaces for a class', function () {
    $definition = <<<'PHP'
    use Illuminate\Contracts\Support\Arrayable;

    class BaseClass implements ClassInterface, Arrayable {}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredClass::class)
        ->implements->toContain('ClassInterface', Arrayable::class);
});

it('can resolve a final clas', function () {
    $definition = <<<'PHP'
    final class BaseClass{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredClass::class)
        ->isFinal->toBeTrue();
});

it('can resolve an abstract class', function () {
    $definition = <<<'PHP'
    abstract class BaseClass{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredClass::class)
        ->isAbstract->toBeTrue();
});

/**
 * Attributes
 */

it('can resolve an attribute', function () {
    $definition = <<<'PHP'
    #[BaseAttribute]
    class BaseClass{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredClass::class)
        ->attributes->toEqual([new DiscoveredAttribute('BaseAttribute')]);
});

it('can resolve an attribute with arguments', function () {
    $definition = <<<'PHP'
    #[BaseAttribute('a', 'b', 'c')]
    class BaseClass{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredClass::class)
        ->attributes->toEqual([new DiscoveredAttribute('BaseAttribute')]);
});

it('can resolve an attribute with named arguments', function () {
    $definition = <<<'PHP'
    #[BaseAttribute(execute: 'b')]
    class BaseClass{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredClass::class)
        ->attributes->toEqual([new DiscoveredAttribute('BaseAttribute')]);
});

it('can resolve an attribute with an expression', function () {
    $definition = <<<'PHP'
    #[BaseAttribute(100 + 200)]
    class BaseClass{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredClass::class)
        ->attributes->toEqual([new DiscoveredAttribute('BaseAttribute')]);
});

it('can resolve combined attributes', function () {
    $definition = <<<'PHP'
    #[FirstAttribute, SecondAttribute]
    class BaseClass{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredClass::class)
        ->attributes->toEqual([
            new DiscoveredAttribute('FirstAttribute'),
            new DiscoveredAttribute('SecondAttribute'),
        ]);
});

it('can resolve multi line attributes', function () {
    $definition = <<<'PHP'
    use Spatie\FirstAttribute;

    #[FirstAttribute]
    #[SecondAttribute]
    class BaseClass{}
PHP;

    expect(getDiscoveredStructure($definition))
        ->toBeInstanceOf(DiscoveredClass::class)
        ->attributes->toEqual([
            new DiscoveredAttribute('Spatie\FirstAttribute'),
            new DiscoveredAttribute('SecondAttribute'),
        ]);
});

it('can have attributes on multiple structures', function () {
    $definition = <<<'PHP'
    use Spatie\FirstAttribute;

    #[ClassAttribute]
    class BaseClass{}

    #[InterfaceAttribute]
    interface BaseInterface{}

    #[EnumAttribute]
    enum BaseEnum{}
PHP;

    expect(getDiscovered($definition))
        ->toBeArray()
        ->toHaveCount(3)
        ->sequence(
            fn($discovered) => $discovered
                ->toBeInstanceOf(DiscoveredClass::class)
                ->attributes->toEqual([new DiscoveredAttribute('ClassAttribute')]),
            fn($discovered) => $discovered
                ->toBeInstanceOf(DiscoveredInterface::class)
                ->attributes->toEqual([new DiscoveredAttribute('InterfaceAttribute')]),
            fn($discovered) => $discovered
                ->toBeInstanceOf(DiscoveredEnum::class)
                ->attributes->toEqual([new DiscoveredAttribute('EnumAttribute')])
        );
});

/**
 * Other
 */

it('can handle a corrupt definition', function () {
    $definition = <<<'PHP'
    class CorruptClass
    {
PHP;
    $discovered = app(FileTokenParser::class)->execute('file.php', "<?php{$definition}");

    expect($discovered)->toBeEmpty();
});

it('can discover multiple structures in one file', function () {
    $definition = <<<'PHP'
    namespace Spatie\PhpAutoDiscoverer;

    trait BaseTrait{}

    enum BaseEnum{}

    interface BaseInterface{}

    interface ChildInterface extends BaseInterface{}

    class BaseClass{}

    class ChildClass extends BaseClass implements ChildInterface{}
PHP;

    expect(getDiscovered($definition))
        ->toBeArray()
        ->toHaveCount(6)
        ->sequence(
            fn($discovered) => $discovered
                ->toBeInstanceOf(DiscoveredTrait::class)
                ->name->ToEqual('BaseTrait'),
            fn($discovered) => $discovered
                ->toBeInstanceOf(DiscoveredEnum::class)
                ->name->ToEqual('BaseEnum'),
            fn($discovered) => $discovered
                ->toBeInstanceOf(DiscoveredInterface::class)
                ->name->ToEqual('BaseInterface'),
            fn($discovered) => $discovered
                ->toBeInstanceOf(DiscoveredInterface::class)
                ->name->ToEqual('ChildInterface'),
            fn($discovered) => $discovered
                ->toBeInstanceOf(DiscoveredClass::class)
                ->name->ToEqual('BaseClass'),
            fn($discovered) => $discovered
                ->toBeInstanceOf(DiscoveredClass::class)
                ->name->ToEqual('ChildClass')
        );
});
