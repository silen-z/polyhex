<?php declare(strict_types=1);

namespace Polyhex\Web\Handler;

use DI\FactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DefaultHandlerResolver implements HandlerResolver {

    public function __construct(private FactoryInterface $factory) {}

    public function resolve(mixed $handler): RequestHandlerInterface
    {
        if ($handler instanceof RequestHandlerInterface) {
            return $handler;
        }

        if (is_callable($handler)) {
            return $this->factory->make(InjectedHandler::class, ['handler' => $handler]);
        }

        $handler = $this->factory->make($handler);

        if ($handler instanceof RequestHandlerInterface) {
            return $handler;
        }

        return $this->factory->make(InjectedHandler::class, ['handler' => $handler]);
    }

}