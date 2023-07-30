<?php declare(strict_types=1);

use Polyhex\Web\Router\FastRouteRouter;

return [
    ...FastRouteRouter::defineRoutes(function ($collector) {
        $collector->get('/another', SilenZ\App\UI\Home\HomeHandler::class);
    }),
];
