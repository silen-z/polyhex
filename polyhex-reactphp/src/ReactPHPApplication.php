<?php

declare(strict_types=1);

namespace Polyhex\Integration\ReactPHP;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

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
    )
    {
    }

    /**
     * @return never
     */
    public function run(): void
    {
        $http = new \React\Http\HttpServer(function ($request) {
            try {
                return $this->handler->handle($request);
            } catch (\Throwable $e) {
                dump($e->getMessage());
                dumpe($e->getTrace()[0]);
            }
        });
        $socket = new \React\Socket\SocketServer('0.0.0.0:8080');
        $http->listen($socket);
    }

    public static function builder(): ReactPHPBuilder
    {
        return new ReactPHPBuilder();
    }

}
