<?php

namespace Polyhex\Integration\ReactPHP;

use Polyhex\Application\Builder;
use Polyhex\Application\Extension;
use Polyhex\Web\WebExtension;

final class ReactPHPExtension implements Extension
{

    public function register(Builder $builder): void
    {
        $builder->with_config([
            ReactPHPApplication::class => \DI\autowire()
                ->constructorParameter('handler', \DI\get(WebExtension::DISPATCHER))
        ]);
    }
}