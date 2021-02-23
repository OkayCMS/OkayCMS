<?php


namespace Okay\Admin\Controllers;


use Okay\Core\Modules\Module;
use Okay\Core\Modules\ModuleDesign;
use Okay\Entities\ModulesEntity;

class ModuleDesignAdmin extends IndexAdmin
{
    public function fetch(ModuleDesign $moduleDesign, ModulesEntity $modulesEntity)
    {
        $vendorName = $this->request->get('vendor');
        $moduleName = $this->request->get('module_name');

        $module = $modulesEntity->findOne(['vendor' => $vendorName, 'module_name' => $moduleName]);
        if (empty($module)) {
            return;
        }

        if ($this->request->method('post')) {
            $action = $this->request->post('action');
            $files  = (array) $this->request->post('check');

            if (!empty($action) && !empty($files)) {
                switch($action) {
                    case 'clone_to_theme': {
                        $moduleDesign->cloneFileSetToTheme($files, $module->vendor, $module->module_name);
                        break;
                    }
                }
            }

            $fileName = $this->request->post('clone_single_file');
            if (!empty($fileName)) {
                $moduleDesign->cloneFileToTheme($fileName, $module->vendor, $module->module_name);
            }
        }

        $files = $moduleDesign->getAllFiles($module->vendor, $module->module_name);

        $this->design->assign('files',  $files);
        $this->design->assign('module', $module);
        $this->response->setContent($this->design->fetch('module_design.tpl'));
    }
}