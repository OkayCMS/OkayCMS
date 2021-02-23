<?php


namespace Okay\Admin\Controllers;


use Giggsey\Locale\Locale;
use libphonenumber\PhoneNumberUtil;
use Okay\Admin\Helpers\BackendSettingsHelper;
use Okay\Core\BackendTranslations;
use Okay\Core\Phone;

class SettingsGeneralAdmin extends IndexAdmin
{
    public function fetch(
        BackendSettingsHelper  $backendSettingsHelper,
        BackendTranslations $backendTranslations,
        Phone $phone
    ) {
        if ($this->request->method('post')) {
            $backendSettingsHelper->updateGeneralSettings();
            $this->design->assign('message_success', 'saved');
        }
        
        // Передаем название стран
        switch ($backendTranslations->getLangLabel()) {
            case 'ua';
                $countries = Locale::getAllCountriesForLocale('uk');
            break;
            case 'ru';
                $countries = Locale::getAllCountriesForLocale('ru');
            break;
            default:
                $countries = Locale::getAllCountriesForLocale('en');
        }

        $phoneUtil = PhoneNumberUtil::getInstance();

        // Передаем пример номера телефона для указанной страны
        $this->design->assign('phone_example', $phone->getPhoneExample());
        $this->design->assign('phone_regions', $phoneUtil->getSupportedRegions());
        $this->design->assign('phone_regions_names', $countries);
        
        $this->response->setContent($this->design->fetch('settings_general.tpl'));
    }
}