<?php

declare(strict_types=1);

use Polyhex\Web\Router\FastRouteRouter;

return [
    ...FastRouteRouter::defineRoutes(function ($collector) {
        $collector->get('/', SilenZ\App\UI\Home\HomeHandler::class);
    }),

    Cycle\Database\Config\DatabaseConfig::class => DI\create()->constructor([
        'default' => 'default',
        'databases' => [
            'default' => ['connection' => 'sqlite']
        ],
        'connections' => [
            'sqlite' => new Cycle\Database\Config\SQLiteDriverConfig(
                connection: new Cycle\Database\Config\SQLite\MemoryConnectionConfig(),
                queryCache: true,
            ),
        ],
    ]),
];
