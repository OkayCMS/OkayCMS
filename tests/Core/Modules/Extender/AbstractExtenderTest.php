<?php

namespace Core\Modules\Extender;

use Okay\Core\Modules\Extender\AbstractExtender;
use Okay\Core\Modules\Extender\ExtensionInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AbstractExtenderTest extends TestCase
{
    #[DataProvider('deprecatedMethodsDataProvider')]
    public function testLoadDeprecatedMethods($config, $expectedResult)
    {
        /** @var AbstractExtender $abstractExtender */
        $abstractExtender = new class extends AbstractExtender {};

        $reflector = new \ReflectionClass($abstractExtender);
        $property = $reflector->getProperty('deprecatedMethods');

        $abstractExtender->setDeprecated($config);

        $this->assertEquals($property->getValue($abstractExtender), $expectedResult);
        $property->setValue($abstractExtender, []);
    }

    #[DataProvider('newExtensionsDataProvider')]
    public function testNewExtension($deprecated, $expectedResult)
    {
        $abstractExtenderBuilder = $this
            ->getMockBuilder(AbstractExtender::class)
            ->onlyMethods(['checkAndCorrectDeprecatedMethod', 'validateExtension']);

        $abstractExtender = $abstractExtenderBuilder->getMock();
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

        $abstractExtender->newExtension(
            'Okay\ClassTest1',
            'testMethod1',
            'Okay\ClassTest2',
            'testMethod2');

        // For static properties, pass null as first argument in PHP 8.3+
        $this->assertEquals($property->getValue(null), $expectedResult);
        $property->setValue(null, []);
    }

    public function testCompileTrigger()
    {
        /** @var AbstractExtender $abstractExtender */
        $abstractExtender = new class extends AbstractExtender {};

        $reflector = new \ReflectionClass($abstractExtender);
        $method = $reflector->getMethod('compileTrigger');

        $actualResult = $method->invoke($abstractExtender, 'Okay\TestClass', 'testMethod');

        $this->assertEquals('Okay\TestClass::testMethod', $actualResult);
    }

    #[DataProvider('correctDeprecatedMethodsDataProvider')]
    public function testCheckAndCorrectDeprecatedMethod($trigger, $error, $expectedResult)
    {
        /** @var AbstractExtender $abstractExtender */
        $abstractExtender = new class extends AbstractExtender {};

        $reflector = new \ReflectionClass($abstractExtender);
        $property = $reflector->getProperty('deprecatedMethods');
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

        $restoreHandler = false;

        if ($error === E_USER_WARNING || $error === E_USER_DEPRECATED) {
            set_error_handler(
                function (int $errno, string $errstr) use ($error): bool {
                    if ($errno === $error) {
                        return true;
                    }

                    return false;
                }
            );
            $restoreHandler = true;
        }

        try {
            $actualResult = $method->invoke($abstractExtender, $trigger);
        } finally {
            if ($restoreHandler) {
                restore_error_handler();
            }
        }

        $this->assertEquals($actualResult, $expectedResult);
    }

    #[DataProvider('extensionsValidateDataProvider')]
    public function testValidateExtension($classExpandable, $classExtender, $exceptionMessage)
    {
        /** @var AbstractExtender $abstractExtender */
        $abstractExtender = new class extends AbstractExtender {};

        $reflector = new \ReflectionClass($abstractExtender);
        $method = $reflector->getMethod('validateExtension');

        $actualResult = null;
        try {
            $method->invoke($abstractExtender, $classExpandable, 'methodExpandable', $classExtender, 'methodExtender');
        } catch (\Exception $e) {
            $actualResult = $e->getMessage();
        };

        $this->assertEquals($actualResult, $exceptionMessage);
    }

    #[DataProvider('triggersDataProvider')]
    public function testExtensionLog($trigger, $expectedResult)
    {
        $abstractExtender = new class extends AbstractExtender {};

        $reflector = new \ReflectionClass($abstractExtender);
        $property = $reflector->getProperty('triggers');
        $property->setValue(null, [
            'Okay\TestClass::testMethod' => ['test'],
        ]);

        $actualResult = $abstractExtender::extensionLog($trigger);

        $this->assertEquals($actualResult, $expectedResult);
        $property->setValue(null, []);
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
