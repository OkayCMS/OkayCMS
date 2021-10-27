<?php

namespace Okay\Core\Console\Commands\Scheduler;

use Okay\Core\Console\Command;
use Okay\Core\Modules\Modules;
use Okay\Core\Scheduler\Scheduler;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;

class SchedulerTaskCommand extends Command
{
    protected static $defaultName = 'scheduler:task';
    protected static $defaultDescription = 'Run single task';

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows to run a single task.')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Run task regardless the schedule and overlaps'
            )
            ->addArgument(
                'task_id',
                InputArgument::REQUIRED,
                'Id of task that will be executed'
            );
    }

    protected function handle(
        Modules   $modules,
        Scheduler $scheduler
    ) {
        $modules->startEnabledModules();
        $this->output->setVerbosity(128);

        $logger = new ConsoleLogger($this->output);

        $scheduler->pushLogger($logger);
        $scheduler->runTask((int) $this->input->getArgument('task_id'), $this->input->getOption('force'));

        return Command::SUCCESS;
    }
}