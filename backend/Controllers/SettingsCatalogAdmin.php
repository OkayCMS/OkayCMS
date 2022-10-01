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

            if (!empty($this->request->post('truncate_table_confirm')) || !empty($this->request->post('truncate_table_confirm_entity'))) {
                if (empty($error = $backendValidateHelper->getTruncateTableValidateError())) {
                    if ($settingsCatalogRequest->postTruncateTableConfirm() === "1") {
                        $backendSettingsCatalogHelper->clearCatalog();

                    } elseif ($settingsCatalogRequest->postTruncateTableConfirmEntity() === "category") {
                        $backendSettingsCatalogHelper->clearCategorys();

                    } elseif ($settingsCatalogRequest->postTruncateTableConfirmEntity() === "product") {
                        $backendSettingsCatalogHelper->clearProducts();

                    } elseif ($settingsCatalogRequest->postTruncateTableConfirmEntity() === "brand") {
                        $backendSettingsCatalogHelper->clearBrands();

                    } elseif ($settingsCatalogRequest->postTruncateTableConfirmEntity() === "feature") {
                        $backendSettingsCatalogHelper->clearFeatures();

                    } elseif ($settingsCatalogRequest->postTruncateTableConfirmEntity() === "blog") {
                        $backendSettingsCatalogHelper->clearBlogs();
                    }
                } elseif (!empty($error)) {
                    $this->design->assign('message_error', $error);
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
