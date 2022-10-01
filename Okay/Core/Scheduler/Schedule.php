<?php

namespace Okay\Core\Scheduler;

use Symfony\Component\Lock\LockFactory;

class Schedule
{
    /** @var string|array */
    private $command;

    /** @var string */
    private $name;

    /** @var string */
    private $time = '0 0 * * *';

    /** @var int */
    private $timeout = 3600;

    /** @var bool */
    private $overlap = true;

    public function __construct($command)
    {
        $this->command = $command;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function time(string $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function timeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function overlap(bool $value = true): self
    {
        $this->overlap = $value;

        return $this;
    }

    public function buildTask(LockFactory $lockFactory): Task
    {
        if ($this->command instanceof \Closure) {
            $ref  = new \ReflectionFunction($this->command);
            $file = new \SplFileObject($ref->getFileName());
            $file->seek($ref->getStartLine()-1);

            $content = '';
            while ($file->key() < $ref->getEndLine()) {
                $content .= $file->current();
                $file->next();
            }

            $lock = $lockFactory->createLock(md5(serialize([$content, $ref->getStaticVariables()])));
        } else {
            $lock = $lockFactory->createLock(md5(serialize($this->command)));
        }

        return new Task($this->command, $this->time, $this->timeout, $this->name, $this->overlap, $lock);
    }
}