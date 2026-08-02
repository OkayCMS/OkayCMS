<?php

declare(strict_types=1);

namespace Security;

use PHPUnit\Framework\TestCase;

final class FilemanagerAccessTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function guardedEntrypoints(): iterable
    {
        yield 'dialog' => ['backend/design/js/filemanager/dialog.php'];
        yield 'upload' => ['backend/design/js/filemanager/upload.php'];
        yield 'execute' => ['backend/design/js/filemanager/execute.php'];
        yield 'ajax' => ['backend/design/js/filemanager/ajax_calls.php'];
        yield 'download' => ['backend/design/js/filemanager/force_download.php'];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('guardedEntrypoints')]
    public function testFilemanagerEntrypointsRequireOkayPermissionGuard(string $path): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/' . $path);

        self::assertIsString($source);
        self::assertStringContainsString('okay_access.php', $source);
    }

    public function testTinymceOnlyExposesFilemanagerForImageManagers(): void
    {
        $template = file_get_contents(dirname(__DIR__, 2) . '/backend/design/html/tinymce_init.tpl');

        self::assertIsString($template);
        self::assertStringContainsString("in_array('images', \$manager->permissions)", $template);
        self::assertStringContainsString('external_filemanager_path', $template);
    }
}
