<?php

declare(strict_types=1);

namespace Polyhex\Web;

use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Polyhex\Application\Event\ApplicationStarted;
use Nyholm\Psr7Server\ServerRequestCreator;
use Polyhex\Web\ErrorHandler;
use Throwable;

/**
 * @api
 */
final class Application
{

    /**
     * @internal
     */
    public function __construct(
        private RequestHandlerInterface  $handler,
        private ErrorHandler             $errorHandler,
        private EmitterInterface         $emitter,
        private EventDispatcherInterface $dispatcher,
        private LoggerInterface          $logger,
        private ServerRequestCreator     $requestCreator,
        // private HubInterface             $hub,
    ) {
    }

    public function run(ServerRequestInterface $request): void
    {
        // $context = TransactionContext::fromHeaders(
        //     $request->getHeader('sentry-trace')[0] ?? '',
        //     $request->getHeader('baggage')[0] ?? '',
        // );
        // $context->setOp('http.server');
        // $context->setName($request->getMethod() . ' ' . $request->getUri()->getPath());
        // $context->setSource(TransactionSource::route());

        // $transaction = $this->hub->startTransaction($context);
        // $this->hub->setSpan($transaction);

        $this->logger->info('application started');
        $this->dispatcher->dispatch(new ApplicationStarted());

        // $handleContext = new \Sentry\Tracing\SpanContext();
        // $handleContext->setOp('http.request.handle');

        // $response = \Sentry\trace(fn () => 

        try {
            $response = $this->handler->handle($request);
        } catch (Throwable $e) {
            $response = $this->errorHandler->handleError($e);
        }
        // $handleContext);

        // $emitSpan = new \Sentry\Tracing\SpanContext();
        // $emitSpan->setOp('http.response.emit');

        // \Sentry\trace(function () use ($response) {
            $this->emitter->emit($response);
        // }, $emitSpan);

        // $transaction->setStatus(SpanStatus::createFromHttpStatusCode($response->getStatusCode()));
        // $transaction->finish();
    }

    public function requestFromGlobals(): ServerRequestInterface
    {
        return $this->requestCreator->fromGlobals();
    }

    public static function builder(): WebBuilder
    {
        return new WebBuilder();
    }

}
