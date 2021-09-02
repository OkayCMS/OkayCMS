<?php

namespace Okay\Core\Console\Commands\Scheduler;

use Okay\Core\Console\Command;
use Okay\Core\Modules\Modules;
use Okay\Core\Scheduler\Scheduler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;

class SchedulerRunCommand extends Command
{
    protected static $defaultName = 'scheduler:run';
    protected static $defaultDescription = 'Run scheduled tasks';

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows to run scheduling tasks.')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Run all tasks regardless of schedule'
            )
            ->addOption(
                'max_execution_time',
                'met',
                InputOption::VALUE_REQUIRED,
                'Run all tasks regardless of schedule',
                60
            );
    }

    protected function handle(
        Modules   $modules,
        Scheduler $scheduler
    ): int {
        ini_set('max_execution_time', $this->input->getOption('max_execution_time'));

        $modules->startEnabledModules();
        $this->output->setVerbosity(128);

        $logger = new ConsoleLogger($this->output);

        $scheduler->pushLogger($logger);
        $scheduler->run($this->input->getOption('force'));

        return Command::SUCCESS;
    }
}