<?php

require __DIR__ . '/vendor/autoload.php';

use FastRoute\RouteCollector;
use Polyhex\Application\BuildConfig;
use Polyhex\Integration\ReactPHP\ReactPHPApplication;
use Polyhex\Web\Routing\Router;
use Polyhex\Web\WebExtension;
use SilenZ\App\UI\Home\HomeHandler;

$app = ReactPHPApplication::builder()
    ->with_config([
        WebExtension::ROUTER => \DI\decorate(fn(Router $router)
            => $router->with(fn(RouteCollector $collector) 
                => $collector->get('/', HomeHandler::class)))
    ])
    ->build(new BuildConfig());

$app->run();