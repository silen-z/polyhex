<?php

declare(strict_types=1);

namespace Polyhex\Web\Routing;

use DI\FactoryInterface;
use FastRoute\RouteCollector;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @api
 */
final class Router
{

    const PATH_PARAMETERS = 'router.path_parameters';

    /** @var callable[] */
    private array $callbacks = [];

    public function __construct(
        private FactoryInterface $factory,
        private array $options = [],
    ) {
    }

    public function with(callable $callback): self
    {
        $this->callbacks[] = $callback;

        return $this;
    }

    public function build(): RequestHandlerInterface
    {
        $router = \FastRoute\cachedDispatcher(function (RouteCollector $route_collector) {
            foreach ($this->callbacks as $cb) {
                $cb($route_collector);
            }
        }, $this->options);

        $dispatcher = $this->factory->make(Dispatcher::class, [
            'router' => $router,
        ]);

        assert($dispatcher instanceof Dispatcher);

        return $dispatcher;
    }
}
