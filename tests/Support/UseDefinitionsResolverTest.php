<?php

use Spatie\StructureDiscoverer\Data\Usage;
use Spatie\StructureDiscoverer\Support\UseDefinitionsResolver;
use Spatie\StructureDiscoverer\Tests\Fakes\Nested\FakeNestedInterface;

it('can parse uses', function (){
    $resolver = new UseDefinitionsResolver();

    $usages = $resolver->execute(__DIR__.'/../Fakes/FakeChildInterface.php');

    expect($usages)->toHaveCount(1);
    expect($usages->findForAlias('FakeNestedInterface'))->toBeInstanceOf(Usage::class);
    expect($usages->findForAlias('FakeNestedInterface')->fcqn)->toBe(FakeNestedInterface::class);
    expect($usages->findForAlias('FakeNestedInterface')->name)->toBe('FakeNestedInterface');
});
