<?php

namespace Silenz\App\Plugin\Example;

use Polyhex\Application\Builder;
use Polyhex\Application\Extension;
use Polyhex\Web\Router\FastRouteRouter;

class ExamplePluginExtension implements Extension
{

    public function register(Builder $builder): void
    {
        $builder->with_config([
            ...FastRouteRouter::defineRoutes(function ($r) {
                $r->get("/plugin", PluginHandler::class);
            }),
        ]);
    }
}
