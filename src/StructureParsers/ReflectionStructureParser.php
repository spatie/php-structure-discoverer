<?php

namespace Spatie\StructureDiscoverer\StructureParsers;

use ReflectionClass;
use ReflectionEnum;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredEnum;
use Spatie\StructureDiscoverer\Data\DiscoveredInterface;
use Spatie\StructureDiscoverer\Data\DiscoveredTrait;
use Spatie\StructureDiscoverer\Data\DiscoverProfileConfig;
use Throwable;

class ReflectionStructureParser implements StructureParser
{
    public function __construct(
        protected DiscoverProfileConfig $config
    ) {
    }

    public function execute(array $filenames): array
    {
        $discovered = [];

        foreach ($filenames as $filename) {
            $fqcn = $this->fullQualifiedClassNameFromFile($filename);

            try {
                $reflectionClass = new ReflectionClass($fqcn);

                if ($reflectionClass->isEnum()) {
                    $discovered[] = DiscoveredEnum::fromReflection(
                        $reflectionClass instanceof ReflectionEnum ? $reflectionClass : new ReflectionEnum($reflectionClass->name)
                    );
                }

                if ($reflectionClass->isInterface()) {
                    $discovered[] = DiscoveredInterface::fromReflection($reflectionClass);
                }

                if ($reflectionClass->isTrait()) {
                    $discovered[] = DiscoveredTrait::fromReflection($reflectionClass);
                }

                $discovered[] = DiscoveredClass::fromReflection($reflectionClass);
            } catch (Throwable $e) {
                continue;
            }
        }

        return $discovered;
    }

    /** @return class-string */
    protected function fullQualifiedClassNameFromFile(string $filename): string
    {
        $class = preg_replace(
            pattern: "#".preg_quote($this->config->reflectionBasePath)."#",
            replacement: '',
            subject: $filename,
            limit: 1
        );

        $class = trim($class, DIRECTORY_SEPARATOR);

        $class = str_replace(
            [DIRECTORY_SEPARATOR, 'App\\'],
            ['\\', app()->getNamespace()],
            ucfirst(str_replace('.php', '', $class))
        );

        $rootNamespace = $this->config->reflectionRootNamespace === null || str_ends_with($this->config->reflectionRootNamespace, '\\')
            ? $this->config->reflectionRootNamespace
            : $this->config->reflectionRootNamespace.'\\';

        /** @var class-string $fqcn */
        $fqcn = $rootNamespace.$class;

        return $fqcn;
    }
}
