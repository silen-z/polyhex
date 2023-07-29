<?php

declare(strict_types=1);

namespace Polyhex\Web;

use Polyhex\Application\Builder;
use Polyhex\Application\Extension;

final class WebExtension implements Extension
{
    public const ROUTER = 'web.router';
    public const ROUTER_OPTIONS = 'web.router.options';

    public const DISPATCHER = 'web.dispatcher';

    public function register(Builder $builder): void
    {
        $builder->with_config([
            \Psr\Http\Message\ResponseFactoryInterface::class => \DI\create(\Laminas\Diactoros\ResponseFactory::class),
            \Laminas\HttpHandlerRunner\Emitter\EmitterInterface::class => \DI\create(\Laminas\HttpHandlerRunner\Emitter\SapiEmitter::class),

            WebExtension::ROUTER_OPTIONS => ['cacheFile' => '', 'cacheDisabled' => true],
            WebExtension::ROUTER => \DI\autowire(\Polyhex\Web\Routing\Router::class)
                ->constructorParameter('options', \DI\get(WebExtension::ROUTER_OPTIONS)),

            /** @psalm-suppress InvalidArgument */
            WebExtension::DISPATCHER => \DI\factory([WebExtension::ROUTER, 'build']),

            Application::class => \DI\autowire()
                ->constructor(\DI\get(WebExtension::DISPATCHER)),
        ]);
    }
}
