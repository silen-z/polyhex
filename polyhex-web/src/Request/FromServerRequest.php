<?php

declare(strict_types=1);

namespace Polyhex\Web\Request;

use Psr\Http\Message\ServerRequestInterface;

interface FromServerRequest
{

    public static function fromRequest(ServerRequestInterface $request): self;
}
