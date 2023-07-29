<?php

declare(strict_types=1);

namespace Polyhex\Web\Routing;

final class MethodNotAllowed extends \Exception
{

    /** 
     * @param string[] $allowedMethods 
     */
    public function __construct(string $method, public array $allowedMethods)
    {
        parent::__construct("method {$method} not allowed", 400);
    }
}
