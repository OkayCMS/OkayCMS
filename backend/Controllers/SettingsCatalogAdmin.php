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

            if ($settingsCatalogRequest->postTruncateTableConfirm() === "1") {
                if ($error = $backendValidateHelper->getTruncateTableValidateError()) {
                    $this->design->assign('message_error', $error);
                } else {
                    $backendSettingsCatalogHelper->clearCatalog();
                }
            }
            if ($settingsCatalogRequest->postTruncateTableConfirm() === "2") {
                if ($error = $backendValidateHelper->getTruncateTableValidateError()) {
                    $this->design->assign('message_error', $error);
                } else {
                    $backendSettingsCatalogHelper->clearCategory();
                }
            }
            if ($settingsCatalogRequest->postTruncateTableConfirm() === "3") {
                if ($error = $backendValidateHelper->getTruncateTableValidateError()) {
                    $this->design->assign('message_error', $error);
                } else {
                    $backendSettingsCatalogHelper->clearBrand();
                }
            }
            if ($settingsCatalogRequest->postTruncateTableConfirm() === "4") {
                if ($error = $backendValidateHelper->getTruncateTableValidateError()) {
                    $this->design->assign('message_error', $error);
                } else {
                    $backendSettingsCatalogHelper->clearFeature();
                }
            }
            if ($settingsCatalogRequest->postTruncateTableConfirm() === "5") {
                if ($error = $backendValidateHelper->getTruncateTableValidateError()) {
                    $this->design->assign('message_error', $error);
                } else {
                    $backendSettingsCatalogHelper->clearBlog();
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
