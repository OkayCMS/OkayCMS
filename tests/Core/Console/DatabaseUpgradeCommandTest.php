<?php

declare(strict_types=1);

namespace Core\Console;

use Okay\Core\Console\Commands\Database\DatabaseUpgradeCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class DatabaseUpgradeCommandTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/okay-db-upgrade-command-' . bin2hex(random_bytes(8));
        mkdir($this->workspace, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->workspace);
    }

    public function testDryRunListsSelectedSqlFilesWithoutDatabaseAccess(): void
    {
        $this->writeUpdate('update_4.6.0.sql');

        $tester = new CommandTester(new DatabaseUpgradeCommand());
        $exitCode = $tester->execute([
            '--from' => '4.5.2',
            '--to' => '4.6.0',
            '--updates-dir' => $this->workspace,
            '--dry-run' => true,
        ]);

        self::assertSame(Command::SUCCESS, $exitCode);
        self::assertStringContainsString('Database upgrade range: 4.5.2 -> 4.6.0', $tester->getDisplay());
        self::assertStringContainsString('update_4.6.0.sql', $tester->getDisplay());
        self::assertStringContainsString('Post-SQL normalizers: 1', $tester->getDisplay());
        self::assertStringContainsString('social-share-settings', $tester->getDisplay());
    }

    public function testMissingVersionOptionsFailBeforeDatabaseAccess(): void
    {
        $tester = new CommandTester(new DatabaseUpgradeCommand());
        $exitCode = $tester->execute([
            '--from' => '4.5.2',
            '--updates-dir' => $this->workspace,
            '--dry-run' => true,
        ]);

        self::assertSame(Command::FAILURE, $exitCode);
        self::assertStringContainsString('Both --from and --to options are required.', $tester->getDisplay());
    }

    private function writeUpdate(string $filename): void
    {
        file_put_contents($this->workspace . '/' . $filename, '-- fixture');
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
