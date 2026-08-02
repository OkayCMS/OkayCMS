<?php

declare(strict_types=1);

namespace Core\Console;

use Okay\Core\Console\Command;
use Okay\Core\Console\Commands\Database\DatabaseDeployCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class DatabaseDeployCommandTest extends TestCase
{
    public function testNonInteractiveRunWithoutYesFailsImmediately(): void
    {
        $tester = new CommandTester(new DatabaseDeployCommand());
        $exitCode = $tester->execute([], ['interactive' => false]);

        self::assertSame(Command::FAILURE, $exitCode);
    }

    public function testYesSkipsPromptsAndFailsWhenSqlFileMissing(): void
    {
        $tester = new CommandTester(new DatabaseDeployCommand());
        $exitCode = $tester->execute([
            '--yes' => true,
            '--file_path' => '/tmp/okaycms-missing-clean-db.sql',
        ], ['interactive' => false]);

        self::assertSame(Command::FAILURE, $exitCode);
        self::assertStringContainsString('Database file does not exist', $tester->getDisplay());
    }
}
