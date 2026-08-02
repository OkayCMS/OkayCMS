<?php

declare(strict_types=1);

namespace Security;

use PHPUnit\Framework\TestCase;

final class FilemanagerUploadPolicyTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'okaycms.test';
        $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '/backend/design/js/filemanager/upload.php';
        $_SERVER['SCRIPT_FILENAME'] = $_SERVER['SCRIPT_FILENAME'] ?? dirname(__DIR__, 2) . '/backend/design/js/filemanager/upload.php';
        $_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? 'okaycms.test';
        $_SERVER['SERVER_PORT'] = $_SERVER['SERVER_PORT'] ?? 80;
        $previousDirectory = getcwd();
        self::assertIsString($previousDirectory);

        chdir(dirname(__DIR__, 2) . '/backend/design/js/filemanager');
        $GLOBALS['config'] = ['default_language' => 'en_EN'];
        $_SESSION['RF'] = [
            'verify' => 'RESPONSIVEfilemanager',
            'language' => 'en_EN',
        ];
        require_once 'include/utils.php';
        chdir($previousDirectory);
    }

    public function testActiveWebExtensionsAreRemovedFromDefaultAllowlistExceptSanitizedSvgImages(): void
    {
        $config = file_get_contents(dirname(__DIR__, 2) . '/backend/design/js/filemanager/config/config.php');

        self::assertIsString($config);
        preg_match("/'ext_img'\\s*=>\\s*array\\((.*?)\\)/s", $config, $imageMatch);
        preg_match("/'ext_file'\\s*=>\\s*array\\((.*?)\\)/s", $config, $fileMatch);
        preg_match("/'editable_text_file_exts'\\s*=>\\s*array\\((.*?)\\)/s", $config, $editableMatch);
        $imageExtensions = $imageMatch[1] ?? '';
        $configuredCreationExtensions = implode("\n", [$fileMatch[1] ?? '', $editableMatch[1] ?? '']);

        self::assertStringContainsString("'svg'", $imageExtensions);
        self::assertStringNotContainsString("'svg'", $configuredCreationExtensions);

        foreach (['html', 'xhtml', 'xml', 'sql', 'css'] as $blockedExtension) {
            self::assertStringNotContainsString("'" . $blockedExtension . "'", $configuredCreationExtensions);
        }
        self::assertStringNotContainsString("'',", $configuredCreationExtensions);
    }

    public function testRemoteUrlUploadIsDeniedAtUploadEntrypoint(): void
    {
        $upload = file_get_contents(dirname(__DIR__, 2) . '/backend/design/js/filemanager/upload.php');
        $config = file_get_contents(dirname(__DIR__, 2) . '/backend/design/js/filemanager/config/config.php');

        self::assertIsString($upload);
        self::assertIsString($config);
        self::assertStringContainsString("if (isset(\$_POST['url']))", $upload);
        self::assertStringContainsString('http_response_code(403)', $upload);
        self::assertStringNotContainsString('curl_init($url)', $upload);
        self::assertMatchesRegularExpression("/'url_upload'\\s*=>\\s*false/", $config);
    }

    public function testUploadEntrypointReturnsJsonForEarlyFileErrors(): void
    {
        $upload = file_get_contents(dirname(__DIR__, 2) . '/backend/design/js/filemanager/upload.php');

        self::assertIsString($upload);
        self::assertStringContainsString('function sendUploadErrorResponse', $upload);
        self::assertStringContainsString("sendUploadErrorResponse(trans('wrong path')", $upload);
        self::assertStringContainsString("sendUploadErrorResponse(trans('wrong extension')", $upload);
    }

    public function testSvgUploadsAreSanitizedBeforeUploadHandlerMovesFile(): void
    {
        $upload = file_get_contents(dirname(__DIR__, 2) . '/backend/design/js/filemanager/upload.php');

        self::assertIsString($upload);
        self::assertStringContainsString('sanitizeFilemanagerSvg($uploadedFile)', $upload);
    }

    public function testSvgSanitizerRemovesActiveContentAndKeepsSafeSvg(): void
    {
        $path = sys_get_temp_dir() . '/okay-filemanager-svg-' . bin2hex(random_bytes(4)) . '.svg';
        file_put_contents($path, <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" onload="alert(1)">
  <script>alert(1)</script>
  <foreignObject><body onload="alert(2)">x</body></foreignObject>
  <a href="javascript:alert(3)"><path d="M0 0h10v10z" onclick="alert(4)" /></a>
  <use xlink:href="#safe-shape" />
  <path id="safe-shape" d="M1 1h8v8z" fill="currentColor" />
</svg>
SVG);

        try {
            self::assertTrue(\sanitizeFilemanagerSvg($path));

            $sanitized = file_get_contents($path);
            self::assertIsString($sanitized);
            self::assertStringContainsString('<svg', $sanitized);
            self::assertStringContainsString('#safe-shape', $sanitized);
            self::assertStringNotContainsString('<script', $sanitized);
            self::assertStringNotContainsString('foreignObject', $sanitized);
            self::assertStringNotContainsString('onload', $sanitized);
            self::assertStringNotContainsString('onclick', $sanitized);
            self::assertStringNotContainsString('javascript:', $sanitized);
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    public function testSvgSanitizerRejectsNonSvgXml(): void
    {
        $path = sys_get_temp_dir() . '/okay-filemanager-svg-' . bin2hex(random_bytes(4)) . '.svg';
        file_put_contents($path, '<html><body>not svg</body></html>');

        try {
            self::assertFalse(\sanitizeFilemanagerSvg($path));
        } finally {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    public function testFilemanagerPreviewUrlsUseContextAwareEncoding(): void
    {
        $dialog = file_get_contents(dirname(__DIR__, 2) . '/backend/design/js/filemanager/dialog.php');
        $ajax = file_get_contents(dirname(__DIR__, 2) . '/backend/design/js/filemanager/ajax_calls.php');

        self::assertIsString($dialog);
        self::assertIsString($ajax);
        self::assertStringContainsString('function rfm_attr', $dialog);
        self::assertStringContainsString('function rfm_query', $dialog);
        self::assertStringContainsString('htmlentities((string)$preview_file', $ajax);
        self::assertStringContainsString('rfm_attr($cad_url)', $ajax);
        self::assertStringContainsString('rfm_attr($googledoc_url)', $ajax);
    }

    public function testFilemanagerRenameKeepsOriginalExtensionContract(): void
    {
        $execute = file_get_contents(dirname(__DIR__, 2) . '/backend/design/js/filemanager/execute.php');

        self::assertIsString($execute);
        $renameBlock = self::extractSwitchCase($execute, "case 'rename_file':");
        $duplicateBlock = self::extractSwitchCase($execute, "case 'duplicate_file':");

        self::assertStringContainsString('$name = fix_filename($name, $config);', $renameBlock);
        self::assertStringContainsString('rename_file($path, $name, $ftp, $config)', $renameBlock);
        self::assertStringNotContainsString('requireAllowedFilemanagerName($name, $config);', $renameBlock);

        self::assertStringContainsString('$name = fix_filename($name, $config);', $duplicateBlock);
        self::assertStringContainsString('duplicate_file($path, $name, $ftp, $config)', $duplicateBlock);
        self::assertStringNotContainsString('requireAllowedFilemanagerName($name, $config);', $duplicateBlock);
    }

    public function testFilemanagerDownloadFormUsesDependencyFreeSubmit(): void
    {
        $dialog = file_get_contents(dirname(__DIR__, 2) . '/backend/design/js/filemanager/dialog.php');

        self::assertIsString($dialog);
        self::assertStringContainsString("document.getElementById('form", $dialog);
        self::assertStringContainsString(').submit(); return false;', $dialog);
        self::assertStringNotContainsString("onclick=\"$('#form", $dialog);
    }

    private static function extractSwitchCase(string $source, string $case): string
    {
        $start = strpos($source, $case);
        self::assertIsInt($start);

        $nextCase = strpos($source, "\n        case ", $start + strlen($case));
        self::assertIsInt($nextCase);

        return substr($source, $start, $nextCase - $start);
    }
}
