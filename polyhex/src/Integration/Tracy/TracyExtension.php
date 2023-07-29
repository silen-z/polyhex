<?php

namespace Polyhex\Integration\Tracy;

use Polyhex\Application\Extension\CoreExtension;
use Psr\Container\ContainerInterface;
use Polyhex\Application\Builder;
use Polyhex\Application\Extension;

use Tracy\IBarPanel;

final class TracyExtension implements Extension
{

    public const PANELS = 'tracy.panels';

    public function register(Builder $builder): void
    {
        $builder->with_config([
            TracyExtension::PANELS => [
                \DI\create(DebugModePanel::class)
                    ->constructor(\DI\get('debug_mode'), \DI\get('registered_extensions')),
            ],

            CoreExtension::LISTENERS => \DI\add([
                \Polyhex\Application\Event\ApplicationStarted::class => fn(ContainerInterface $container) => function () use ($container) {
                    $bar = \Tracy\Debugger::getBar();

                    /** @var array<IBarPanel> $panels */
                    $panels = $container->get(TracyExtension::PANELS);

                    foreach ($panels as $panel) {
                        $bar->addPanel($panel);
                    }
                },
            ])
        ]);
    }
}