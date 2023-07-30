<?php

declare(strict_types=1);

namespace Polyhex\Web;

use Polyhex\Application\Builder;
use Polyhex\Application\Extension;
use Polyhex\Web\Handler\DefaultErrorHandler;
use Polyhex\Web\ErrorHandler;

final class WebExtension implements Extension
{
    public const HANDLER = 'web.handler';

    public const ROUTER = 'web.router';
    public const ROUTER_OPTIONS = 'web.router.options';

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

            // Routing
            WebExtension::ROUTER_OPTIONS => ['cacheFile' => '', 'cacheDisabled' => true],
            WebExtension::ROUTER => \DI\autowire(\Polyhex\Web\Routing\Router::class)
                ->constructorParameter('options', \DI\get(WebExtension::ROUTER_OPTIONS)),

            ErrorHandler::class => \DI\autowire(DefaultErrorHandler::class),

            /** @psalm-suppress InvalidArgument */
            WebExtension::HANDLER => \DI\factory([WebExtension::ROUTER, 'build']),
        ]);
    }
}
