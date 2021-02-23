<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendManagersHelper;
use Okay\Admin\Helpers\BackendSettingsHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendSettingsRequest;

class SettingsCatalogAdmin extends IndexAdmin
{
    /*Настройки каталога*/
    public function fetch(
        BackendSettingsHelper $backendSettingsCatalogHelper,
        BackendManagersHelper $backendManagersHelper,
        BackendSettingsRequest $settingsCatalogRequest,
        BackendValidateHelper         $backendValidateHelper
    ) {
        $managersList = $backendManagersHelper->findManagers();
        $this->design->assign('managers', $managersList);

        if ($this->request->method('POST')) {
            
            if ($this->request->post('save')) {
                $backendSettingsCatalogHelper->updateSettings();
            }

            if ($settingsCatalogRequest->postTruncateTableConfirm()) {
                if ($error = $backendValidateHelper->getTruncateTableValidateError()) {
                    $this->design->assign('message_error', $error);
                } else {
                    $backendSettingsCatalogHelper->clearCatalog();
                }
            }

            $error = $backendSettingsCatalogHelper->updateWatermark();
            if (!empty($error)) {
                $this->design->assign('message_error', $error);
            }

            $this->design->assign('message_success', 'saved');
        }

        $this->response->setContent($this->design->fetch('settings_catalog.tpl'));
    }
}
