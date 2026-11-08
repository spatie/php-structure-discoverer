<?php

use Spatie\StructureDiscoverer\Data\Usage;
use Spatie\StructureDiscoverer\Support\UseDefinitionsResolver;
use Spatie\StructureDiscoverer\Tests\Fakes\Nested\FakeNestedInterface;

it('can parse uses', function () {
    $resolver = new UseDefinitionsResolver();

    $usages = $resolver->execute(__DIR__.'/../Fakes/FakeChildInterface.php');

    expect($usages)->toHaveCount(1);
    expect($usages->findForAlias('FakeNestedInterface'))->toBeInstanceOf(Usage::class);
    expect($usages->findForAlias('FakeNestedInterface')->fcqn)->toBe(FakeNestedInterface::class);
    expect($usages->findForAlias('FakeNestedInterface')->name)->toBe('FakeNestedInterface');
});

it('can parse group uses', function () {
    $file = tempnam(sys_get_temp_dir(), 'use-definitions-resolver');

    file_put_contents($file, <<<'PHP'
    <?php

    namespace App\Data;

    use App\Enums\Status;
    use Modules\Shared\Data\{LocationData, RegistrationNumberAssignmentData as AssignmentData};

    class ShipmentData
    {
        /** @var AssignmentData[] */
        public array $assignments;
    }
    PHP);

    $usages = (new UseDefinitionsResolver())->execute($file);

    unlink($file);

    expect($usages)->toHaveCount(3);
    expect($usages->findForAlias('Status')->fcqn)->toBe('App\Enums\Status');
    expect($usages->findForAlias('LocationData')->fcqn)->toBe('Modules\Shared\Data\LocationData');
    expect($usages->findForAlias('AssignmentData')->fcqn)->toBe('Modules\Shared\Data\RegistrationNumberAssignmentData');
    expect($usages->findForAlias('Data'))->toBeNull();
});
