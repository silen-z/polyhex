<?php

namespace Silenz\App\Plugin\Example;

use Polyhex\Application\Builder;
use Polyhex\Application\Extension;

class ExamplePluginExtension implements Extension
{

    public function register(Builder $builder): void
    {
        $builder->with_config([
            \Polyhex\Web\WebExtension::ROUTER => \DI\decorate(fn ($router) => $router->with(function ($r) {
                $r->get("/plugin", PluginHandler::class);
            })),
        ]);
    }
}
