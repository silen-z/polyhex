<?php

declare(strict_types=1);

namespace Polyhex\Web\Handler;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

interface IntoResponse
{
    public function intoResponse(ResponseFactoryInterface $response_factory): ResponseInterface;
}
