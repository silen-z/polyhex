<?php

declare(strict_types=1);

namespace Polyhex\Web\Handler;

use Psr\Http\Message\ServerRequestInterface;

abstract class ExtractedRequest implements FromServerRequest
{

    public static function fromRequest(ServerRequestInterface $request): self
    {
        $ref = new \ReflectionClass(static::class);
        $instance = $ref->newInstanceWithoutConstructor();

        foreach ($ref->getProperties() as $property) {
            $attrs = $property->getAttributes();

            foreach ($attrs as $attr) {
                if (is_a($attr->getName(), Extractor::class, true)) {
                    /** @var Extractor $extractor */
                    $extractor = $attr->newInstance();

                    $value = $extractor->extract($request, $property);
                    if ($property->hasDefaultValue() && $value === null) {
                        break;
                    }

                    if ($value === null && !$property->getType()->allowsNull()) {
                        throw new \LogicException('missing parameter');
                    }

                    $property->setValue($instance, $value);
                    break;
                }
            }
        }

        return $instance;
    }

}