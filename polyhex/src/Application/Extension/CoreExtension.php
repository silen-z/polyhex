<?php

namespace Polyhex\Application\Extension;

use Polyhex\Application\Builder;
use Polyhex\Application\Extension;

class CoreExtension implements Extension
{

    public const LISTENERS = 'core.event_listeners';

    public function register(Builder $builder): void
    {
        $builder->with_config([
            self::LISTENERS => [],
            \Psr\EventDispatcher\EventDispatcherInterface::class => \DI\create(DefaultEventDispatcher::class),
            \Psr\Log\LoggerInterface::class => \DI\create(DefaultLogger::class),
        ]);
    }
}