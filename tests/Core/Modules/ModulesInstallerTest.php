<?php

namespace Core\Modules;

use \Exception;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\DTO\ModuleParamsDTO;
use Okay\Core\Modules\Installer;
use Okay\Core\Modules\Module;
use Okay\Entities\ModulesEntity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ModulesInstallerTest extends TestCase
{

    // Связки текстовых версий и их математического представления
    private $versionReturnMap = [
        [
            '1.0.0',
            101100100,
        ],
        [
            '1.1.0',
            101101100,
        ],
        [
            '1.0.1',
            101100101,
        ],
        [
            '1.0.11',
            101100111,
        ],
        [
            '1.5.11',
            101105111,
        ],
        [
            '1.1.1',
            101101101,
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        require_once 'StubModuleInitClass.php';
    }

    #[DataProvider('getUpdateMethodsDataProvider')]
    public function testGetUpdateMethods(int $moduleCurrentMathVersion, int $moduleInstallMathVersion, $expectedResult)
    {


        $entityFactoryStub = $this->createStub(EntityFactory::class);
        $moduleStub = $this->createStub(Module::class);

        // настраиваем ModuleStub
        $moduleStub->method('getMathVersion')->willReturnMap($this->versionReturnMap);

        // Т.к. метод приватный, доступ к нему получаем через рефлексию
        $reflector = new \ReflectionClass(Installer::class);
        $method = $reflector->getMethod('getUpdateMethods');

        $installer = new Installer($entityFactoryStub, $moduleStub);

        $actualResult = $method->invokeArgs($installer, [StubModuleInitClass::class, $moduleCurrentMathVersion, $moduleInstallMathVersion]);

        $this->assertTrue($this->arraysAreSimilar($expectedResult, $actualResult));
    }

    private function arraysAreSimilar($a, $b) : bool
    {
        $a = array_values($a);
        $b = array_values($b);
        if (count(array_diff_assoc($a, $b))) {
            return false;
        }
        foreach($a as $k => $v) {
            if ($v !== $b[$k]) {
                return false;
            }
        }
        return true;
    }

    #[DataProvider('updateDataProvider')]
    public function testUpdate(int $moduleId, string $installedVersion, array $callUpdateMethods, string $newVersion)
    {
        $entityFactoryStub = $this->createStub(EntityFactory::class);
        $moduleStub = $this->createStub(Module::class);

        // настраиваем ModuleStub
        $moduleStub->method('getMathVersion')->willReturnMap($this->versionReturnMap);

        $newMathVersion = $moduleStub->getMathVersion($newVersion);

        $modulesParamsDTO = new ModuleParamsDTO();
        $modulesParamsDTO->fromArray([
            'version' => $newVersion,
            'math_version' => $newMathVersion,
        ]);
        $moduleStub->method('getModuleParams')
            ->willReturn($modulesParamsDTO);

        $moduleStub->method('getInitClassName')->willReturn(StubModuleInitClass::class);

        // настраиваем ModulesEntityStub
        $modulesEntityStub = $this->createStub(ModulesEntity::class);

        $modulesEntityStub->method('findOne')
            ->willReturnMap([
                [
                    ['id' => 1],
                    (object)[
                        'id' => '1',
                        'vendor' => 'OkayCMS',
                        'module_name' => 'Banners',
                        'version' => $installedVersion,
                    ],
                ],
                [
                    ['id' => 2],
                    false,
                ],
            ]);

        $modulesEntityStub->method('update')->willReturn(true);

        $entityFactoryMap = [
            [ModulesEntity::class, $modulesEntityStub],
        ];

        $entityFactoryStub->method('get')
            ->willReturnMap($entityFactoryMap);

        // Экстендим через анонимный класс наш установщик, чтобы передать ему мок класса Init
        // он там нужен для контроля вызова методов апдейтов
        $installer = new class($entityFactoryStub, $moduleStub) extends Installer {
            private $initMock;

            public function setInitMock($initMock)
            {
                $this->initMock = $initMock;
            }

            protected function getInitObject($init, $moduleId, $vendorName, $moduleName)
            {
                return $this->initMock;
            }
        };

        $stubInitClassMock = $this->getMockBuilder(StubModuleInitClass::class)->getMock();

        // Проходимся по методам класса Init и указываем что все они не должны быть вызваны
        // за исключением переданных к вызову методов
        $reflection = new \ReflectionClass(StubModuleInitClass::class);
        foreach ($reflection->getMethods() as $method) {
            if (!in_array($method->name, $callUpdateMethods)) {
                $stubInitClassMock->expects($this->never())->method($method->name);
            }
        }

        // Указываем какие методы класса Init должны быть вызваны. Каждый по одному разу
        foreach ($callUpdateMethods as $callUpdateMethod) {
            $stubInitClassMock->expects($this->once())
                ->method($callUpdateMethod);
        }

        $installer->setInitMock($stubInitClassMock);
        $installer->update($moduleId);
    }

    #[DataProvider('installDataProvider')]
    public function testInstall(string $fullModuleName, array $callUpdateMethods, string $newInstalledVersion)
    {
        $entityFactoryStub = $this->createStub(EntityFactory::class);
        $moduleStub = $this->createStub(Module::class);

        // настраиваем ModuleStub
        $moduleStub->method('getModuleDirectory')->willReturn($fullModuleName);

        $moduleStub->method('getInitClassName')->willReturn(StubModuleInitClass::class);

        $moduleStub->method('getMathVersion')->willReturnMap($this->versionReturnMap);

        $newMathVersion = $moduleStub->getMathVersion($newInstalledVersion);

        $modulesParamsDTO = new ModuleParamsDTO();
        $modulesParamsDTO->fromArray([
            'version' => $newInstalledVersion,
            'math_version' => $newMathVersion,
        ]);
        $moduleStub->method('getModuleParams')
            ->willReturn($modulesParamsDTO);

        // настраиваем ModulesEntityStub
        $modulesEntityStub = $this->createStub(ModulesEntity::class);

        $modulesEntityStub->method('cols')->willReturnSelf();

        $modulesEntityStub->method('add')->willReturn(3);

        $modulesEntityStub->method('find')
            ->willReturnMap([
                [
                    [
                        'vendor' => 'OkayCMS',
                        'module_name' => 'Banners',
                    ],
                    [
                        (object)[
                            'id' => '1',
                            'vendor' => 'OkayCMS',
                            'module_name' => 'Banners',
                        ],
                    ]
                ],
                [
                    [
                        'vendor' => 'OkayCMS',
                        'module_name' => 'NP',
                    ],
                    [],
                ],
            ]);

        if ($fullModuleName == 'OkayCMS/Banners') {
            $this->expectException(Exception::class);
        }

        $entityFactoryMap = [
            [ModulesEntity::class, $modulesEntityStub],
        ];

        $entityFactoryStub->method('get')
            ->willReturnMap($entityFactoryMap);

        // Экстендим через анонимный класс наш установщик, чтобы передать ему мок класса Init
        // он там нужен для контроля вызова методов апдейтов
        $installer = new class($entityFactoryStub, $moduleStub) extends Installer {
            private $initMock;

            public function setInitMock($initMock)
            {
                $this->initMock = $initMock;
            }

            protected function getInitObject($init, $moduleId, $vendorName, $moduleName)
            {
                return $this->initMock;
            }
        };

        $stubInitClassMock = $this->getMockBuilder(StubModuleInitClass::class)->getMock();

        // Проходимся по методам класса Init и указываем что все они не должны быть вызваны
        // за исключением переданных к вызову методов
        $reflection = new \ReflectionClass(StubModuleInitClass::class);
        foreach ($reflection->getMethods() as $method) {
            if (!in_array($method->name, $callUpdateMethods)) {
                $stubInitClassMock->expects($this->never())->method($method->name);
            }
        }

        // Указываем какие методы класса Init должны быть вызваны. Каждый по одному разу
        foreach ($callUpdateMethods as $callUpdateMethod) {
            $stubInitClassMock->expects($this->once())
                ->method($callUpdateMethod);
        }

        $installer->setInitMock($stubInitClassMock);
        $installer->install($fullModuleName);

    }

    public static function updateDataProvider() : array
    {
        return [
            [
                1,
                '1.0.0',
                [
                    'update_1_0_11',
                    'update_1_0_1',
                    'update_1_1_0',
                ],
                '1.1.0',
            ],
            [
                1,
                '1.0.0',
                [
                    'update_1_0_1',
                ],
                '1.0.1',
            ],
            [
                1,
                '1.0.0',
                [
                    'update_1_0_1',
                    'update_1_0_11',
                    'update_1_1_0',
                    'update_1_1_1',
                ],
                '1.1.1',
            ],
            [
                2, // модуль не найден, методы не должны вызываться
                '1.0.0',
                [],
                '1.0.1',
            ],
        ];
    }

    public static function installDataProvider() : array
    {
        return [
            [
                'OkayCMS/NP',
                [
                    'install',
                    'update_1_0_11',
                    'update_1_0_1',
                    'update_1_1_0',
                ],
                '1.1.0', // Версия которую устанавливаем (в файле module.json)
            ],
            [ // Такое модуль существует, ожидаем исключение
                'OkayCMS/Banners',
                [],
                '1.0.0', // Версия которую устанавливаем (в файле module.json)
            ],
        ];
    }

    public static function getUpdateMethodsDataProvider() : array
    {
        return [
            [
                101100111, // Current module math version
                101100100, // Installed module math version
                [
                    '101100101' => 'update_1_0_1',
                    '101100111' => 'update_1_0_11',
                ],
            ],
            [
                101101101, // Current module math version
                101100100, // Installed module math version
                [
                    '101100101' => 'update_1_0_1',
                    '101100111' => 'update_1_0_11',
                    '101101100' => 'update_1_1_0',
                    '101101101' => 'update_1_1_1',
                ],
            ],
        ];
    }
}
