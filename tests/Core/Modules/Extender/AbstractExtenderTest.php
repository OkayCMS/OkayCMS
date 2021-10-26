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
        $property->setAccessible(true);

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
        $property->setAccessible(true);

        $abstractExtender->newExtension(
            'Okay\ClassTest1',
            'testMethod1',
            'Okay\ClassTest2',
            'testMethod2');

        $this->assertEquals($property->getValue(), $expectedResult);
        $property->setValue([]);
    }

    public function testCompileTrigger()
    {
        /** @var AbstractExtender $abstractExtender */
        $abstractExtender = new class extends AbstractExtender {};

        $reflector = new \ReflectionClass($abstractExtender);
        $method = $reflector->getMethod('compileTrigger');
        $method->setAccessible(true);

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
        $property->setAccessible(true);
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
        $method->setAccessible(true);

        switch ($error) {
            case E_USER_WARNING:
                $this->expectWarning();
                break;

            case E_USER_DEPRECATED:
                $this->expectDeprecation();
                break;
        }

        $actualResult = $method->invoke($abstractExtender, $trigger);

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
        $method->setAccessible(true);

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
        $property->setAccessible(true);
        $property->setValue([
            'Okay\TestClass::testMethod' => ['test']
        ]);

        $actualResult = $abstractExtender::extensionLog($trigger);

        $this->assertEquals($actualResult, $expectedResult);
    }

    public function deprecatedMethodsDataProvider()
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

    public function newExtensionsDataProvider()
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

    public function extensionsValidateDataProvider()
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

    public function triggersDataProvider()
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

    public function correctDeprecatedMethodsDataProvider()
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