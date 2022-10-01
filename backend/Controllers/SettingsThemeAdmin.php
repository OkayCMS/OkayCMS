<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendSettingsHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendSettingsRequest;

class SettingsThemeAdmin extends IndexAdmin
{
    public function fetch(
        BackendSettingsRequest $settingsRequest,
        BackendSettingsHelper  $backendSettingsHelper,
        BackendValidateHelper  $backendValidateHelper
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


            $backendSettingsHelper->initSettings();
            $this->design->assign('message_success', 'saved');
        }

        $cssVariables    = $backendSettingsHelper->getCssVariables();
        $allowExt        = $backendSettingsHelper->getAllowImageExtensions();
        $jsSocials       = $backendSettingsHelper->getJsSocials();
        $jsCustomSocials = $backendSettingsHelper->getJsCustomSocials();
        $sitePhones      = $backendSettingsHelper->getSitePhones();
        $siteSocialLinks = $backendSettingsHelper->getSiteSocialLinks();

        $this->design->assign('css_variables',     $cssVariables);
        $this->design->assign('allow_ext',         $allowExt);
        $this->design->assign('js_socials',        $jsSocials);
        $this->design->assign('js_custom_socials', $jsCustomSocials);
        $this->design->assign('site_phones',       $sitePhones);
        $this->design->assign('site_social_links', $siteSocialLinks);

        $this->response->setContent($this->design->fetch('settings_theme.tpl'));
    }

}
