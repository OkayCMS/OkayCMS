<?php

namespace Okay\Core\Console\Commands\Scheduler;

use Okay\Core\Console\Command;
use Okay\Core\Modules\Modules;
use Okay\Core\Scheduler\Scheduler;
use Okay\Core\Scheduler\Task;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputOption;

class SchedulerListCommand extends Command
{
    protected static $defaultName = 'scheduler:list';
    protected static $defaultDescription = 'Show the list of scheduler task';

    protected function configure(): void
    {
        $this
            ->setHelp('This command shows a list of all scheduled tasks.')
            ->addOption(
                'json',
                null,
                InputOption::VALUE_NONE,
                'Output scheduled tasks as machine-readable JSON'
            );
    }

    protected function handle(
        Modules $modules,
        Scheduler $scheduler
    ): int {
        $modules->startEnabledModules();

        $tasks = $scheduler->getTasks();
        if ($this->input->getOption('json')) {
            $this->output->writeln(json_encode(
                $this->buildJsonPayload($tasks),
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            ));

            return Command::SUCCESS;
        }

        $table = $this->builTable($tasks);
        $table->render();

        return Command::SUCCESS;
    }

    /**
     * @param array<int|string, Task> $tasks
     * @return array{tasks: list<array{id: string, name: string, time: string, command: string}>}
     */
    private function buildJsonPayload(array $tasks): array
    {
        $payload = ['tasks' => []];

        /** @var Task $task */
        foreach ($tasks as $task) {
            $payload['tasks'][] = [
                'id' => $task->getId(),
                'name' => $task->getName(),
                'time' => (string) $task->getSchedule()->getExpression(),
                'command' => $this->formatCommand($task->getCommand()),
            ];
        }

        return $payload;
    }

    /**
     * @param array<int|string, Task> $tasks
     */
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
            $result = $command[0] . '::' . $command[1];
        } elseif ($command instanceof \Closure) {
            $ref  = new \ReflectionFunction($command);
            $result = $ref->getName() . ':' . $ref->getStartLine();
        } else {
            $result = $command;
        }

        return $result;
    }
}
