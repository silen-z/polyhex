<?php

namespace Polyhex\Integration\ReactPHP;

use Polyhex\Application\Builder;
use Polyhex\Application\Extension;

final class ReactPHPExtension implements Extension
{

    public function register(Builder $builder): void
    {
        $builder->with_config([]);
    }
}