<?php


namespace Okay\Core\Modules;


use Okay\Core\EntityFactory;
use Okay\Entities\ModulesEntity;

class Installer
{
    /** @var ModulesEntity */
    private $modulesEntity;

    /** @var Module */
    private $module;
    
    public function __construct(EntityFactory $entityFactory, Module $module)
    {
        $this->modulesEntity = $entityFactory->get(ModulesEntity::class);
        $this->module = $module;
    }

    public function install($fullModuleName)
    {

        $moduleId = false;
        
        list($vendor, $moduleName) = explode('/', $fullModuleName);
        
        // Директорию получаем чтобы провалидировать, что такой модуль существует в ФС
        if ($this->module->getModuleDirectory($vendor, $moduleName)) {
            
            $findModules = $this->modulesEntity->cols(['id', 'type'])->find([
                'vendor' => $vendor,
                'module_name' => $moduleName,
            ]);

            if (count($findModules) > 0) {
                throw new \Exception('Module name "'.$vendor.'/'.$moduleName.'" is already exists');
            }

            $module = new \stdClass();
            $module->vendor  = $vendor;
            $module->module_name = $moduleName;
            $module->enabled = 1;

            $moduleParams = $this->module->getModuleParams($vendor, $moduleName);
            
            $module->version = $moduleParams->version;
            
            if (!$moduleId = $this->modulesEntity->add($module)) {
                // todo ошибка во время утановки
            }
            
            if ($initClassName = $this->module->getInitClassName($vendor, $moduleName)) {
                /** @var AbstractInit $initObject */
                $initObject = $this->getInitObject($initClassName, $moduleId, $vendor, $moduleName);
                $initObject->install();

                // Апдейты при установке вызываем всегда начиная с версии 1.0.0
                $updateMethods = $this->getUpdateMethods($initClassName, $moduleParams->math_version, $this->module->getMathVersion('1.0.0'));

                // Вызываем поочередно методы для обновления модуля
                if (!empty($updateMethods)) {
                    $initObject = $this->getInitObject($initClassName, $moduleId, $module->vendor, $module->module_name);
                    foreach ($updateMethods as $method) {
                        $initObject->$method();
                    }
                }
                
            }
        }
        
        return $moduleId;
    }
    
    public function update($moduleId)
    {
        if (!$module = $this->modulesEntity->findOne(['id' => $moduleId])) {
            return;
        }

        if (!$moduleMathVersion = $this->module->getMathVersion($module->version)) {
            return;
        }
        
        if (!($moduleParams = $this->module->getModuleParams($module->vendor, $module->module_name)) || empty($moduleParams->math_version)) {
            return;
        }

        if ($initClassName = $this->module->getInitClassName($module->vendor, $module->module_name)) {

            $updateMethods = $this->getUpdateMethods($initClassName, $moduleParams->math_version, $moduleMathVersion);
            
            // Вызываем поочередно методы для обновления модуля
            if (!empty($updateMethods)) {
                $initObject = $this->getInitObject($initClassName, $moduleId, $module->vendor, $module->module_name);
                foreach ($updateMethods as $method) {
                    $initObject->$method();
                }
            }

            // Обновляем версию модуля в системе
            $this->modulesEntity->update($moduleId, ['version' => $moduleParams->version]);
        }
    }
    
    protected function getInitObject($init, $moduleId, $vendorName, $moduleName)
    {
        return new $init($moduleId, $vendorName, $moduleName);
    }

    /**
     * @param $initClassName
     * @param int|string $moduleCurrentMathVersion текущая версия модуля в файле module.json, до которой нужно обновиться
     * @param int|string $moduleInstallMathVersion текущая установленная версия модуля с которой будем обновляться
     * @return array
     * @throws \ReflectionException
     */
    private function getUpdateMethods($initClassName, $moduleCurrentMathVersion, $moduleInstallMathVersion) : array
    {
        $reflection = new \ReflectionClass($initClassName);
        $updateMethods = [];

        //  Собираем список методов обновления, которые нужно вызвать
        foreach ($reflection->getMethods() as $method) {
            $matches = [];

            if (preg_match('~^update_([0-9]+_[0-9]+_[0-9]+)~', $method->name, $matches)) {
                $version = $this->module->getMathVersion(str_replace('_', '.', $matches[1]));
                if ($version <= $moduleCurrentMathVersion && $version > $moduleInstallMathVersion) {
                    $updateMethods[$version] = $method->name;
                }
            }
        }
        
        ksort($updateMethods, SORT_NATURAL);
        
        return $updateMethods;
    }
    
}