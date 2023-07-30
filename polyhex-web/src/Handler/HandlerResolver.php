<?php declare(strict_types=1);

namespace Polyhex\Web\Handler;

use Psr\Http\Server\RequestHandlerInterface;

interface HandlerResolver {

    public function resolve(mixed $handler): RequestHandlerInterface;

}