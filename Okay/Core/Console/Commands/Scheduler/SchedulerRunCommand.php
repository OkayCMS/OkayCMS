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
                'Run tasks regardless the schedule and overlaps'
            );
    }

    protected function handle(
        Modules   $modules,
        Scheduler $scheduler
    ): int {
        $modules->startEnabledModules();
        $this->output->setVerbosity(128);

        $logger = new ConsoleLogger($this->output);

        $scheduler->pushLogger($logger);
        $scheduler->run($this->input->getOption('force'));

        return Command::SUCCESS;
    }
}