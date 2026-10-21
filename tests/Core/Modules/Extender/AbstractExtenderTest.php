<?php


namespace Core\Modules\Extender;


use Okay\Core\Modules\Extender\AbstractExtender;
use Okay\Core\Modules\Extender\ExtensionInterface;
use PHPUnit\Framework\TestCase;

class AbstractExtenderTest extends TestCase
{
    /**
     * @param $path
     * @param $expectedResult
     * @dataProvider deprecatedMethodsDataProvider
     */
    public function testLoadDeprecatedMethods($config, $expectedResult)
    {
        /** @var AbstractExtender $abstractExtender */
        $abstractExtender = new class extends AbstractExtender {};

        $reflector = new \ReflectionClass($abstractExtender);
        $property = $reflector->getProperty('deprecatedMethods');
        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }

        $abstractExtender->setDeprecated($config);

        $this->assertEquals($property->getValue($abstractExtender), $expectedResult);
        $property->setValue($abstractExtender, []);
    }

    /**
     * @param $deprecated
     * @dataProvider newExtensionsDataProvider
     */
    public function testNewExtension($deprecated, $expectedResult)
    {
        $abstractExtenderBuilder = $this
            ->getMockBuilder(AbstractExtender::class)
            ->onlyMethods(['checkAndCorrectDeprecatedMethod', 'validateExtension']);

        $abstractExtender = $abstractExtenderBuilder->getMockForAbstractClass();
        if ($deprecated) {
            $abstractExtender
                ->expects($this->once())
                ->method('checkAndCorrectDeprecatedMethod')
                ->willReturn(['Okay\TestClass3', 'testMethod3']);
        } else {
            $abstractExtender
                ->expects($this->once())
                ->method('checkAndCorrectDeprecatedMethod')
                ->willReturn(false);
        }

        $abstractExtender
            ->expects($this->once())
            ->method('validateExtension');

        $reflector = new \ReflectionClass($abstractExtender);
        $property = $reflector->getProperty('triggers');
        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }

        $abstractExtender->newExtension(
            'Okay\ClassTest1',
            'testMethod1',
            'Okay\ClassTest2',
            'testMethod2');

        $this->assertEquals($property->getValue(), $expectedResult);
        $property->setValue(null, []);
    }

    public function testCompileTrigger()
    {
        /** @var AbstractExtender $abstractExtender */
        $abstractExtender = new class extends AbstractExtender {};

        $reflector = new \ReflectionClass($abstractExtender);
        $method = $reflector->getMethod('compileTrigger');
        if (PHP_VERSION_ID < 80100) {
            $method->setAccessible(true);
        }

        $actualResult = $method->invoke($abstractExtender, 'Okay\TestClass', 'testMethod');

        $this->assertEquals('Okay\TestClass::testMethod', $actualResult);
    }

    /**
     * @param $trigger
     * @param $expectedResult
     * @dataProvider correctDeprecatedMethodsDataProvider
     */
    public function testCheckAndCorrectDeprecatedMethod($trigger, $error, $expectedResult)
    {
        /** @var AbstractExtender $abstractExtender */
        $abstractExtender = new class extends AbstractExtender {};

        $reflector = new \ReflectionClass($abstractExtender);
        $property = $reflector->getProperty('deprecatedMethods');
        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }
        $property->setValue($abstractExtender, [
            'Okay\TestClass1::testMethod1' => [
                ['Okay\TestClass1', 'testMethod1'],
                ['Okay\TestClass2', 'testMethod2']
            ],
            'Okay\TestClass3::testMethod3' => [
                ['Okay\TestClass3', 'testMethod3'],
                false
            ]
        ]);

        $method = $reflector->getMethod('checkAndCorrectDeprecatedMethod');
        if (PHP_VERSION_ID < 80100) {
            $method->setAccessible(true);
        }

        $actualError = null;
        set_error_handler(function ($errno, $errstr) use (&$actualError) {
            $actualError = $errno;
            return true;
        });

        try {
            $actualResult = $method->invoke($abstractExtender, $trigger);
        } finally {
            restore_error_handler();
        }

        $this->assertSame($error === false ? null : $error, $actualError);
        $this->assertEquals($actualResult, $expectedResult);
    }

    /**
     * @param $classExpandable
     * @param $classExtender
     * @param $exception
     * @param $exceptionMessage
     * @dataProvider extensionsValidateDataProvider
     */
    public function testValidateExtension($classExpandable, $classExtender, $exceptionMessage)
    {
        /** @var AbstractExtender $abstractExtender */
        $abstractExtender = new class extends AbstractExtender {};

        $reflector = new \ReflectionClass($abstractExtender);
        $method = $reflector->getMethod('validateExtension');
        if (PHP_VERSION_ID < 80100) {
            $method->setAccessible(true);
        }

        $actualResult = null;
        try {
            $method->invoke($abstractExtender, $classExpandable, 'methodExpandable', $classExtender, 'methodExtender');
        } catch (\Exception $e) {
            $actualResult = $e->getMessage();
        };

        $this->assertEquals($actualResult, $exceptionMessage);
    }

    /**
     * @param $trigger
     * @dataProvider triggersDataProvider
     */
    public function testExtensionLog($trigger, $expectedResult)
    {
        $abstractExtender = $this
            ->getMockBuilder(AbstractExtender::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $reflector = new \ReflectionClass($abstractExtender);
        $property = $reflector->getProperty('triggers');
        if (PHP_VERSION_ID < 80100) {
            $property->setAccessible(true);
        }
        $property->setValue(null, [
            'Okay\TestClass::testMethod' => ['test']
        ]);

        $actualResult = $abstractExtender::extensionLog($trigger);

        $this->assertEquals($actualResult, $expectedResult);
    }

    public static function deprecatedMethodsDataProvider()
    {
        return [
            'Not empty config' => [
                [ // Конфиг
                    [
                        ['Okay\TestClass1', 'testMethod1'],
                        ['Okay\TestClass2', 'testMethod2']
                    ],
                    [
                        ['Okay\TestClass3', 'testMethod3'],
                        false
                    ]
                ],
                [ // Ожидаемый результат
                    'Okay\TestClass1::testMethod1' => [
                        ['Okay\TestClass1', 'testMethod1'],
                        ['Okay\TestClass2', 'testMethod2']
                    ],
                    'Okay\TestClass3::testMethod3' => [
                        ['Okay\TestClass3', 'testMethod3'],
                        false
                    ]
                ]
            ],
            'Empty config' => [
                [],
                []
            ]
        ];
    }

    public static function newExtensionsDataProvider()
    {
        return [
            'With deprecated method' => [
                true,
                [
                    'Okay\TestClass3::testMethod3' => [
                        (object) [
                            'class' => 'Okay\ClassTest2',
                            'method' => 'testMethod2'
                        ]
                    ]
                ]
            ],
            'Withou deprecated method' => [
                false,
                [
                    'Okay\ClassTest1::testMethod1' => [
                        (object) [
                            'class' => 'Okay\ClassTest2',
                            'method' => 'testMethod2'
                        ]
                    ]
                ]
            ]
        ];
    }

    public static function extensionsValidateDataProvider()
    {
        return [
            'Wrong expandable method' => [
                new class {
                    public function methodExpandableWrong() {}
                    public function __toString()
                    {
                        return 'Okay\ClassExpandable';
                    }
                },
                new class implements ExtensionInterface {
                    public function methodExtender() {}
                    public function __toString()
                    {
                        return 'Okay\ClassExtender';
                    }
                },
                'Expandable "Okay\ClassExpandable::methodExpandable()" is not a method', // Сообщение exception
            ],
            'Extender method has not callable structure' => [
                new class {
                    public function methodExpandable() {}
                    public function __toString()
                    {
                        return 'Okay\ClassExpandable';
                    }
                },
                null,
                "Method ::methodExtender is not callable",
            ],
            'ClassExtender without ExtensionInterface' => [
                new class {
                    public function methodExpandable() {}
                    public function __toString()
                    {
                        return 'Okay\ClassExpandable';
                    }
                },
                new class {
                    public function methodExtender() {}
                    public function __toString()
                    {
                        return 'Okay\ClassExtender';
                    }
                },
                "Class Okay\ClassExtender::class must implements " . ExtensionInterface::class . " interface",
            ],
            'Without errors' => [
                new class {
                    public function methodExpandable() {}
                    public function __toString()
                    {
                        return 'Okay\ClassExpandable';
                    }
                },
                new class implements ExtensionInterface {
                    public function methodExtender() {}
                    public function __toString()
                    {
                        return 'Okay\ClassExtender';
                    }
                },
                null,
            ]
        ];
    }

    public static function triggersDataProvider()
    {
        return [
            'Correct string trigger' => [
                [ // Триггер
                   'Okay\TestClass',
                    'testMethod'
                ],
                ['test'] // Ожидаемый результат
            ],
            'Correct array trigger' => [
                'Okay\TestClass::testMethod',
                ['test']
            ],
            'Wrong string trigger' => [
                'Okay\TestClass::testMethodWrong',
                []
            ],
        ];
    }

    public static function correctDeprecatedMethodsDataProvider()
    {
        return [
            'Deprecated method with replace' => [
                'Okay\TestClass1::testMethod1',
                E_USER_DEPRECATED,
                ['Okay\TestClass2', 'testMethod2']
            ],
            'Deprecated method without replace' => [
                'Okay\TestClass3::testMethod3',
                E_USER_WARNING,
                false
            ],
            'Not deprecated method' => [
                'Okay\TestClass4::testMethod4',
                false,
                false

            ]
        ];
    }
}