<?php

declare(strict_types=1);

namespace SilenZ\App\UI\Home;

use Polyhex\Web\Handler\ExtractedRequest;
use Polyhex\Web\Routing\QueryParam;

final class HomeRequest extends ExtractedRequest
{

    public function __construct(
        #[QueryParam('lang')]
        public string $language = 'en',
    )
    {
    }

}
