<?php

declare(strict_types=1);

namespace Polyhex\Application;

/**
 * @psalm-api
 */
final class BuildConfig
{
    public string|null $cache_path = null;

    public array $parameters = [];

    /** @psalm-api */
    public function cached(string|null $cache_path): self
    {
        $this->cache_path = $cache_path;

        return $this;
    }

    public function addParameters(array $parameters): self
    {
        $this->parameters += $parameters;

        return $this;
    }

    public function hash(): string
    {
        return md5(json_encode($this->parameters));
    }
}