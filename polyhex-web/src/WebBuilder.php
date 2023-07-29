<?php

declare(strict_types=1);

namespace Polyhex\Web;

use Polyhex\Application\BuildConfig;
use Polyhex\Application\Builder;
use Polyhex\Integration\RoadRunner\RoadRunnerApplication;

/**
 * @api
 */
final class WebBuilder extends Builder
{

    public function __construct()
    {
        parent::__construct();
        $this->use(new WebExtension());
    }

    public function build(BuildConfig $config): Application
    {
        /** @var Application $application */
        $application = $this->build_container($config)->make(Application::class);

        return $application;
    }

}