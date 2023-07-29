<?php

declare(strict_types=1);

namespace Polyhex\Integration\RoadRunner;

use Psr\Http\Message\ResponseFactoryInterface;
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
        private RequestHandlerInterface $router,
        private ResponseFactoryInterface $responseFactory,
    )
    {
    }

    public function run(): void
    {

        while (true) {
            try {
                $request = $this->worker->waitRequest();
            } catch (\Throwable $e) {
                // Although the PSR-17 specification clearly states that there can be
                // no exceptions when creating a request, however, some implementations
                // may violate this rule. Therefore, it is recommended to process the
                // incoming request for errors.
                //
                // Send "Bad Request" response.
                $this->worker->respond($this->responseFactory->createResponse(400));
                continue;
            }

            try {

                $response = $this->router->handle($request);
                //
                // Reply by the 200 OK response
                $this->worker->respond($response);
            } catch (\Throwable $e) {
                // In case of any exceptions in the application code, you should handle
                // them and inform the client about the presence of a server error.
                //
                // Reply by the 500 Internal Server Error response
                $response = $this->responseFactory->createResponse(500);
                $response->getBody()->write($e->getMessage());
                $response->getBody()->write($e->getTraceAsString());
                $this->worker->respond($response);

                // Additionally, we can inform the RoadRunner that the processing
                // of the request failed.
                $this->worker->getWorker()->error((string)$e);
            }
        }
    }

    public static function builder(): RoadRunnerBuilder
    {
        return new RoadRunnerBuilder();
    }

}
