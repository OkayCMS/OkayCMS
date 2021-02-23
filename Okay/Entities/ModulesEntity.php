<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Module;
use Okay\Core\ServiceLocator;

class ModulesEntity extends Entity
{
    protected static $fields = [
        'id',
        'vendor',
        'module_name',
        'version',
        'type',
        'enabled',
        'position',
        'system',
        'backend_main_controller',
    ];

    protected static $langFields = [];

    protected static $searchFields = [
        'module_name',
    ];

    protected static $defaultOrderFields = [
        'position DESC',
    ];

    protected static $table = '__modules';
    protected static $tableAlias = 'm';

    public function getByVendorModuleName($vendor, $moduleName)
    {
        if (empty($vendor) || empty($moduleName)) {
            return null;
        }

        $this->setUp();

        $filter['vendor'] = $vendor;
        $filter['module_name'] = $moduleName;

        $this->buildFilter($filter);
        $this->select->cols($this->getAllFields());

        $this->db->query($this->select);
        return $this->getResult();
    }
    
    public function enable($ids)
    {
        return $this->update($ids, ['enabled'=>1]);
    }

    public function delete($ids)
    {
        // TODO тут должна быть возможность удаления файлов модуля и откат его миграций
        return parent::delete($ids);
    }

    public function disable($ids)
    {
        return $this->update($ids, ['enabled'=>0]);
    }

    // TODO подумать над тем, чтоб модули автоматически индексировались в базу при заходе в вдминку, тогда этот метод будет не нужен
    public function findNotInstalled($filterVendor = null, $filterModuleName = null)
    {
        
        $SL = ServiceLocator::getInstance();
        
        /** @var Module $moduleCore */
        $moduleCore = $SL->getService(Module::class);
        
        $modulesDir = __DIR__.'/../Modules/';
        $modulesDirContains = scandir($modulesDir);

        $notInstalledModules = [];
        $installedFullModuleNames = $this->installedFullModuleNames();
        foreach($modulesDirContains as $vendorName) {
            if ($this->isNotDir($modulesDir.$vendorName)) {
                continue;
            }

            $modulesByVendor = scandir($modulesDir.$vendorName);
            foreach($modulesByVendor as $moduleName) {
                
                if ($filterVendor !== null && $filterVendor != $vendorName) {
                    continue;
                }
                if ($filterModuleName !== null && $filterModuleName != $moduleName) {
                    continue;
                }
                
                if ($this->isNotDir($modulesDir.$vendorName.'/'.$moduleName)) {
                    continue;
                }

                $fullModuleName = $this->compileFullModuleName($vendorName, $moduleName);
                if (in_array($fullModuleName, $installedFullModuleNames)) {
                    continue;
                }

                $module = new \stdClass();
                $module->id       = null;
                $module->vendor   = $vendorName;
                $module->module_name = $moduleName;
                $module->position = null;
                $module->type     = null;
                $module->enabled  = 0;
                $module->status   = 'Not Installed';

                $module->params = $moduleCore->getModuleParams($vendorName, $moduleName);
                if (!empty($module->params->version)) {
                    $module->version = $module->params->version;
                }
                
                $notInstalledModules[] = $module;
            }
        }

        return $notInstalledModules;
    }

    public function compileFullModuleName($vendor, $moduleName)
    {
        if (empty($vendor) || empty($moduleName)) {
            throw new \Exception("Vendor And Name cannot be empty");
        }

        return $vendor.'/'.$moduleName;
    }

    protected function filter__without_system()
    {
        $this->select->where('system IS NULL OR system = 0');
    }

    private function installedFullModuleNames()
    {
        $installedModules = $this->cols(['vendor', 'module_name'])->find();

        $installedFullModuleNames = [];
        foreach($installedModules as $module) {
            $installedFullModuleNames[] = $this->compileFullModuleName($module->vendor, $module->module_name);
        }

        return $installedFullModuleNames;
    }

    private function isNotDir($dir)
    {
        $catalogNames = explode('/', $dir);
        if (!is_dir($dir) || end($catalogNames) === '.' || end($catalogNames) === '..') {
            return true;
        }

        return false;
    }
}