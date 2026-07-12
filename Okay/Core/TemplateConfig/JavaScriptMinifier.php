<?php

declare(strict_types=1);

namespace Okay\Core\TemplateConfig;

use Wikimedia\Minify\JavaScriptMinifier as WikimediaJavaScriptMinifier;

final class JavaScriptMinifier
{
    public function minify(string $javascript): string
    {
        return WikimediaJavaScriptMinifier::minify($javascript);
    }
}
