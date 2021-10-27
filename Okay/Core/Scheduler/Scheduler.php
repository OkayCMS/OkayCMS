<?php

namespace Okay\Core\Scheduler;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class Scheduler
{
    /** @var LockFactory */
    private $lockFactory;


    /** @var LoggerInterface[] */
    private $loggers = [];

    /** @var Task[] */
    private $tasks = [];

    public function __construct($loggerDir) {
        $this->lockFactory = new LockFactory(new FlockStore());

        $logger = new Logger('Scheduler');
        $logger->pushHandler(new RotatingFileHandler($loggerDir.'/scheduler/scheduler.log', 10));
        $this->pushLogger($logger);
    }

    public function registerSchedule(Schedule $schedule): void
    {
        $task = $schedule->buildTask($this->lockFactory);
        $this->tasks[$task->getId()] = $task;
    }

    public function run(bool $force = false): void
    {
        $subProcesses = [];
        foreach ($this->tasks as $key => $task) {
            if ($task->isDue() || $force) {
                $command = [PHP_BINARY, 'ok', 'scheduler:task', $task->getId()];
                if ($force) {
                    $command[] = '-f';
                }

                $subProcesses[$key]['process'] = $process = new Process($command);
                $subProcesses[$key]['time'] = $this->info("Task #{$task->getId()} ({$task->getName()}): Start");

                $process
                    ->setTimeout($task->getTimeout())
                    ->start(function ($type, $buffer) {
                        echo $buffer;
                });
            }
        }

        $this->waitForProcesses($subProcesses);
    }

    private function waitForProcesses(array $subProcesses): void
    {
        while (!empty($subProcesses)) {
            foreach ($subProcesses as $key => $subProcess) {
                if ($subProcess['process']->isRunning()) {
                    try {
                        $subProcess['process']->checkTimeout();
                    } catch (ProcessTimedOutException $e) {
                        $task = $this->tasks[$key];
                        $this->info("Task #{$task->getId()} ({$task->getName()}) ({$subProcess['time']}): Timeout");
                        unset($subProcesses[$key]);
                    }
                } else {
                    $task = $this->tasks[$key];
                    $this->info("Task #{$task->getId()} ({$task->getName()}) ({$subProcess['time']}): Finish");
                    unset($subProcesses[$key]);
                }
            }
            sleep(1);
        }
    }

    public function runTask(int $taskId, $force = false): void
    {
        if ($taskId && $task = $this->tasks[$taskId]) {
            if ($task->isDue() || $force) {
                try {
                    $task->run();
                } catch (\Exception $e) {
                    $this->info("Task #{$task->getId()} ({$task->getName()}): Error: {$e->getMessage()}");
                }
            }
        }
    }

    public function getTasks(): array
    {
        return $this->tasks;
    }

    public function pushLogger(LoggerInterface $logger): void
    {
        $this->loggers[] = $logger;
    }

    private function info(string $message, array $context = []): string
    {
        $time = date('Y-m-d H:i:s');
        if (!empty($this->loggers)) {
            foreach ($this->loggers as $logger) {
                $logger->info("($time) $message", $context);
            }
        }

        return $time;
    }
}