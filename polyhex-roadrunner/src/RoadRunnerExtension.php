<?php

namespace Polyhex\Integration\RoadRunner;

use Polyhex\Application\Builder;
use Polyhex\Application\Extension;

final class RoadRunnerExtension implements Extension
{

    public function register(Builder $builder): void
    {
        $builder->with_config([
            \Spiral\RoadRunner\WorkerInterface::class => fn() => \Spiral\RoadRunner\Worker::create(),
            \Spiral\RoadRunner\Http\PSR7Worker::class => \DI\autowire(),
        ]);
    }
}