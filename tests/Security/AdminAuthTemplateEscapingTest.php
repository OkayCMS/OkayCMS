<?php

declare(strict_types=1);

namespace Security;

use PHPUnit\Framework\TestCase;

final class AdminAuthTemplateEscapingTest extends TestCase
{
    public function testPreAuthTemplateEscapesRequestAndServerValues(): void
    {
        $template = file_get_contents(dirname(__DIR__, 2) . '/backend/design/html/auth.tpl');

        self::assertIsString($template);
        self::assertStringContainsString('{$smarty.server.HTTP_HOST|escape}', $template);
        self::assertStringContainsString('{$login|escape}', $template);
        self::assertStringContainsString('{$recovery_login|escape}', $template);
        self::assertStringNotContainsString('{$smarty.server.HTTP_HOST}</p>', $template);
        self::assertStringNotContainsString('value="{$login}"', $template);
    }
}
