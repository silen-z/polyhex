<?php

declare(strict_types=1);

return [
    \Polyhex\Web\WebExtension::ROUTER => \DI\decorate(fn ($router) => $router->with(require(__DIR__ . "/routes.php"))),

    \Cycle\Database\Config\DatabaseConfig::class => \DI\create()->constructor([
        'default' => 'default',
        'databases' => [
            'default' => ['connection' => 'sqlite']
        ],
        'connections' => [
            'sqlite' => new \Cycle\Database\Config\SQLiteDriverConfig(
                connection: new \Cycle\Database\Config\SQLite\MemoryConnectionConfig(),
                queryCache: true,
            ),
        ],
    ]),
];
