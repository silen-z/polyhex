<?php

declare(strict_types=1);

namespace Polyhex\Integration\ReactPHP;

use Polyhex\Web\ErrorHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @api
 */
final class ReactPHPApplication
{

    /**
     * @internal
     */
    public function __construct(
        private RequestHandlerInterface $handler,
        private ErrorHandler $errorHandler,
    )
    {
    }

    /**
     * @return never
     */
    public function run(): void
    {
        $http = new \React\Http\HttpServer($this->handle(...));
        $socket = new \React\Socket\SocketServer('0.0.0.0:8080');
        $http->listen($socket);
    }

    public static function builder(): ReactPHPBuilder
    {
        return new ReactPHPBuilder();
    }

    private function handle(ServerRequestInterface $request): ResponseInterface {
        try {
            return $this->handler->handle($request);
        } catch (\Throwable $e) {
            return $this->errorHandler->handleError($e);
        }
    }

}
