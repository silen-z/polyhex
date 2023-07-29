<?php

namespace Polyhex\Integration\RoadRunner;

use Polyhex\Application\Builder;
use Polyhex\Application\Extension;
use Polyhex\Web\WebExtension;

final class RoadRunnerExtension implements Extension
{

    public function register(Builder $builder): void
    {
        $builder->with_config([
            \Psr\Http\Server\RequestHandlerInterface::class => \DI\get(WebExtension::DISPATCHER),
            \Spiral\RoadRunner\WorkerInterface::class => \Spiral\RoadRunner\Worker::create(),
            \Spiral\RoadRunner\Http\PSR7Worker::class => \DI\create()->constructor(
                \DI\get(\Spiral\RoadRunner\WorkerInterface::class),
                new \Laminas\Diactoros\ServerRequestFactory(),
                new \Laminas\Diactoros\StreamFactory(),
                new \Laminas\Diactoros\UploadedFileFactory(),
            )
        ]);
    }
}