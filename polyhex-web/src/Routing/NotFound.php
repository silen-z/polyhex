<?php

declare(strict_types=1);

namespace Polyhex\Web\Routing;

final class NotFound extends \Exception
{

    public function __construct(string $path)
    {
        parent::__construct("no handler found for path {$path}", 404);
    }
}
