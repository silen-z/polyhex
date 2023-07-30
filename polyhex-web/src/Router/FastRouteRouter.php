<?php

declare(strict_types=1);

namespace Polyhex\Web\Router;

use DI;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use FastRoute;
use Polyhex\Web\Handler\HandlerResolver;

/**
 * @api
 */
final class FastRouteRouter implements RequestHandlerInterface
{

    public const PATH_PARAMETERS = 'router.path_parameters';

    public const ROUTES = "web.FastRouteRouter.routes";
    public const ROUTER_OPTIONS = 'web.FastRouteRouter.options';

    private FastRoute\Dispatcher $dispatcher;

    public function __construct(
        private HandlerResolver $handlerResolver,
        callable $routes,
        array $options = [],
    ) {
        $this->dispatcher = FastRoute\cachedDispatcher($routes, $options);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $match = $this->dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        if ($match[0] === FastRoute\Dispatcher::NOT_FOUND) {
            throw new NotFound($request->getUri()->getPath());
        }

        if ($match[0] === FastRoute\Dispatcher::METHOD_NOT_ALLOWED) {
            /** @var string[] $allowedMethods */
            $allowedMethods = $match[1];
            throw new MethodNotAllowed($request->getMethod(), $allowedMethods);
        }

        $handler = $this->handlerResolver->resolve($match[1]);
        return $handler->handle($request->withAttribute(self::PATH_PARAMETERS, $match[2]));
    }

    public static function defineRoutes(callable $define): array
    {
        return [
            self::ROUTES => DI\decorate(fn (callable $original) => function($r) use ($original, $define) {
                $original($r);
                $define($r);
            }),
        ];
    }

}
