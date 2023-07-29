<?php declare(strict_types=1);

namespace Polyhex\Web\Routing;

use Attribute;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionProperty;
use Polyhex\Web\Handler\Extractor;

#[Attribute]
class QueryParam implements Extractor
{

    public function __construct(
        private readonly string $name,
    )
    {
    }

    public function extract(ServerRequestInterface $request, ReflectionProperty $property): mixed
    {
        $name = $this->name ?? $property->getName();
        return $request->getQueryParams()[$name] ?? null;
    }
}