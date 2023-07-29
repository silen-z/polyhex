<?php

return function (\FastRoute\RouteCollector $router) {
    $router->get('/', \SilenZ\App\UI\Home\HomeHandler::class);
};
