<?php

namespace Vendor;

use axy\sourcemap\parsing\Line;
use axy\sourcemap\PosMap;
use PHPUnit\Framework\TestCase;

final class AxySourcemapLineConcatTest extends TestCase
{
    public function testConcatIgnoresNullSourceIndexesWithoutDeprecation(): void
    {
        $line = new Line(0, [
            new PosMap(
                ['line' => 0, 'column' => 0],
                ['fileIndex' => null, 'nameIndex' => null]
            ),
        ]);

        $deprecations = [];
        set_error_handler(static function (int $severity, string $message) use (&$deprecations): bool {
            if ($severity === E_DEPRECATED) {
                $deprecations[] = $message;

                return true;
            }

            return false;
        });

        try {
            $line->concat(1, 0, [], []);
        } finally {
            restore_error_handler();
        }

        self::assertSame([], $deprecations);
    }
}
