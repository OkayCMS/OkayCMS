<?php

declare(strict_types=1);

namespace Core\OkayContainer {
    use Okay\Core\OkayContainer\Exception\ContainerException;
    use Okay\Core\OkayContainer\Exception\ServiceNotFoundException;
    use Okay\Core\OkayContainer\OkayContainer;
    use PHPUnit\Framework\TestCase;
    use Psr\Container\ContainerExceptionInterface;
    use Psr\Container\NotFoundExceptionInterface;
    use ReflectionProperty;
    use stdClass;

    final class OkayContainerTest extends TestCase
    {
        protected function tearDown(): void
        {
            $instance = new ReflectionProperty(OkayContainer::class, 'instance');
            $instance->setValue(null, null);

            parent::tearDown();
        }

        public function testGetReturnsStoredServiceInstance(): void
        {
            $container = OkayContainer::getInstance([
                stdClass::class => [
                    'class' => stdClass::class,
                ],
            ]);

            $service = $container->get(stdClass::class);

            self::assertInstanceOf(stdClass::class, $service);
            self::assertSame($service, $container->get(stdClass::class));
        }

        public function testHasReportsKnownAndUnknownServices(): void
        {
            $container = OkayContainer::getInstance([
                stdClass::class => [
                    'class' => stdClass::class,
                ],
            ]);

            self::assertTrue($container->has(stdClass::class));
            self::assertFalse($container->has('unknown.service'));
        }

        public function testMissingServiceThrowsPsrNotFoundException(): void
        {
            $container = OkayContainer::getInstance();

            try {
                $container->get('unknown.service');
                self::fail('Expected a service-not-found exception.');
            } catch (ServiceNotFoundException $exception) {
                self::assertInstanceOf(NotFoundExceptionInterface::class, $exception);
                self::assertSame('Service not found: unknown.service', $exception->getMessage());
            }
        }

        public function testMalformedServiceDefinitionThrowsPsrContainerException(): void
        {
            $container = OkayContainer::getInstance([
                'broken.service' => [
                    'arguments' => [],
                ],
            ]);

            try {
                $container->get('broken.service');
                self::fail('Expected a malformed service definition exception.');
            } catch (ContainerException $exception) {
                self::assertInstanceOf(ContainerExceptionInterface::class, $exception);
                self::assertSame(
                    'broken.service service entry must be an array containing a \'class\' key',
                    $exception->getMessage()
                );
            }
        }
    }

}
