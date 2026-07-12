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

    /** @var string|array<int, mixed>|\Closure */
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
        LockInterface $lock
    ) {
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

        try {
            $serviceLocator = ServiceLocator::getInstance();

            if (is_string($this->command)) {
                exec($this->command);
            } elseif (is_array($this->command)) {
                $commandClass = $this->command[0] ?? null;
                $commandMethod = $this->command[1] ?? null;
                if ((!is_string($commandClass) && !is_object($commandClass)) || !is_string($commandMethod)) {
                    throw new \Exception('The command is not callable');
                }

                $reflection = new \ReflectionMethod($commandClass, $commandMethod);

                if ($reflection->isStatic() || is_object($commandClass)) {
                    $command = [$commandClass, $commandMethod];
                } elseif ($serviceLocator->hasService($commandClass)) {
                    $command = [
                        $serviceLocator->getService($commandClass),
                        $commandMethod
                    ];
                } else {
                    $command = [
                        new $commandClass(),
                        $commandMethod
                    ];
                }

                if (!is_callable($command)) {
                    throw new \Exception('The command is not callable');
                }

                call_user_func_array($command, $this->getMethodArguments($reflection));
            } elseif ($this->command instanceof \Closure) {
                $reflection = new \ReflectionFunction($this->command);

                call_user_func_array($this->command, $this->getMethodArguments($reflection));
            } else {
                throw new \Exception('The command is not callable');
            }
        } finally {
            if ($this->lock->isAcquired()) {
                $this->lock->release();
            }
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
