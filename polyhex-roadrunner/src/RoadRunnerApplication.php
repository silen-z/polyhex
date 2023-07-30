<?php

declare(strict_types=1);

namespace Polyhex\Integration\RoadRunner;

use Polyhex\Web\ErrorHandler;
use Psr\Http\Server\RequestHandlerInterface;
use Spiral\RoadRunner\Http\PSR7Worker;

/**
 * @api
 */
final class RoadRunnerApplication
{

    /**
     * @internal
     */
    public function __construct(
        private PSR7Worker $worker,
        private RequestHandlerInterface $handler,
        private ErrorHandler $errorHandler,
    )
    {
    }

    public function run(): void
    {
        while (true) {
            try {
                $request = $this->worker->waitRequest();
                $response = $this->handler->handle($request);
                $this->worker->respond($response);
            } catch (\Throwable $e) {
                $response = $this->errorHandler->handleError($e);
                $this->worker->respond($response);
                $this->worker->getWorker()->error($e->__toString());
                continue;
            }
        }
    }

    public static function builder(): RoadRunnerBuilder
    {
        return new RoadRunnerBuilder();
    }

}
