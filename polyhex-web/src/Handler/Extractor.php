<?php

namespace Polyhex\Web\Handler;

use Attribute;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionProperty;

#[Attribute]
interface Extractor
{

    public function extract(ServerRequestInterface $request, ReflectionProperty $property): mixed;

}