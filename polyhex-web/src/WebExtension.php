<?php declare(strict_types=1);

namespace Polyhex\Web;

use Polyhex\Application\Builder;
use Polyhex\Application\Extension;
use Polyhex\Web\Handler\DefaultErrorHandler;
use Polyhex\Web\Handler\ErrorHandler;
use Polyhex\Web\Handler\DefaultHandlerResolver;
use Polyhex\Web\Handler\HandlerResolver;

final class WebExtension implements Extension
{
    public function __construct(private string|null $router = 'FastRoute') {}

    public const HANDLER = 'web.handler';

    public const ROUTER = 'web.router';

    public function register(Builder $builder): void
    {
        $builder->with_config([
            \Nyholm\Psr7\Factory\Psr17Factory::class => \DI\create(),

            // PSR factories
            \Psr\Http\Message\ServerRequestFactoryInterface::class => \DI\get(\Nyholm\Psr7\Factory\Psr17Factory::class),
            \Psr\Http\Message\UriFactoryInterface::class => \DI\get(\Nyholm\Psr7\Factory\Psr17Factory::class),
            \Psr\Http\Message\UploadedFileFactoryInterface::class => \DI\get(\Nyholm\Psr7\Factory\Psr17Factory::class),
            \Psr\Http\Message\StreamFactoryInterface::class => \DI\get(\Nyholm\Psr7\Factory\Psr17Factory::class),
            \Psr\Http\Message\ResponseFactoryInterface::class => \DI\get(\Nyholm\Psr7\Factory\Psr17Factory::class),

            // Response emitter
            \Laminas\HttpHandlerRunner\Emitter\EmitterInterface::class => \DI\create(\Laminas\HttpHandlerRunner\Emitter\SapiEmitter::class),

            ErrorHandler::class => \DI\autowire(DefaultErrorHandler::class),
            HandlerResolver::class => \DI\autowire(DefaultHandlerResolver::class),
        ]);

        if ($this->router === 'FastRoute') {
            $builder->with_config([
                // Routing
                \Polyhex\Web\Router\FastRouteRouter::ROUTES => [],
                \Polyhex\Web\Router\FastRouteRouter::ROUTER_OPTIONS => ['cacheFile' => '', 'cacheDisabled' => true],

                WebExtension::ROUTER => \DI\autowire(\Polyhex\Web\Router\FastRouteRouter::class)
                    ->constructorParameter('routes', \DI\get(\Polyhex\Web\Router\FastRouteRouter::ROUTES))
                    ->constructorParameter('options', \DI\get(\Polyhex\Web\Router\FastRouteRouter::ROUTER_OPTIONS)),

                WebExtension::HANDLER => \DI\get(WebExtension::ROUTER),
            ]);
        }
    }
}
