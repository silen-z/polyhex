<?php declare(strict_types=1);

namespace Polyhex\Integration\CycleORM;

use Spiral\Tokenizer\ClassesInterface;

class StaticClassProvider implements ClassesInterface
{

    /**
     * @param array<class-string> $classes
     */
    public function __construct(private readonly array $classes)
    {
    }

    /** {@inheritDoc} */
    public function getClasses(object|string|null $target = null): array
    {
        $reflections = [];

        foreach ($this->classes as $classname) {
            $reflections[$classname] = new ReflectionClass($classname);
        }

        return $reflections;
    }
}