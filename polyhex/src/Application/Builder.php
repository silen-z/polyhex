<?php

declare(strict_types=1);

namespace Polyhex\Application;

use DI\ContainerBuilder;
use Polyhex\Application\Extension\CoreExtension;

/**
 * @psalm-api
 */
abstract class Builder
{
    private ContainerBuilder $container_builder;

    /** @var string[] */
    private array $registered_extensions = [];

    /** @psalm-api */
    public function __construct()
    {
        $this->container_builder = new ContainerBuilder();
        $this->use(new CoreExtension());
    }

    /** @psalm-api */
    public static function buildConfig(): BuildConfig
    {
        return new BuildConfig();
    }

    public function with_config(array ...$configs): static
    {
        foreach ($configs as $definitions) {
            $this->container_builder->addDefinitions($definitions);
        }

        return $this;
    }

    public function use(Extension ...$extensions): static
    {
        foreach ($extensions as $extension) {

            $this->registered_extensions[] = $extension::class;

            $extension->register($this);

        }

        return $this;
    }

    /**
     * @psalm-api
     * @throws \Exception
     */
    public function build_container(BuildConfig $config): \DI\Container
    {
        $this->container_builder->addDefinitions([
            ...$config->parameters,
            'registered_extensions' => $this->registered_extensions,
        ]);

        if ($config->cache_path !== null) {
            $cache_dir = rtrim($config->cache_path, '/') . '/containers/';
            $this->container_builder->enableCompilation($cache_dir, 'container_' . $config->hash());
        }

        $this->container_builder->useAutowiring(true);

        return $this->container_builder->build();
    }

}
