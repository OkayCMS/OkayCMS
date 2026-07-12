<?php

declare(strict_types=1);

namespace Scripts;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class ChangelogDraftScriptTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/okay-changelog-draft-' . bin2hex(random_bytes(8));
        mkdir($this->workspace, 0777, true);
        $this->createFixtureRepository();
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->workspace);
    }

    public function testPrintsGroupedDraftForConventionalCommits(): void
    {
        $this->writeFile('feature.txt', 'feature');
        $this->commit('feat(import): normalize CSV encoding');
        $this->writeFile('fix.txt', 'fix');
        $this->commit('fix(cli): respect debug mode in console entrypoint');
        $this->writeFile('security.txt', 'security');
        $this->commit('fix(filemanager): allow sanitized SVG uploads');

        $process = $this->runScript(['--from=1.0.0']);

        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        self::assertStringContainsString('### Додано', $process->getOutput());
        self::assertStringContainsString('feat(import): normalize CSV encoding', $process->getOutput());
        self::assertStringContainsString('### Виправлено', $process->getOutput());
        self::assertStringContainsString('fix(cli): respect debug mode in console entrypoint', $process->getOutput());
        self::assertStringContainsString('### Безпека', $process->getOutput());
        self::assertStringContainsString('fix(filemanager): allow sanitized SVG uploads', $process->getOutput());
    }

    public function testCheckPassesWhenUnreleasedSectionContainsCommitKeywords(): void
    {
        $this->replaceChangelog(
            <<<'MARKDOWN'
# Changelog

## [Невипущено]

### Виправлено

- Виправлено CLI entrypoint `ok`: debug mode тепер керує console startup errors.

## [1.0.0] - 2026-01-01
MARKDOWN
        );
        $this->writeFile('fix.txt', 'fix');
        $this->commit('fix(cli): respect debug mode in console entrypoint');

        $process = $this->runScript(['--from=1.0.0', '--check']);

        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        self::assertStringContainsString('likely covers 1.0.0..HEAD', $process->getOutput());
    }

    public function testCheckMatchesProjectTerminologyInUkrainianChangelog(): void
    {
        $this->replaceChangelog(
            <<<'MARKDOWN'
# Changelog

## [Невипущено]

### Виправлено

- Виправлено видалення товарів із кошика при натисканні Enter у checkout-полях.

## [1.0.0] - 2026-01-01
MARKDOWN
        );
        $this->writeFile('cart.txt', 'cart');
        $this->commit('fix(cart): prevent Enter from removing purchases');

        $process = $this->runScript(['--from=1.0.0', '--check']);

        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        self::assertStringContainsString('likely covers 1.0.0..HEAD', $process->getOutput());
    }

    public function testCheckFailsWhenCandidateCommitIsMissingFromUnreleasedSection(): void
    {
        $this->writeFile('fix.txt', 'fix');
        $this->commit('fix(cli): respect debug mode in console entrypoint');

        $process = $this->runScript(['--from=1.0.0', '--check']);

        self::assertSame(1, $process->getExitCode());
        self::assertStringContainsString(
            'fix(cli): respect debug mode in console entrypoint',
            $process->getErrorOutput()
        );
    }

    /**
     * @param list<string> $arguments
     */
    private function runScript(array $arguments): Process
    {
        $process = new Process(array_merge([
            PHP_BINARY,
            dirname(__DIR__, 2) . '/dev/scripts/changelog-draft.php',
            '--root=' . $this->workspace,
        ], $arguments));
        $process->run();

        return $process;
    }

    private function createFixtureRepository(): void
    {
        $this->replaceChangelog(
            <<<'MARKDOWN'
# Changelog

## [Невипущено]

### Виправлено

- Поки немає змін.

## [1.0.0] - 2026-01-01
MARKDOWN
        );

        $this->runGit(['init']);
        $this->runGit(['add', '.']);
        $this->runGit([
            '-c',
            'user.name=Test',
            '-c',
            'user.email=test@example.test',
            'commit',
            '-m',
            'chore(release): fixture release',
        ]);
        $this->runGit(['tag', '1.0.0']);
    }

    private function replaceChangelog(string $content): void
    {
        $this->writeFile('CHANGELOG.md', $content . "\n");
    }

    private function writeFile(string $path, string $content): void
    {
        $fullPath = $this->workspace . '/' . $path;
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($fullPath, $content);
    }

    private function commit(string $message): void
    {
        $this->runGit(['add', '.']);
        $this->runGit([
            '-c',
            'user.name=Test',
            '-c',
            'user.email=test@example.test',
            'commit',
            '-m',
            $message,
        ]);
    }

    /**
     * @param list<string> $arguments
     */
    private function runGit(array $arguments): void
    {
        $process = new Process(array_merge(['git', '-C', $this->workspace], $arguments));
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
