<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendModulesHelper;
use Okay\Core\BackendTranslations;
use Okay\Core\Managers;
use Okay\Core\Modules\Installer;
use Okay\Core\Modules\Modules;
use Okay\Core\Validator;
use Okay\Entities\ManagersEntity;
use Okay\Entities\ModulesEntity;
use Okay\Core\Modules\Module;

class ModulesAdmin extends IndexAdmin
{
    public function fetch(
        ModulesEntity  $modulesEntity,
        Installer      $modulesInstaller,
        Module         $moduleCore,
        ManagersEntity $managersEntity,
        Managers       $managersCore
    ) {
        // Обработка действий
        if ($this->request->method('post')) {
            if (!empty($this->request->post('install_module'))) {
                if ($modulesInstaller->install($this->request->post('install_module'))) {
                    $this->design->clearCompiled();
                    $this->response->redirectTo($this->request->getCurrentUrl());
                }
            }

            // Действия с выбранными
            $ids = $this->request->post('check');
            if (is_array($ids)) {
                switch ($this->request->post('action')) {
                    case 'disable': {
                        $modulesEntity->disable($ids);
                        $this->design->clearCompiled();
                        break;
                    }
                    case 'enable': {
                        $modulesEntity->enable($ids);
                        $this->design->clearCompiled();
                        break;
                    }
                    case 'delete': {
                        $modulesEntity->delete($ids);
                        $this->design->clearCompiled();
                        $this->response->redirectTo($this->request->getCurrentUrl());
                        break;
                    }
                    case 'update': {
                        foreach ($ids as $id) {
                            $modulesInstaller->update((int)$id);
                        }
                        $this->design->clearCompiled();
                        break;
                    }
                }
            }

            // Сортировка
            $positions = $this->request->post('positions');
            $ids = array_keys($positions);
            rsort($positions);
            foreach ($positions as $i=>$position) {
                $modulesEntity->update($ids[$i], ['position'=>$position]);
            }

            $this->response->redirectTo($this->request->getCurrentUrl());
        }

        $filter = [];
        $manager = $managersEntity->findOne(['login' => $_SESSION['admin']]);
        if ($managersCore->cannotVisibleSystemModules($manager)) {
            $filter['without_system'] = 1;
        }

        $modulesList = array_merge($modulesEntity->findNotInstalled(), $modulesEntity->find($filter));

        foreach ($modulesList as $module) {
            $preview = $moduleCore->findModulePreview($module->vendor, $module->module_name);
            if (!empty($preview)) {
                $module->preview = $preview;
            }
            $module->params = $moduleCore->getModuleParams($module->vendor, $module->module_name);
        }

        $this->design->assign('modules', $modulesList);
        
        $this->response->setContent($this->design->fetch('modules.tpl'));
    }
    
    public function downloadModule(
        BackendModulesHelper $backendModulesHelper,
        ModulesEntity $modulesEntity,
        Module $moduleCore,
        BackendTranslations $backendTranslations
    ) {
        $response = [];
        if ($accessUrl = $this->request->post('access_url')) {
            if (!($accessDomain = parse_url($accessUrl, PHP_URL_HOST)) || $accessDomain != parse_url($this->config->get('marketplace_url'), PHP_URL_HOST)) {
                $response = [
                    'error' => $backendTranslations->getTranslation('m_modules_wrong_marketplace_domain') . ' (' . $this->config->get('marketplace_url') . ')',
                ];
            } else {
                // С api версии приходят в порядке возрастания версии модуля
                $downloadVersionsData = $backendModulesHelper->checkDownloadVersions($accessUrl);

                if (!empty($downloadVersionsData->error)) {
                    
                    switch ($downloadVersionsData->error) {
                        case 'Resource not found':
                            $response['error'] = $backendTranslations->getTranslation('m_modules_resource_not_found');
                            break;
                    }
                    
                
                // Проверяем может у нас установлен этот модуль
                } elseif (!empty($downloadVersionsData->meta->vendor_name) 
                    && !empty($downloadVersionsData->meta->module_name)
                    && ($installedModule = $modulesEntity->findOne(['vendor' => $downloadVersionsData->meta->vendor_name, 'module_name' => $downloadVersionsData->meta->module_name]))
                ) {
                    $response['installed_version'] = $installedModule->version;
                } else {
                    $downloadUrl = null;
                    foreach ($downloadVersionsData->versions as $downloadVersion) {
                        
                        preg_match('~^(?:\w+_)?(\d+?\.\d+?)\.\d+?(?:\.\d+?)?$~', $this->config->version, $okayVersionMatches);
                        preg_match('~^(?:\w+_)?(\d+?\.\d+?)\.\d+?(?:\.\d+?)?$~', $downloadVersion->okay_version, $moduleVersionMatches);

                        $okayMinorVersion = $okayVersionMatches[1];
                        $moduleOkayMinorVersion = $moduleVersionMatches[1];

                        if ($downloadVersion->okay_version == $this->config->version) {
                            $downloadUrl = $downloadVersion->zip;
                            break;
                        } elseif ($okayMinorVersion == $moduleOkayMinorVersion) {
                            $downloadUrl = $downloadVersion->zip;
                            break;
                        }
                    }
                    if (!empty($downloadUrl) && ($moduleTmpDir = $backendModulesHelper->downloadModule($downloadUrl))) {
                        if ($backendModulesHelper->moveModule($moduleTmpDir, $downloadVersionsData->meta->vendor_name, $downloadVersionsData->meta->module_name)) {

                            $modulesList = $modulesEntity->findNotInstalled($downloadVersionsData->meta->vendor_name, $downloadVersionsData->meta->module_name);
                            foreach ($modulesList as $module) {
                                $preview = $moduleCore->findModulePreview($module->vendor, $module->module_name);
                                if (!empty($preview)) {
                                    $module->preview = $preview;
                                }
                                $module->params = $moduleCore->getModuleParams($module->vendor, $module->module_name);
                            }
                            
                            $this->design->assign('now_downloaded', true);
                            $this->design->assign('modules', $modulesList);
                            $response = [
                                'success' => true,
                                'modules' => $this->design->fetch('module_list.tpl'),
                            ];
                        } else {
                            $response = [
                                'error' => $backendTranslations->getTranslation('m_modules_ready_exists')
                            ];
                        }
                    } elseif (empty($downloadUrl)) {
                        $response = [
                            'error' => $backendTranslations->getTranslation('m_modules_wrong_version')
                        ];
                    } else {
                        $response = [
                            'error' => $backendTranslations->getTranslation('m_modules_error_download_zip')
                        ];
                    }
                }
            }
        }

        $this->response->setContent(json_encode($response), RESPONSE_JSON);
    }
    
    public function marketplace(
        BackendModulesHelper $backendModulesHelper,
        ModulesEntity $modulesEntity
    ) {
        
        $searchData = $backendModulesHelper->findModules();
        $this->design->assign('search_modules', $searchData);

        $modulesList = [];
        foreach ($modulesEntity->find() as $module) {
            $modulesList[$module->vendor][$module->module_name] = $module;
        }
        $this->design->assign('installed_modules', $modulesList);
        
        $this->response->setContent($this->design->fetch('modules_from_marketplace.tpl'));
    }
    
    public function ajaxPagination(BackendModulesHelper $backendModulesHelper, ModulesEntity $modulesEntity)
    {
        if ($nextPage = $this->request->get('next_page')) {
            $searchData = $backendModulesHelper->request(htmlspecialchars_decode($nextPage));
            
            $this->design->assign('search_modules', $searchData);

            $modulesList = [];
            foreach ($modulesEntity->find() as $module) {
                $modulesList[$module->vendor][$module->module_name] = $module;
            }
            $this->design->assign('installed_modules', $modulesList);
            
            $result = [
                'result' => $this->design->fetch('search_modules.tpl'),
            ];
            if (!empty($searchData->links->next)) {
                $result['next_page'] = $this->request->url([
                    'controller' => 'ModulesAdmin@ajaxPagination',
                    'next_page' => $searchData->links->next,
                ]);
            }
        } else {
            $result = ['error' => 'empty next page'];
        }
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
    
    public function ajaxSearch(BackendModulesHelper $backendModulesHelper, ModulesEntity $modulesEntity)
    {
        $query = $this->request->get('query');
        $searchData = $backendModulesHelper->findModules($query);
        
        $this->design->assign('search_modules', $searchData);

        $modulesList = [];
        foreach ($modulesEntity->find() as $module) {
            $modulesList[$module->vendor][$module->module_name] = $module;
        }
        $this->design->assign('installed_modules', $modulesList);
        
        $result = [
            'result' => $this->design->fetch('search_modules.tpl'),
        ];
        if (!empty($searchData->links->next)) {
            $result['next_page'] = $this->request->url([
                'controller' => 'ModulesAdmin@ajaxPagination',
                'next_page' => $searchData->links->next,
            ]);
        }
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
}