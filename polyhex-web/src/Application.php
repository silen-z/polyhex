<?php

declare(strict_types=1);

namespace Polyhex\Web;

use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Sentry\State\HubInterface;
use Sentry\Tracing\SpanStatus;
use Sentry\Tracing\TransactionContext;
use Sentry\Tracing\TransactionSource;
use Polyhex\Application\Event\ApplicationStarted;

/**
 * @api
 */
final class Application
{

    /**
     * @internal
     */
    public function __construct(
        private RequestHandlerInterface  $router,
        private EmitterInterface         $emitter,
        private EventDispatcherInterface $dispatcher,
        private LoggerInterface          $logger,
        private HubInterface             $hub,
    ) {
    }

    public function run(ServerRequestInterface $request): void
    {
        $context = TransactionContext::fromHeaders(
            $request->getHeader('sentry-trace')[0] ?? '',
            $request->getHeader('baggage')[0] ?? '',
        );
        $context->setOp('http.server');
        $context->setName($request->getMethod() . ' ' . $request->getUri()->getPath());
        $context->setSource(TransactionSource::route());

        $transaction = $this->hub->startTransaction($context);
        $this->hub->setSpan($transaction);

        $this->logger->info('application started');
        $this->dispatcher->dispatch(new ApplicationStarted());

        $handleContext = new \Sentry\Tracing\SpanContext();
        $handleContext->setOp('http.request.handle');

        $response = \Sentry\trace(fn () => $this->router->handle($request), $handleContext);

        $emitSpan = new \Sentry\Tracing\SpanContext();
        $emitSpan->setOp('http.response.emit');

        \Sentry\trace(function () use ($response) {
            $this->emitter->emit($response);
        }, $emitSpan);

        $transaction->setStatus(SpanStatus::createFromHttpStatusCode($response->getStatusCode()));
        $transaction->finish();
    }

    public static function builder(): WebBuilder
    {
        return new WebBuilder();
    }

    public static function requestFromGlobals(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals();
    }
}
