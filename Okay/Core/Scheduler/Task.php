<?php

namespace Okay\Core\Scheduler;

use Cron\CronExpression;
use Okay\Core\OkayContainer\MethodDI;
use Okay\Core\ServiceLocator;
use Psr\Log\LoggerInterface;

class Task
{
    use MethodDI;

    /** @var CronExpression */
    private $schedule;

    /** @var callable */
    private $callback;

    /** @var string */
    private $name;

    /** @var string */
    private $comment;

    private $output;

    /** @var LoggerInterface[] */
    private $loggers;

    public function __construct(string $name, string $timePattern, $callback, string $comment = '', LoggerInterface $logger = null)
    {
        $this->name = $name;
        $this->schedule = new CronExpression($timePattern);
        $this->callback = $callback;
        $this->comment = $comment;
        if (!empty($logger)) {
            $this->pushLogger($logger);
        }
    }

    public function run($force = false)
    {
        if ($this->isDue() || $force) {
            $serviceLocator = ServiceLocator::getInstance();

            $this->info("(".(new \DateTime())->format('Y-m-d H:i:s').") (Started) Task \"{$this->name}\" ({$this->comment}).");

            if (is_string($this->callback)) {
                exec($this->callback.' > /dev/null &');
                $this->info("(".(new \DateTime())->format('Y-m-d H:i:s').") (Background) Task \"{$this->name}\".");
            } else {
                if (is_array($this->callback)) {
                    $reflection = new \ReflectionMethod($this->callback[0], $this->callback[1]);
                    if ($reflection->isStatic() || is_object($this->callback[0])) {
                        $callback = $this->callback;
                    } elseif ($serviceLocator->hasService($this->callback[0])) {
                        $callback = [
                            $serviceLocator->getService($this->callback[0]),
                            $this->callback[1]
                        ];
                    } else {
                        $callback = [
                            new $this->callback[0](),
                            $this->callback[1]
                        ];
                    }
                } else {
                    $reflection = new \ReflectionFunction($this->callback);
                    $callback = $this->callback;
                }

                $result = call_user_func_array($callback, $this->getMethodArguments($reflection));
                $this->info("(".(new \DateTime())->format('Y-m-d H:i:s').") (Finished) Task \"{$this->name}\".");
                if (!empty($result)) {
                    $this->info("Result: {$result}");
                }
            }
        } else {
            $this->info("(".(new \DateTime())->format('Y-m-d H:i:s').") (Skipped) Task \"{$this->name}\" ({$this->comment}).");
        }
    }

    public function isDue()
    {
        return $this->schedule->isDue();
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