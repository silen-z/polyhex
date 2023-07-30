<?php

declare(strict_types=1);

namespace Polyhex\Web;

use Polyhex\Application\BuildConfig;
use Polyhex\Application\Builder;

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
        $application = $this->build_container($config)->make(Application::class, [
            'handler' => \DI\get(WebExtension::HANDLER),
        ]);

        return $application;
    }

}