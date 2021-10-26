<?php


namespace Okay\Core\Console;


use Okay\Core\Console\Commands\Database\DatabaseDeployCommand;
use Okay\Core\Console\Commands\Module\ModuleCreateCommand;
use Okay\Core\Console\Commands\Scheduler\SchedulerListCommand;
use Okay\Core\Console\Commands\Scheduler\SchedulerRunCommand;
use Okay\Core\Console\Commands\Scheduler\SchedulerTaskCommand;
use Symfony\Component\Console\Application AS SymfonyApplication;

class Application extends SymfonyApplication
{
    private $commands = [
        DatabaseDeployCommand::class,
        ModuleCreateCommand::class,
        SchedulerRunCommand::class,
        SchedulerTaskCommand::class,
        SchedulerListCommand::class,
    ];

    public function __construct()
    {
        parent::__construct();

        foreach ($this->commands as $commandClass) {
            $this->registerCommand($commandClass);
        }
    }

    public function registerCommand(string $commandClass): void
    {
        if (!($class = new $commandClass()) instanceof Command) {
            throw new \Exception("Command must be an instance of ".Command::class.".");
        }
        $this->add(new $class());
    }
}