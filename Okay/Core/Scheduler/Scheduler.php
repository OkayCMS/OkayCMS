<?php


namespace Okay\Core\Scheduler;


use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class Scheduler
{
    /** @var LoggerInterface[] */
    private $loggers = [];
    private $tasks = [];

    public function __construct()
    {
        $logger = new Logger('SCHEDULER');
        $logger->pushHandler(new RotatingFileHandler(dirname(__DIR__, 2).'/log/scheduler/scheduler.log', 2));
        $this->pushLogger($logger);
    }

    public function run($force = false)
    {
        $fp = fopen(__DIR__ . '/' . 'lock.tmp', 'w');
        if (flock($fp, LOCK_EX | LOCK_NB)) {
            $this->info('(' . (new \DateTime())->format('Y-m-d H:i:s') . ') Scheduler started.');

            /** @var Task $task */
            foreach ($this->tasks as $task) {
                foreach ($this->loggers as $logger) {
                    $task->pushLogger($logger);
                }
                $task->run($force);
            }

            $this->info('(' . (new \DateTime())->format('Y-m-d H:i:s') . ') Scheduler finished.');

            flock($fp, LOCK_UN);
            fclose($fp);
            @unlink(__DIR__ . '/' . 'lock.tmp');
        } else {
            $this->info('(' . (new \DateTime())->format('Y-m-d H:i:s') . ') One instance of the scheduler is already working.');
        }
    }

    public function addTask(string $name, string $timePattern, $callback, string $comment = '', $logger = null)
    {
        $this->tasks[] = new Task($name, $timePattern, $callback, $comment, $logger);
    }

    public function pushLogger(LoggerInterface $logger)
    {
        $this->loggers[] = $logger;
    }

    private function info($message, array $context = [])
    {
        if (!empty($this->loggers)) {
            foreach ($this->loggers as $logger) {
                $logger->info($message, $context);
            }
        }
    }
}