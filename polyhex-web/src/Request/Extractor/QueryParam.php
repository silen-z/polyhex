<?php declare(strict_types=1);

namespace Polyhex\Web\Request\Extractor;

use Attribute;
use ReflectionProperty;
use Psr\Http\Message\ServerRequestInterface;

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