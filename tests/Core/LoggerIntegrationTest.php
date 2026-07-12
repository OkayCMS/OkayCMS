<?php

declare(strict_types=1);

namespace Core {
    use Monolog\Handler\ChromePHPHandler;
    use Monolog\Handler\RotatingFileHandler;
    use Monolog\Logger;
    use Okay\Core\EntityFactory;
    use Okay\Core\OkayContainer\OkayContainer;
    use PHPUnit\Framework\TestCase;
    use Psr\Log\LoggerInterface;
    use ReflectionProperty;
    use RuntimeException;

    final class LoggerIntegrationTest extends TestCase
    {
        protected function tearDown(): void
        {
            $instance = new ReflectionProperty(OkayContainer::class, 'instance');
            $instance->setValue(null, null);

            parent::tearDown();
        }

        public function testCoreServiceMapProvidesMonologLoggerForPsrInterface(): void
        {
            $container = OkayContainer::getInstance($this->loggerServices(), $this->loggerParameters());

            $logger = $container->get(LoggerInterface::class);
            $chromeHandlers = array_filter(
                $logger->getHandlers(),
                static fn ($handler): bool => $handler instanceof ChromePHPHandler
            );
            $rotatingFileHandlers = array_filter(
                $logger->getHandlers(),
                static fn ($handler): bool => $handler instanceof RotatingFileHandler
            );

            self::assertInstanceOf(Logger::class, $logger);
            self::assertTrue($container->has(LoggerInterface::class));
            self::assertNotEmpty($chromeHandlers);
            self::assertContainsOnlyInstancesOf(ChromePHPHandler::class, $chromeHandlers);
            self::assertNotEmpty($rotatingFileHandlers);
            self::assertContainsOnlyInstancesOf(RotatingFileHandler::class, $rotatingFileHandlers);

            $logger->warning('PSR logger compatibility check', [
                'exception' => new RuntimeException('PSR context exception'),
            ]);
        }

        public function testLoggerConsumingCoreServiceReceivesConfiguredLogger(): void
        {
            $container = OkayContainer::getInstance($this->loggerServices(), $this->loggerParameters());

            $entityFactory = $container->get(EntityFactory::class);

            self::assertInstanceOf(EntityFactory::class, $entityFactory);
            self::assertSame(
                $container->get(LoggerInterface::class),
                $this->readLogger($entityFactory)
            );
        }

        /**
         * @return array<string, mixed>
         */
        private function loggerServices(): array
        {
            $services = (static function (): array {
                require dirname(__DIR__, 2) . '/Okay/Core/config/services.php';

                return $services;
            })();

            return array_intersect_key($services, array_flip([
                ChromePHPHandler::class,
                RotatingFileHandler::class,
                LoggerInterface::class,
                EntityFactory::class,
            ]));
        }

        /**
         * @return array<string, mixed>
         */
        private function loggerParameters(): array
        {
            return [
                'logger' => [
                    'file' => sys_get_temp_dir() . '/okay-psr-contracts-test.log',
                    'max_files_rotation' => 1,
                ],
            ];
        }

        private function readLogger(EntityFactory $entityFactory): LoggerInterface
        {
            $logger = new ReflectionProperty($entityFactory, 'logger');

            return $logger->getValue($entityFactory);
        }
    }
}
