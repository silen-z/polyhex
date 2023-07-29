<?php

namespace Polyhex\Integration\Tracy;

/**
 * @psalm-api
 */
class DebugModePanel implements \Tracy\IBarPanel
{

    /**
     * @psalm-api
     */
    public function __construct(
        private readonly bool  $debug_mode,
        private readonly array $registered_extensions,
    )
    {

    }

    public function getTab(): string
    {
        $status = $this->debug_mode ? '✔️' : '❌';

        return "debug: $status";
    }


    public function getPanel(): string
    {
        $extension_list = implode("\n", array_map(fn($ext) => "<li>{$ext}</li>", $this->registered_extensions));

        return "<ul>{$extension_list}</ul>";
    }
}