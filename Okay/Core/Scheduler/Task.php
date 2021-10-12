<?php

namespace Okay\Core\Scheduler;

use Cron\CronExpression;
use Okay\Core\OkayContainer\MethodDI;
use Okay\Core\ServiceLocator;
use Symfony\Component\Lock\LockInterface;

class Task
{
    use MethodDI;

    /** @var int */
    private static $tasksCounter = 1;


    /** @var int */
    private $id;

    /** @var string|array */
    private $command;

    /** @var CronExpression */
    private $schedule;

    /** @var int */
    private $timeout;

    /** @var string */
    private $name;

    /** @var bool */
    private $overlap;

    /** @var LockInterface */
    private $lock;

    public function __construct(
        $command,
        string $time,
        int $timout,
        ?string $name,
        bool $overlap,
        LockInterface $lock)
    {
        $this->id       = self::$tasksCounter++;
        $this->command  = $command;
        $this->schedule = new CronExpression($time);
        $this->timeout  = $timout;
        $this->name     = $name ?? "Task {$this->id}";
        $this->overlap  = $overlap;
        $this->lock     = $lock;
    }

    public function run(): void
    {
        $this->lock->acquire();

        $serviceLocator = ServiceLocator::getInstance();

        if (is_string($this->command)) {
            exec($this->command);
        } elseif (is_array($this->command)) {
            $reflection = new \ReflectionMethod($this->command[0], $this->command[1]);

            if ($reflection->isStatic() || is_object($this->command[0])) {
                $command = $this->command;
            } elseif ($serviceLocator->hasService($this->command[0])) {
                $command = [
                    $serviceLocator->getService($this->command[0]),
                    $this->command[1]
                ];
            } else {
                $command = [
                    new $this->command[0](),
                    $this->command[1]
                ];
            }

            call_user_func_array($command, $this->getMethodArguments($reflection));
        } elseif ($this->command instanceof \Closure) {
            $reflection = new \ReflectionFunction($this->command);

            call_user_func_array($this->command, $this->getMethodArguments($reflection));
        } else {
            throw new \Exception('The command is not callable');
        }

        if ($this->lock->isAcquired()) {
            $this->lock->release();
        }
    }

    public function isDue(): bool
    {
        return ($this->schedule->isDue() && ($this->overlap || !$this->isAcquired()));
    }

    private function isAcquired(): bool
    {
        if ($result = $this->lock->acquire()) {
            $this->lock->release();
        }

        return !$result;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getSchedule(): CronExpression
    {
        return $this->schedule;
    }
}