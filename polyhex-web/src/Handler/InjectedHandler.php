<?php

declare(strict_types=1);

namespace Polyhex\Web\Handler;

use Exception;
use Invoker\InvokerInterface;
use LogicException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Polyhex\Web\Request\FromServerRequest;
use ReflectionFunction;
use ReflectionIntersectionType;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;

final class InjectedHandler implements RequestHandlerInterface
{

    /** @var callable(mixed...): ResponseInterface|IntoResponse */
    private $handler;

    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly InvokerInterface         $invoker,
        callable                                  $handler,
    )
    {
        $this->handler = $handler;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $params = [];

        $requestParam = $this->resolveRequestType();
        if ($requestParam !== null) {
            [$requestClass, $acceptsError] = $requestParam;
            $resolved = $this->resolveRequest($request, $requestClass);
            if (!$acceptsError && $resolved instanceof Exception) {
                return $this->defaultBadRequestResponse();
            }

            $params['request'] = $resolved;
        }

        $response = $this->invoker->call($this->handler, $params);

        if ($response instanceof IntoResponse) {
            return $response->intoResponse($this->responseFactory);
        }

        return $response;
    }

    private function resolveRequest(
        ServerRequestInterface $request,
        string $requestClass,
    ): RequestInterface|FromServerRequest|Exception|null
    {
        if (!is_a($requestClass, FromServerRequest::class, true)) {
            return $request;
        }

        try {
            return $requestClass::fromRequest($request);
        } catch (Exception $exception) {
            return $exception;
        }
    }

    /**
     * @return array{0: class-string, 1: bool}|null
     */
    private function resolveRequestType(): array|null
    {
        $callable = match (method_exists($this->handler, '__invoke')) {
            true => new ReflectionMethod($this->handler, '__invoke'),
            false => new ReflectionFunction($this->handler),
        };

        foreach ($callable->getParameters() as $parameter) {
            if ($parameter->name === 'request') {
                $type = $parameter->getType();

                if ($type instanceof ReflectionIntersectionType) {
                    throw $this->invalidRequestTypeError();
                }

                if ($type instanceof ReflectionNamedType) {
                    $typeName = $type->getName();

                    if ($typeName === RequestInterface::class) {
                        return [RequestInterface::class, false];
                    }

                    if (is_a($typeName, FromServerRequest::class, true)) {
                        return [$typeName, $type->allowsNull() ? null : false];
                    }

                    throw $this->invalidRequestTypeError();
                }

                if ($type instanceof ReflectionUnionType) {
                    $types = array_map(fn($t) => $t->getName(), $type->getTypes());

                    if (count($types) !== 2 || !in_array(Exception::class, $types, true)) {
                        throw $this->invalidRequestTypeError();
                    }

                    $requestType = is_a($types[0], Exception::class, true) ? $types[1] : $types[0];
                    if (is_a($requestType, FromServerRequest::class, true)) {
                        return [$requestType, true];
                    }

                    throw $this->invalidRequestTypeError();
                }

                return [RequestInterface::class, false];
            }
        }

        return null;
    }

    private function invalidRequestTypeError(): LogicException
    {
        return new LogicException(sprintf(
            "Handler can not resolve request type. Expected request of type %s, type implementing %s, %s|null or %s|%s",
            RequestInterface::class,
            FromServerRequest::class,
            FromServerRequest::class,
            FromServerRequest::class,
            Exception::class
        ));
    }

    private function defaultBadRequestResponse(): ResponseInterface {
        $response = $this->responseFactory->createResponse(400)->withHeader('Content-Type', 'text/plain');
        $response->getBody()->write('bad request');
        return $response;
    }
}
