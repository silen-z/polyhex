<?php declare(strict_types=1);

namespace Polyhex\Web\Handler;

use Polyhex\Web\ErrorHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final class DefaultErrorHandler implements ErrorHandler
{

    public function __construct(
        private ResponseFactoryInterface $responseFactory
    ) {}

    public function handleError(Throwable $e): ResponseInterface {
        $response = $this->responseFactory->createResponse(500)
            ->withHeader('content-type', 'text/plain');

        $response->getBody()->write($e->getMessage());
        $response->getBody()->write("\n");
        $response->getBody()->write($e->getTraceAsString());

        return $response;
    }
    
}
