<?php


namespace Okay\Core\Modules;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\DTO\ModificationDTO;
use Okay\Core\Modules\DTO\ModuleParamsDTO;
use Okay\Core\Modules\DTO\TplChangeDTO;
use Okay\Core\ServiceLocator;
use Okay\Entities\ModulesEntity;
use Psr\Log\LoggerInterface;

/**
 * Class Module
 * @package Okay\Core\Modules
 * 
 * Класс предназначен для получения различной информации по модулю
 * 
 */

class Module
{
    const COMMON_MODULE_NAMESPACE = 'Okay\\Modules';
    const COMMON_MODULE_DIRECTORY = 'Okay/Modules/';

    protected LoggerInterface $logger;
    protected LicenseModulesTemplates $licenseModulesTemplates;
    protected array $modulesExpires;

    public function __construct(
        LoggerInterface $logger,
        LicenseModulesTemplates $licenseModulesTemplates
    ) {
        $this->logger = $logger;
        $this->licenseModulesTemplates = $licenseModulesTemplates;
    }

    private static array $modulesIds;

    /**
     * @param array $modulesExpires
     * @return void
     *
     * Метод встановлює інформацію по закінченню терміну доступу до оновлень модулів
     */
    public function setModulesExpires(array $modulesExpires)
    {
        $this->modulesExpires = $modulesExpires;
    }

    /**
     * Метод возвращает параметры модуля описанные в module.json
     * 
     * @param $vendor
     * @param $moduleName
     * @return ModuleParamsDTO
     * @throws \Exception
     */
    public function getModuleParams($vendor, $moduleName): ModuleParamsDTO
    {
        $moduleJsonFileFile = __DIR__ . '/../../Modules/' . $vendor . '/' . $moduleName . '/Init/module.json';

        $moduleParamsDTO = new ModuleParamsDTO();
        if (file_exists($moduleJsonFileFile)) {
            $moduleParams = json_decode(file_get_contents($moduleJsonFileFile), true);
            if (JSON_ERROR_NONE === $code = json_last_error()) {
                if (isset($moduleParams['modifications']['front'])) {
                    $this->initModifications(
                        $moduleParams['modifications']['front'],
                        $moduleParamsDTO,
                        $vendor,
                        $moduleName,
                        true
                    );
                }
                if (isset($moduleParams['modifications']['backend'])) {
                    $this->initModifications(
                        $moduleParams['modifications']['backend'],
                        $moduleParamsDTO,
                        $vendor,
                        $moduleName,
                        false
                    );
                }

                $moduleParamsDTO->fromArray($moduleParams);
            } else {
                $this->logger->error(sprintf(
                    "Error %d when decoding module.json of %s/%s: %s",
                    $code, $vendor, $moduleName, json_last_error_msg()
                ));
            }
        }

        if (isset($this->modulesExpires[$vendor . '/' . $moduleName])) {
            $moduleExpireInfo = $this->modulesExpires[$vendor . '/' . $moduleName];
            if ($moduleExpireInfo->daysToExpire >= 0) {
                $moduleParamsDTO->setDaysToExpire((int)$moduleExpireInfo->daysToExpire);
            } else {
                $moduleParamsDTO->setAccessExpired(true);
            }
            $moduleParamsDTO->setAddToCartUrl($moduleExpireInfo->addToCartUrl);
        }
        $moduleParamsDTO->setMathVersion($this->getMathVersion($moduleParamsDTO->getVersion()));

        $moduleParamsDTO->setIsOfficial(
            $this->licenseModulesTemplates->isOfficialModule($vendor, $moduleName)
        );

        $moduleParamsDTO->setIsLicensed(
            $this->licenseModulesTemplates->isLicensedModule($vendor, $moduleName)
        );

        return $moduleParamsDTO;
    }

    /**
     * @param array $modifications
     * @param ModuleParamsDTO $moduleParamsDTO
     * @param string $vendor
     * @param string $moduleName
     * @param bool $isFrontModification
     * @return void
     * @throws \Exception
     */
    private function initModifications(
        array $modifications,
        ModuleParamsDTO $moduleParamsDTO,
        string $vendor,
        string $moduleName,
        bool $isFrontModification
    ): void
    {
        foreach ($modifications as $modification) {
            $changes = [];
            foreach ($modification['changes'] as $change) {
                if (empty($change['find']) && empty($change['like'])) {
                    throw new \Exception(sprintf(
                        'Change must have "find" or "like" param in module "%s/%s"',
                        $vendor,
                        $moduleName
                    ));
                }
                $changeDTO = new TplChangeDTO(
                    (string)($change['find'] ?? ''),
                    (string)($change['like'] ?? '')
                );

                if (isset($change['parent'])) {
                    $changeDTO->setParent();
                }
                if (!empty($change['closestFind'])) {
                    $changeDTO->setClosestFind($change['closestFind']);
                }
                if (!empty($change['closestLike'])) {
                    $changeDTO->setClosestLike($change['closestLike']);
                }
                if (!empty($change['childrenFind'])) {
                    $changeDTO->setChildrenFind($change['childrenFind']);
                }
                if (!empty($change['childrenLike'])) {
                    $changeDTO->setChildrenLike($change['childrenLike']);
                }

                if (!empty($change['append'])) {
                    $changeDTO->setAppend($change['append']);
                }
                if (!empty($change['appendBefore'])) {
                    $changeDTO->setAppendBefore($change['appendBefore']);
                }
                if (!empty($change['prepend'])) {
                    $changeDTO->setPrepend($change['prepend']);
                }
                if (!empty($change['appendAfter'])) {
                    $changeDTO->setAppendAfter($change['appendAfter']);
                }
                if (!empty($change['html'])) {
                    $changeDTO->setHtml($change['html']);
                }
                if (!empty($change['text'])) {
                    $changeDTO->setText($change['text']);
                }
                if (!empty($change['replace'])) {
                    $changeDTO->setReplace($change['replace']);
                }
                if (!empty($change['comment'])) {
                    $changeDTO->setComment($change['comment']);
                }
                if (isset($change['remove'])) {
                    $changeDTO->setRemove();
                }

                $changes[] = $changeDTO;
            }
            $modificationDTO = new ModificationDTO($modification['file'], $changes);
            if ($isFrontModification) {
                $moduleParamsDTO->setFrontModification($modificationDTO);
            } else {
                $moduleParamsDTO->setBackendModification($modificationDTO);
            }

        }
    }
    
    /**
     * Получить базовую область видимости для указанного модуля
     * @param string $vendor
     * @param string $moduleName
     * @return string
     */
    public function getBaseNamespace(string $vendor, string $moduleName): string
    {
        return self::COMMON_MODULE_NAMESPACE . '\\' . $vendor . '\\' . $moduleName;
    }

    /**
     * Получить область видимости контроллеров админки для указанного модуля
     * @param string $vendor
     * @param string $moduleName
     * @return string
     */
    public function getBackendControllersNamespace(string $vendor, string $moduleName): string
    {
        return self::COMMON_MODULE_NAMESPACE . '\\' . $vendor . '\\' . $moduleName . '\\Backend\\Controllers';
    }

    /**
     * Получить область видимости контроллеров админки для указанного модуля
     * @param string $vendor
     * @param string $moduleName
     * @return string
     * @throws \Exception
     */
    public function getBackendControllersDirectory(string $vendor, string $moduleName): string
    {
        return $this->getModuleDirectory($vendor, $moduleName) . 'Backend/Controllers/';
    }

    /**
     * Получить экземпляр конфигурационного класса указанного модуля
     * @param string $vendor
     * @param string $moduleName
     * @return string
     */
    public function getInitClassName(string $vendor, string $moduleName): string
    {
        $initClassName = $this->getBaseNamespace($vendor, $moduleName) . '\\Init\\Init';
        if (class_exists($initClassName)) {
            return $initClassName;
        }

        return '';
    }

    /**
     * Получить базовую директорию для указанного модуля
     * @param string $vendor
     * @param string $moduleName
     * @throws \Exception
     * @return string
     */
    public function getModuleDirectory(string $vendor, string $moduleName): string
    {
        if (!preg_match('~^[\w]+$~', $vendor)) {
            throw new \Exception('"' . $vendor . '" is wrong name of vendor');
        }

        if (!preg_match('~^[\w]+$~', $moduleName)) {
            throw new \Exception('"' . $moduleName . '" is wrong name of module');
        }

        $dir = self::COMMON_MODULE_DIRECTORY . $vendor . '/' . $moduleName;
        return rtrim($dir, '/') . '/';
    }

    public function moduleDirectoryNotExists(string $vendor, string $moduleName): bool
    {
        $moduleDir = $this->getModuleDirectory($vendor, $moduleName);

        if (is_dir($moduleDir)) {
            return false;
        }

        $moduleNotExistsMsg = 'Module "' . $vendor . '/' . $moduleName . '" installed but not exists';
        trigger_error($moduleNotExistsMsg, E_USER_WARNING);
        $this->logger->addWarning($moduleNotExistsMsg);
        return true;
    }

    /**
     * Получить список роутов модуля
     * @param string $vendor
     * @param string $moduleName
     * @throws \Exception
     * @return array
     */
    public function getRoutes(string $vendor, string $moduleName): array
    {
        $file = $this->getModuleDirectory($vendor, $moduleName) . '/Init/routes.php';

        if (!file_exists($file)) {
            return [];
        }

        if (!($routes = include($file)) || !is_array($routes)) {
            return [];
        }
        return $routes;
    }

    /**
     * Получить список сервисов модуля
     * @param string $vendor
     * @param string $moduleName
     * @throws \Exception
     * @return array
     */
    public function getServices(string $vendor, string $moduleName): array
    {
        $file = $this->getModuleDirectory($vendor, $moduleName) . '/Init/services.php';

        if (!file_exists($file)) {
            return [];
        }
        if (!($services = include($file)) || !is_array($services)) {
            return [];
        }
        return $services;
    }

    /**
     * Получить список параметров модуля
     * @param string $vendor
     * @param string $moduleName
     * @throws \Exception
     * @return array
     */
    public function getParameters(string $vendor, string $moduleName): array
    {
        $file = $this->getModuleDirectory($vendor, $moduleName) . '/Init/parameters.php';

        if (!file_exists($file)) {
            return [];
        }

        if (!($parameters = include($file)) || !is_array($parameters)) {
            return [];
        }
        return $parameters;
    }

    /**
     * Получить список сервисов модуля
     * @param string $vendor
     * @param string $moduleName
     * @throws \Exception
     * @return array
     */
    public function getSmartyPlugins(string $vendor, string $moduleName): array
    {
        $file = $this->getModuleDirectory($vendor, $moduleName) . '/Init/SmartyPlugins.php';

        if (!file_exists($file)) {
            return [];
        }

        if (!($smartyPlugins = include($file)) || !is_array($smartyPlugins)) {
            return [];
        }
        return $smartyPlugins;
    }

    public function isModuleClass($className)
    {
        return preg_match('~Okay\\\\Modules\\\\([a-zA-Z0-9]+)\\\\([a-zA-Z0-9]+)\\\\?.*~', $className);
    }

    public function getVendorName($className)
    {
        if (!$this->isModuleClass($className)) {
            throw new \Exception('Wrong module name');
        }
        return preg_replace('~Okay\\\\Modules\\\\([a-zA-Z0-9]+)\\\\([a-zA-Z0-9]+)\\\\?.*~', '$1', $className);
    }

    public function getModuleName($className)
    {
        if (!$this->isModuleClass($className)) {
            throw new \Exception('Wrong module name');
        }

        return preg_replace('~Okay\\\\Modules\\\\([a-zA-Z0-9]+)\\\\([a-zA-Z0-9]+)\\\\?.*~', '$2', $className);
    }

    public function isModuleController($controllerName)
    {
        return preg_match('~Okay\\\\Modules\\\\([a-zA-Z0-9]+)\\\\([a-zA-Z0-9]+)\\\\Controllers\\\\?.*~', $controllerName);
    }

    public function isBackendControllerName($backendController)
    {
        return preg_match('/[a-zA-Z]+\.[a-zA-Z]+\.[a-zA-Z]+/', $backendController);
    }

    public function getVendorNameByBackendControllerName($backendController)
    {
        if (!$this->isBackendControllerName($backendController)) {
            throw new \Exception('Incorrect module backend controller name');
        }

        $nameParts = explode('.', $backendController);
        return $nameParts[0];
    }

    public function getModuleNameByBackendControllerName($backendController)
    {
        if (!$this->isBackendControllerName($backendController)) {
            throw new \Exception('Incorrect module backend controller name');
        }

        $nameParts = explode('.', $backendController);
        return $nameParts[1];
    }

    /**
     * Получить параметры контроллера админки. Имя контроллера имеет структуру Vendor.Module.Controller
     * В случае если имя контроллера соответствует контрорллеру админки,
     * в ответ получим массив
     * [
     *      'vendor' => 'Vendor',
     *      'module' => 'Module',
     *      'controller' => 'Controller',
     * ]
     * @param $vendorModuleController
     * @return bool|array
     * @throws \Exception
     */
    public function getBackendControllerParams($vendorModuleController)
    {
        if (preg_match('~([a-zA-Z0-9]+)\.([a-zA-Z0-9]+)\.([a-zA-Z0-9]+)+~', $vendorModuleController, $matches)) {
            $vendor = $matches[1];
            $moduleName = $matches[2];
            $controllerName = $matches[3];

            if (is_file($this->getBackendControllersDirectory($vendor, $moduleName) . $controllerName . '.php')) {
                return [
                    'vendor' => $vendor,
                    'module' => $moduleName,
                    'controller' => $controllerName,
                ];
            }
        }

        return false;
    }

    public function getBackendControllerName($vendor, $module, $controllerClass)
    {
        return $vendor . '.' . $module . '.' . $controllerClass;
    }

    public function generateModuleTemplateDir($vendor, $moduleName)
    {
        return realpath(__DIR__ . '/../../Modules/' . $vendor . '/' . $moduleName . '/design/html/');
    }

    /**
     * Метод принимает по сути имя любого класса модуля, и возвращает id этого модуля в БД
     *
     * @param $namespace
     * @return int|bool id модуля в системе, или false в случае ошибки
     * @throws \Exception
     */
    public function getModuleIdByNamespace($namespace)
    {
        $vendor = $this->getVendorName($namespace);
        $moduleName = $this->getModuleName($namespace);

        if (!empty(self::$modulesIds[$vendor][$moduleName])) {
            return self::$modulesIds[$vendor][$moduleName];
        }

        $SL = ServiceLocator::getInstance();

        /** @var EntityFactory $entityFactory */
        $entityFactory = $SL->getService(EntityFactory::class);

        /** @var ModulesEntity $modulesEntity */
        $modulesEntity = $entityFactory->get(ModulesEntity::class);
        if ($module = $modulesEntity->getByVendorModuleName($vendor, $moduleName)) {
            return self::$modulesIds[$vendor][$moduleName] = $module->id;
        }
        return false;
    }

    /**
     * Метод возвращает изображение модуля, которое соответствует файлу с названием preview.* в корне модуля
     *
     * @throws \Exception
     * @param $vendor
     * @param $moduleName
     * @return mixed
     */
    public function findModulePreview($vendor, $moduleName)
    {
        $moduleDir = $this->getModuleDirectory($vendor, $moduleName);
        $matchedFiles = glob($moduleDir . "preview.*");

        if (empty($matchedFiles)) {
            return false;
        }

        foreach ($matchedFiles as $file) {
            if ($this->fileHasAllowImageExtension($file)) {
                return $file;
            }
        }

        return false;
    }

    private function fileHasAllowImageExtension($file)
    {
        return preg_match('/\.(jpeg|jpg|png|gif|svg)$/ui', $file);
    }

    /**
     * Метод возвращает математическое представление версии, которое можно передавать операторам сравнения
     * 
     * @param $version
     * @return int
     */
    public function getMathVersion($version) : int
    {
        $parts = explode('.', $version);
        
        if (count($parts) != 3) {
            return 0;
        }
        
        foreach ($parts as &$part) {
            $part += 100;
        }
        unset($part);
        return (int)implode('' , $parts);
    }

    public function getVersionControl(): VersionControl
    {
        return new VersionControl();
    }
}