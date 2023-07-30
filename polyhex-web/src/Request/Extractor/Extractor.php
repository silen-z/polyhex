<?php declare(strict_types=1);

namespace Polyhex\Web\Request\Extractor;

use Attribute;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionProperty;

#[Attribute]
interface Extractor
{

    public function extract(ServerRequestInterface $request, ReflectionProperty $property): mixed;

}