<?php

declare(strict_types=1);

namespace Polyhex\Application;

interface Extension
{
    public function register(Builder $builder): void;
}
