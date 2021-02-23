<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendSettingsHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendSettingsRequest;
use Okay\Entities\AdvantagesEntity;

class SettingsThemeAdmin extends IndexAdmin
{
    public function fetch(
        BackendSettingsRequest $settingsRequest,
        BackendSettingsHelper  $backendSettingsHelper,
        BackendValidateHelper  $backendValidateHelper,
        AdvantagesEntity       $advantagesEntity
    ) {
        if ($this->request->method('POST')) {
            $backendSettingsHelper->updateThemeSettings();

            // Favicon
            if (is_null($settingsRequest->postFavicon())) {
                $backendSettingsHelper->deleteFavicon();
            }

            if ($error = $backendValidateHelper->getFaviconValidateError()) {
                $this->design->assign('message_error', $error);
            } else {
                
                $backendSettingsHelper->uploadFavicon();
            }

            // Logo
            if (is_null($settingsRequest->postSiteLogo())) {
                $backendSettingsHelper->deleteSiteLogo();
            }

            $error = $backendSettingsHelper->uploadSiteLogo();
            if (!empty($error)) {
                $this->design->assign('message_error', 'wrong_favicon_ext');
            }

            $advantageImagesToUpload = $settingsRequest->filesAdvantageImages();
            $advantageUpdates        = $settingsRequest->postAdvantagesUpdates();
            $advantageImagesToDelete = $settingsRequest->postAdvantageImagesToDelete();
            foreach($advantageUpdates as $advantageId => $advantageUpdate) {
                $backendSettingsHelper->updateAdvantage(
                    $advantageId,
                    $advantageUpdate,
                    $advantageImagesToUpload,
                    $advantageImagesToDelete
                );
            }

            $positions = $settingsRequest->postPositionAdvantage();
            list($ids, $positions) = $backendSettingsHelper->sortPositionAdvantage($positions);
            $backendSettingsHelper->updatePositionAdvantage($ids, $positions);

            // Действия с выбранными
            $ids = $settingsRequest->postCheckAdvantage();
            if(!empty($ids)) {
                switch($settingsRequest->postActionAdvantage()) {
                    case 'delete': {
                        $backendSettingsHelper->deleteAdvantage($ids);
                        break;
                    }
                }
            }

            $newAdvantages      = $settingsRequest->postNewAdvantages();
            $newAdvantageImages = $settingsRequest->filesNewAdvantageImages();
            if (!empty($newAdvantages)) {
                foreach($newAdvantages as $key => $newAdvantage) {
                    $advantageId = $advantagesEntity->add($newAdvantage);
                    $backendSettingsHelper->uploadAdvantageImage($advantageId, $newAdvantageImages[$key]);
                }
            }

            $backendSettingsHelper->initSettings();
            $this->design->assign('message_success', 'saved');
        }

        $cssVariables    = $backendSettingsHelper->getCssVariables();
        $allowExt        = $backendSettingsHelper->getAllowImageExtensions();
        $jsSocials       = $backendSettingsHelper->getJsSocials();
        $jsCustomSocials = $backendSettingsHelper->getJsCustomSocials();
        $sitePhones      = $backendSettingsHelper->getSitePhones();
        $siteSocialLinks = $backendSettingsHelper->getSiteSocialLinks();
        $advantages      = $backendSettingsHelper->findAdvantages();

        $this->design->assign('css_variables',     $cssVariables);
        $this->design->assign('allow_ext',         $allowExt);
        $this->design->assign('js_socials',        $jsSocials);
        $this->design->assign('js_custom_socials', $jsCustomSocials);
        $this->design->assign('site_phones',       $sitePhones);
        $this->design->assign('site_social_links', $siteSocialLinks);
        $this->design->assign('advantages',        $advantages);

        $this->response->setContent($this->design->fetch('settings_theme.tpl'));
    }

}
