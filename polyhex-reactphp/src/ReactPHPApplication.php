<?php

declare(strict_types=1);

namespace Polyhex\Integration\ReactPHP;

use Polyhex\Application;
use Polyhex\Application\Builder;
use Polyhex\Application\Extension\CoreExtension;
use Polyhex\Web\ErrorHandler;
use Polyhex\Web\WebExtension;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use DI;

/**
 * @api
 */
final class ReactPHPApplication implements Application
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

    public static function builder(): Builder
    {
        return (new Builder(self::class, [ 'handler' => DI\get(WebExtension::HANDLER) ]))
            ->use(new CoreExtension(), new WebExtension(), new ReactPHPExtension());
    }

    private function handle(ServerRequestInterface $request): ResponseInterface {
        try {
            return $this->handler->handle($request);
        } catch (\Throwable $e) {
            return $this->errorHandler->handleError($e);
        }
    }

}
