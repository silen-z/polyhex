<?php

declare(strict_types=1);

namespace Polyhex\Integration\EventDispatcher;

use Polyhex\Application\Builder;
use Polyhex\Application\Extension;

/**
 * @psalm-api
 */
final class EventDispatcherExtension implements Extension
{
    public function register(Builder $builder): void
    {
        $builder->with_config([
            \Psr\EventDispatcher\EventDispatcherInterface::class => \DI\factory(function (array $listeners) {
                /** @var array<string, callable> $listeners */

                $dispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();

                foreach ($listeners as $event => $listener) {
                    $dispatcher->addListener($event, $listener);
                }

                return $dispatcher;
            })->parameter('listeners', \DI\get(Extension\CoreExtension::LISTENERS)),
        ]);
    }
}