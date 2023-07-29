<?php

declare(strict_types=1);

namespace Polyhex\Web\Routing;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Polyhex\Web\Handler\IntoResponse;

final class JsonResponse implements IntoResponse
{

    public function __construct(private mixed $payload, private int $code = 200)
    {
    }

    public function intoResponse(ResponseFactoryInterface $response_factory): ResponseInterface
    {
        $response = $response_factory->createResponse($this->code)->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($this->payload));

        return $response;
    }
}
