<?php

declare(strict_types=1);

namespace Core\Console;

use Okay\Core\Console\Application;
use Okay\Core\Console\Command;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class SchedulerCommandTest extends TestCase
{
    public function testSchedulerRunCommandKeepsForceOption(): void
    {
        $command = (new Application())->find('scheduler:run');
        $definition = $command->getDefinition();

        self::assertTrue($definition->hasOption('force'));
        self::assertSame('f', $definition->getOption('force')->getShortcut());
        self::assertFalse($definition->getOption('force')->acceptValue());
    }

    public function testSchedulerTaskCommandKeepsTaskIdArgumentAndForceOption(): void
    {
        $command = (new Application())->find('scheduler:task');
        $definition = $command->getDefinition();

        self::assertTrue($definition->hasArgument('task_id'));
        self::assertTrue($definition->getArgument('task_id')->isRequired());
        self::assertTrue($definition->hasOption('force'));
        self::assertSame('f', $definition->getOption('force')->getShortcut());
        self::assertFalse($definition->getOption('force')->acceptValue());
    }

    public function testSchedulerListCommandKeepsDescriptionAndNoRequiredArguments(): void
    {
        $command = (new Application())->find('scheduler:list');

        self::assertSame([], $command->getDefinition()->getArguments());
        self::assertSame('This command shows a list of all scheduled tasks.', $command->getHelp());
    }

    public function testSchedulerListCanReturnRegisteredTasksAsJson(): void
    {
        $tester = new CommandTester((new Application())->find('scheduler:list'));

        $exitCode = $tester->execute(['--json' => true], ['interactive' => false]);

        self::assertSame(Command::SUCCESS, $exitCode);

        $payload = json_decode($tester->getDisplay(), true);
        self::assertIsArray($payload, json_last_error_msg());
        self::assertArrayHasKey('tasks', $payload);
        self::assertIsArray($payload['tasks']);

        foreach ($payload['tasks'] as $task) {
            self::assertIsArray($task);
            self::assertArrayHasKey('id', $task);
            self::assertArrayHasKey('name', $task);
            self::assertArrayHasKey('time', $task);
            self::assertArrayHasKey('command', $task);
            self::assertIsString($task['id']);
            self::assertIsString($task['name']);
            self::assertIsString($task['time']);
            self::assertIsString($task['command']);
        }
    }
}
