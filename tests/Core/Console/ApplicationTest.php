<?php

declare(strict_types=1);

namespace Core\Console;

use Okay\Core\Console\Application;
use Okay\Core\Console\Command;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    public function testApplicationRegistersStableCommandNames(): void
    {
        $application = new Application();

        self::assertSame('database:deploy', $application->find('database:deploy')->getName());
        self::assertSame('database:upgrade', $application->find('database:upgrade')->getName());
        self::assertSame('module:create', $application->find('module:create')->getName());
        self::assertSame('scheduler:run', $application->find('scheduler:run')->getName());
        self::assertSame('scheduler:task', $application->find('scheduler:task')->getName());
        self::assertSame('scheduler:list', $application->find('scheduler:list')->getName());
    }

    public function testApplicationRejectsNonProjectCommands(): void
    {
        $application = new Application();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Command must be an instance of ' . Command::class . '.');

        $application->registerCommand(\Symfony\Component\Console\Command\Command::class);
    }
}
