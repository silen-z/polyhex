<?php

declare(strict_types=1);

namespace Polyhex\Integration\ReactPHP;

use Polyhex\Application\BuildConfig;
use Polyhex\Application\Builder;
use Polyhex\Web\WebExtension;

/**
 * @api
 */
final class ReactPHPBuilder extends Builder
{

    public function __construct()
    {
        parent::__construct();
        $this->use(new WebExtension());
        $this->use(new ReactPHPExtension());
    }

    public function build(BuildConfig $config): ReactPHPApplication
    {
        /** @var ReactPHPApplication $application */
        $application = $this->build_container($config)->make(ReactPHPApplication::class,[
            'handler' => \DI\get(WebExtension::HANDLER),
        ]);

        return $application;
    }

}