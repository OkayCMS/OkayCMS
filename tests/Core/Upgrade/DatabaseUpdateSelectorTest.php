<?php

declare(strict_types=1);

namespace Core\Upgrade;

use Okay\Core\Upgrade\DatabaseUpdateSelector;
use PHPUnit\Framework\TestCase;

final class DatabaseUpdateSelectorTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/okay-db-update-selector-' . bin2hex(random_bytes(8));
        mkdir($this->workspace, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->workspace);
    }

    public function testSelectsTargetReleaseUpdate(): void
    {
        $this->writeUpdate('update_4.5.2.sql');
        $this->writeUpdate('update_4.6.0.sql');

        $updates = (new DatabaseUpdateSelector())->select($this->workspace, '4.5.2', '4.6.0');

        self::assertSame(['update_4.6.0.sql'], $this->basenames($updates));
    }

    public function testSelectsIntermediateUpdatesInVersionOrder(): void
    {
        $this->writeUpdate('update_4.6.0.sql');
        $this->writeUpdate('update_4.5.2.sql');
        $this->writeUpdate('update_4.5.0.sql');
        $this->writeUpdate('update_3.0.0_Beta.sql');

        $updates = (new DatabaseUpdateSelector())->select($this->workspace, '4.4.0', '4.6.0');

        self::assertSame(['update_4.5.0.sql', 'update_4.5.2.sql', 'update_4.6.0.sql'], $this->basenames($updates));
    }

    public function testNoMatchingUpdatesProducesNoOpSelection(): void
    {
        $this->writeUpdate('update_4.5.2.sql');

        $updates = (new DatabaseUpdateSelector())->select($this->workspace, '4.6.0', '4.6.1');

        self::assertSame([], $updates);
    }

    public function testInvalidVersionLabelsAreRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid version label');

        (new DatabaseUpdateSelector())->select($this->workspace, 'current', '4.6.0');
    }

    public function testHistoricalRepositoryFilenamesAreSelectable(): void
    {
        $updates = (new DatabaseUpdateSelector())->select(dirname(__DIR__, 3) . '/1DB_changes', '4.5.2', '4.6.0');

        self::assertSame(['update_4.6.0.sql'], $this->basenames($updates));
    }

    private function writeUpdate(string $filename): void
    {
        file_put_contents($this->workspace . '/' . $filename, '-- fixture');
    }

    /**
     * @param list<\Okay\Core\Upgrade\DatabaseUpdateFile> $updates
     * @return list<string>
     */
    private function basenames(array $updates): array
    {
        return array_map(static fn ($update): string => basename($update->getPath()), $updates);
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
