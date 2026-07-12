<?php

declare(strict_types=1);

namespace Scripts;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use ZipArchive;

final class BuildInstallPackageScriptTest extends TestCase
{
    private string $workspace;
    private string $fixtureRoot;
    private string $outputDir;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/okay-install-package-' . bin2hex(random_bytes(8));
        $this->fixtureRoot = $this->workspace . '/project';
        $this->outputDir = $this->workspace . '/packages';

        mkdir($this->fixtureRoot, 0777, true);
        mkdir($this->outputDir, 0777, true);
        $this->createFixtureRepository();
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->workspace);
    }

    public function testBuildsProductionInstallArchiveWithGeneratedInstallGuide(): void
    {
        $process = new Process([
            PHP_BINARY,
            dirname(__DIR__, 2) . '/dev/scripts/build-install-package.php',
            '--root=' . $this->fixtureRoot,
            '--ref=HEAD',
            '--version=9.9.9',
            '--output-dir=' . $this->outputDir,
        ]);
        $process->run();

        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        self::assertStringContainsString('Created ', $process->getOutput());

        $zipPath = $this->outputDir . '/okaycms_9.9.9_install.zip';
        self::assertFileExists($zipPath);

        $entries = $this->readZipEntries($zipPath);
        self::assertContains('okaycms_9.9.9/composer.json', $entries);
        self::assertContains('okaycms_9.9.9/Okay/Core/Foo.php', $entries);
        self::assertContains('okaycms_9.9.9/backend/index.php', $entries);
        self::assertContains('okaycms_9.9.9/config/config.php', $entries);
        self::assertContains('okaycms_9.9.9/scripts/apply-patches.php', $entries);
        self::assertContains('okaycms_9.9.9/INSTALL.md', $entries);

        self::assertNotContains('okaycms_9.9.9/docs/workflow/install-production.md', $entries);
        self::assertNotContains('okaycms_9.9.9/dev/scripts/dev-only.php', $entries);
        self::assertNotContains('okaycms_9.9.9/tests/FixtureTest.php', $entries);
        self::assertNotContains('okaycms_9.9.9/.github/workflows/test.yml', $entries);
        self::assertNotContains('okaycms_9.9.9/vendor/package/file.php', $entries);
        self::assertNotContains('okaycms_9.9.9/README.md', $entries);

        $zip = new ZipArchive();
        self::assertTrue($zip->open($zipPath));
        self::assertStringContainsString(
            'Fixture installation guide',
            (string) $zip->getFromName('okaycms_9.9.9/INSTALL.md')
        );
        $zip->close();
    }

    public function testDryRunReportsIncludedAndExcludedFilesWithoutCreatingArchive(): void
    {
        $process = new Process([
            PHP_BINARY,
            dirname(__DIR__, 2) . '/dev/scripts/build-install-package.php',
            '--root=' . $this->fixtureRoot,
            '--ref=HEAD',
            '--version=9.9.9',
            '--output-dir=' . $this->outputDir,
            '--dry-run',
        ]);
        $process->run();

        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        self::assertStringContainsString('Package directory: okaycms_9.9.9', $process->getOutput());
        self::assertStringContainsString('Production files: 5', $process->getOutput());
        self::assertStringContainsString('Generated package files: 1', $process->getOutput());
        self::assertFileDoesNotExist($this->outputDir . '/okaycms_9.9.9_install.zip');
    }

    /**
     * @return list<string>
     */
    private function readZipEntries(string $zipPath): array
    {
        $zip = new ZipArchive();
        self::assertTrue($zip->open($zipPath));

        $entries = [];
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = $zip->getNameIndex($index);
            if ($name !== false) {
                $entries[] = $name;
            }
        }

        $zip->close();
        sort($entries);

        return $entries;
    }

    private function createFixtureRepository(): void
    {
        $prodIgnore = (string) file_get_contents(dirname(__DIR__, 2) . '/.prodignore');
        $this->writeFixtureFile('.prodignore', $prodIgnore);
        $this->writeFixtureFile('composer.json', '{"name":"okaycms/fixture"}');
        $this->writeFixtureFile('README.md', 'dev-facing readme');
        $this->writeFixtureFile('Okay/Core/Foo.php', '<?php');
        $this->writeFixtureFile('backend/index.php', '<?php');
        $this->writeFixtureFile('config/config.php', 'db');
        $this->writeFixtureFile('scripts/apply-patches.php', '<?php');
        $this->writeFixtureFile('docs/workflow/install-production.md', '# Fixture installation guide');
        $this->writeFixtureFile('dev/scripts/dev-only.php', '<?php');
        $this->writeFixtureFile('tests/FixtureTest.php', '<?php');
        $this->writeFixtureFile('.github/workflows/test.yml', 'name: test');
        $this->writeFixtureFile('vendor/package/file.php', '<?php');

        $this->runGit(['init']);
        $this->runGit(['add', '.']);
        $this->runGit([
            '-c',
            'user.name=Test',
            '-c',
            'user.email=test@example.test',
            'commit',
            '-m',
            'fixture',
        ]);
    }

    private function writeFixtureFile(string $path, string $content): void
    {
        $fullPath = $this->fixtureRoot . '/' . $path;
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($fullPath, $content);
    }

    /**
     * @param list<string> $arguments
     */
    private function runGit(array $arguments): void
    {
        $process = new Process(array_merge(['git', '-C', $this->fixtureRoot], $arguments));
        $process->run();

        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                rmdir($fileInfo->getPathname());
                continue;
            }

            unlink($fileInfo->getPathname());
        }

        rmdir($directory);
    }
}
