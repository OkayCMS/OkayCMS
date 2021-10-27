<?php

namespace Okay\Core\Console\Commands\Scheduler;

use Okay\Core\Console\Command;
use Okay\Core\Modules\Modules;
use Okay\Core\Scheduler\Scheduler;
use Okay\Core\Scheduler\Task;
use Symfony\Component\Console\Helper\Table;

class SchedulerListCommand extends Command
{
    protected static $defaultName = 'scheduler:list';
    protected static $defaultDescription = 'Show the list of scheduler task';

    protected function configure(): void
    {
        $this->setHelp('This command shows a list of all scheduled tasks.');
    }

    protected function handle(
        Modules $modules,
        Scheduler $scheduler
    ) {
        $modules->startEnabledModules();

        $tasks = $scheduler->getTasks();
        $table = $this->builTable($tasks);
        $table->render();

        return Command::SUCCESS;
    }

    private function builTable(array $tasks): Table
    {
        $table = new Table($this->output);
        $table->setHeaders([
            'id',
            'Name',
            'Time',
            'Command'
        ]);

        /** @var Task $task */
        foreach ($tasks as $task) {
            $table->addRow([
                $task->getId(),
                $task->getName(),
                $task->getSchedule()->getExpression(),
                $this->formatCommand($task->getCommand())
            ]);
        }

        return $table;
    }

    private function formatCommand($command): string
    {
        if (is_array($command)) {
            $result = $command[0].'::'.$command[1];
        } elseif ($command instanceof \Closure) {
            $ref  = new \ReflectionFunction($command);
            $result = $ref->getName().':'.$ref->getStartLine();
        } else {
            $result = $command;
        }

        return $result;
    }
}