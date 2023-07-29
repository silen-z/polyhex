<?php

declare(strict_types=1);

namespace Polyhex\Integration\RoadRunner;

use Polyhex\Application\BuildConfig;
use Polyhex\Application\Builder;
use Polyhex\Web\WebExtension;

/**
 * @api
 */
final class RoadRunnerBuilder extends Builder
{

    public function __construct()
    {
        parent::__construct();
        $this->use(new WebExtension());
        $this->use(new RoadRunnerExtension());
    }

    public function build(BuildConfig $config): RoadRunnerApplication
    {
        /** @var RoadRunnerApplication $application */
        $application = $this->build_container($config)->make(RoadRunnerApplication::class);

        return $application;
    }

}