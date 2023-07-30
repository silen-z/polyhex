<?php

declare(strict_types=1);

namespace SilenZ\App\UI\Home;

use Polyhex\Web\Request\ExtractedRequest;
use Polyhex\Web\Request\Extractor;

final class HomeRequest extends ExtractedRequest
{

    public function __construct(
        #[Extractor\QueryParam('lang')]
        public string $language = 'en',
    )
    {
    }

}
