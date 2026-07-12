<?php

declare(strict_types=1);

namespace Core\Scheduler;

use Okay\Core\Scheduler\Schedule;
use Okay\Core\Scheduler\Scheduler;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use ReflectionMethod;
use Symfony\Component\Process\Process;

final class SchedulerProcessTest extends TestCase
{
    public function testWaitForProcessesLogsFinishedSubprocesses(): void
    {
        $logger = new ArrayLogger();
        $scheduler = $this->createScheduler($logger);
        $scheduler->registerSchedule((new Schedule(static function (): void {
        }))->name('finished process'));
        $task = current($scheduler->getTasks());

        $process = new Process([PHP_BINARY, '-r', '']);
        $process->start();

        $this->waitForProcesses($scheduler, [
            $task->getId() => [
                'process' => $process,
                'time' => '2026-05-06 12:00:00',
            ],
        ]);

        self::assertFalse($process->isRunning());
        self::assertTrue($logger->hasMessageContaining("Task #{$task->getId()} (finished process)"));
        self::assertTrue($logger->hasMessageContaining('Finish'));
    }

    public function testWaitForProcessesLogsTimedOutSubprocesses(): void
    {
        $logger = new ArrayLogger();
        $scheduler = $this->createScheduler($logger);
        $scheduler->registerSchedule((new Schedule(static function (): void {
        }))->name('timed process'));
        $task = current($scheduler->getTasks());

        $process = new Process([PHP_BINARY, '-r', 'sleep(2);']);
        $process->setTimeout(0.1);
        $process->start();

        $this->waitForProcesses($scheduler, [
            $task->getId() => [
                'process' => $process,
                'time' => '2026-05-06 12:00:00',
            ],
        ]);

        self::assertTrue($logger->hasMessageContaining("Task #{$task->getId()} (timed process)"));
        self::assertTrue($logger->hasMessageContaining('Timeout'));
    }

    /**
     * @param array<int, array{process: Process, time: string}> $subProcesses
     */
    private function waitForProcesses(Scheduler $scheduler, array $subProcesses): void
    {
        $method = new ReflectionMethod(Scheduler::class, 'waitForProcesses');
        $method->invoke($scheduler, $subProcesses);
    }

    private function createScheduler(ArrayLogger $logger): Scheduler
    {
        $scheduler = new Scheduler(sys_get_temp_dir());
        $scheduler->pushLogger($logger);

        return $scheduler;
    }
}

final class ArrayLogger extends AbstractLogger
{
    /** @var string[] */
    private array $messages = [];

    /**
     * @param array<string, mixed> $context
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->messages[] = (string) $message;
    }

    public function hasMessageContaining(string $needle): bool
    {
        foreach ($this->messages as $message) {
            if (str_contains($message, $needle)) {
                return true;
            }
        }

        return false;
    }
}
