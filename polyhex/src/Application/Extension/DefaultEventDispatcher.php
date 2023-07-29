<?php

namespace Polyhex\Application\Extension;

use Psr\EventDispatcher\EventDispatcherInterface;

class DefaultEventDispatcher implements EventDispatcherInterface
{

    public function dispatch(object $event): void
    {
    }
}