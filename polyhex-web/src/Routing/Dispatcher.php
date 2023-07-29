<?php

declare(strict_types=1);

namespace Polyhex\Web\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Polyhex\Web\Handler\Handler;

/** @api */
final class Dispatcher implements RequestHandlerInterface
{

    /** @internal */
    public function __construct(
        private \FastRoute\Dispatcher $router,
        private \DI\FactoryInterface  $factory,
    )
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $match = $this->router->dispatch($request->getMethod(), $request->getUri()->getPath());

        if ($match[0] === \FastRoute\Dispatcher::NOT_FOUND) {
            throw new NotFound($request->getUri()->getPath());
        }

        if ($match[0] === \FastRoute\Dispatcher::METHOD_NOT_ALLOWED) {
            /** @var string[] $allowedMethods */
            $allowedMethods = $match[1];
            throw new MethodNotAllowed($request->getMethod(), $allowedMethods);
        }

        $handler = $this->resolve($match[1]);
        return $handler->handle($request->withAttribute(Router::PATH_PARAMETERS, $match[2]));
    }

    private function resolve(mixed $handler): RequestHandlerInterface
    {
        if ($handler instanceof RequestHandlerInterface) {
            return $handler;
        }

        if (is_callable($handler)) {
            return $this->factory->make(Handler::class, ['handler' => $handler]);
        }

        $handler = $this->factory->make($handler);

        if ($handler instanceof RequestHandlerInterface) {
            return $handler;
        }

        return $this->factory->make(Handler::class, ['handler' => $handler]);
    }
}
