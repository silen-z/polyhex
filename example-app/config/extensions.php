<?php

use Polyhex\Integration;

return [
    new Integration\EventDispatcher\EventDispatcherExtension(),
    new Integration\Tracy\TracyExtension(),

    Integration\CycleORM\CycleORMExtension::entity_paths([
        __DIR__ . "/../src/Database/Entity",
    ]),

    new Integration\Composer\PluginExtension('example-app-plugin'),
];