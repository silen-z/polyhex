<?php

namespace Polyhex\Integration\Composer;

use Polyhex\Application\Builder;
use Polyhex\Application\Extension;

/**
 * @psalm-api
 */
final class PluginExtension implements Extension
{

    public function __construct(
        private readonly string $package_type,
    )
    {
    }

    public function register(Builder $builder): void
    {
        $plugins = \Composer\InstalledVersions::getInstalledPackagesByType($this->package_type);

        foreach (array_unique($plugins) as $plugin_package) {
            $install_path = \Composer\InstalledVersions::getInstallPath($plugin_package);

            if ($install_path === null) {
                throw new \LogicException("Only regular packages can be used as plugins");
            }

            /** @var array $package_info */
            $package_info = json_decode(file_get_contents($install_path . "/composer.json"), true);

            $extension_class = $package_info['extra'][$this->package_type]['extension-class'] ?? null;

            if (!is_a($extension_class, Extension::class, true)) {
                throw new \LogicException("");
            }

            $plugin_extension = new $extension_class;
            $builder->use($plugin_extension);
        }
    }
}