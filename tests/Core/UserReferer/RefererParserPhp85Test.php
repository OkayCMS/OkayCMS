<?php

declare(strict_types=1);

namespace Core\UserReferer;

use ErrorException;
use Okay\Core\UserReferer\UserReferer;
use PHPUnit\Framework\TestCase;
use Snowplow\RefererParser\Medium;
use Snowplow\RefererParser\Parser;

final class RefererParserPhp85Test extends TestCase
{
    public function testKnownRefererWithoutQueryDoesNotTriggerPhp85Deprecation(): void
    {
        $parser = new Parser(UserReferer::createConfigReader());

        set_error_handler(static function (int $severity, string $message): bool {
            if ($severity === E_DEPRECATED) {
                throw new ErrorException($message, 0, $severity);
            }

            return false;
        });

        try {
            $referer = $parser->parse('https://www.google.com/', 'https://example.test/');
        } finally {
            restore_error_handler();
        }

        self::assertSame(Medium::SEARCH, $referer->getMedium());
        self::assertSame('Google', $referer->getSource());
        self::assertNull($referer->getSearchTerm());
    }
}
