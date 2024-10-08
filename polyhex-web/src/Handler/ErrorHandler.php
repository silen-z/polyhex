<?php declare(strict_types=1);

namespace Polyhex\Web\Handler;

use Psr\Http\Message\ResponseInterface;
use Throwable;

interface ErrorHandler {

    public function handleError(Throwable $error): ResponseInterface;

}