<?php

declare(strict_types=1);

namespace Polyhex\Integration\RoadRunner;

use Polyhex\Application;
use Polyhex\Application\Builder;
use Polyhex\Application\Extension\CoreExtension;
use Polyhex\Web\ErrorHandler;
use Polyhex\Web\WebExtension;
use Psr\Http\Server\RequestHandlerInterface;
use Spiral\RoadRunner\Http\PSR7Worker;
use DI;

/**
 * @api
 */
final class RoadRunnerApplication implements Application
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

    public static function builder(): Builder
    {
        return (new Builder(self::class, [ 'handler' => DI\get(WebExtension::HANDLER) ]))
            ->use(new CoreExtension(), new WebExtension(), new RoadRunnerExtension());
    }

}
