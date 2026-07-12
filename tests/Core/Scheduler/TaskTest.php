<?php

declare(strict_types=1);

namespace Core\Scheduler;

use Okay\Core\Scheduler\Task;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Lock\LockInterface;

final class TaskTest extends TestCase
{
    public function testRunReleasesAcquiredLockWhenCommandThrows(): void
    {
        $lock = new class implements LockInterface {
            public int $acquireCalls = 0;
            public int $releaseCalls = 0;
            private bool $acquired = false;

            public function acquire(bool $blocking = false): bool
            {
                $this->acquireCalls++;
                $this->acquired = true;

                return true;
            }

            public function refresh(?float $ttl = null): void
            {
            }

            public function isAcquired(): bool
            {
                return $this->acquired;
            }

            public function release(): void
            {
                $this->releaseCalls++;
                $this->acquired = false;
            }

            public function isExpired(): bool
            {
                return false;
            }

            public function getRemainingLifetime(): ?float
            {
                return null;
            }
        };

        $task = new Task(
            static function (): void {
                throw new RuntimeException('Scheduler task failed');
            },
            '* * * * *',
            60,
            'failing test task',
            false,
            $lock
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Scheduler task failed');

        try {
            $task->run();
        } finally {
            self::assertSame(1, $lock->acquireCalls);
            self::assertSame(1, $lock->releaseCalls);
            self::assertFalse($lock->isAcquired());
        }
    }

    public function testRunKeepsCurrentBehaviorWhenLockCannotBeAcquired(): void
    {
        $lock = new class implements LockInterface {
            public int $acquireCalls = 0;
            public int $releaseCalls = 0;

            public function acquire(bool $blocking = false): bool
            {
                $this->acquireCalls++;

                return false;
            }

            public function refresh(?float $ttl = null): void
            {
            }

            public function isAcquired(): bool
            {
                return false;
            }

            public function release(): void
            {
                $this->releaseCalls++;
            }

            public function isExpired(): bool
            {
                return false;
            }

            public function getRemainingLifetime(): ?float
            {
                return null;
            }
        };
        $commandRan = false;

        $task = new Task(
            static function () use (&$commandRan): void {
                $commandRan = true;
            },
            '* * * * *',
            60,
            'lock failure characterization task',
            false,
            $lock
        );

        $task->run();

        self::assertTrue($commandRan);
        self::assertSame(1, $lock->acquireCalls);
        self::assertSame(0, $lock->releaseCalls);
    }
}
