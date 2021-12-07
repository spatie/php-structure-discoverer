<?php

use Spatie\LaravelAutoDiscoverer\Composer;
use Spatie\LaravelAutoDiscoverer\ClassDiscoverer;
use Spatie\LaravelAutoDiscoverer\Discoverer;

it('can test', function () {
    Discoverer::classes('a')
        ->within(__DIR__.'/Fakes')
        ->get(fn(array $classes) => dd($classes));

    Discoverer::run();
});

it('loads composer autoloaded paths', function (){
    Discoverer::classes()
        ->get(fn(array $classes) => dump($classes));
});
