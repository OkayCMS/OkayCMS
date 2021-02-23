<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Requests\BackendDiscountsRequest;
use Okay\Core\BackendTranslations;
use Okay\Core\Discounts;

class DiscountsSettingsAdmin extends IndexAdmin
{
    public function fetch(
        Discounts               $discountsCore,
        BackendTranslations     $backendTranslations,
        BackendDiscountsRequest $backendDiscountsRequest
    ) {
        if ($this->request->method('post')) {
            $this->settings->set('purchase_discount_sets', $backendDiscountsRequest->postPurchaseSets());
            $this->settings->set('cart_discount_sets', $backendDiscountsRequest->postCartSets());
        }

        $registeredSigns = $discountsCore->getRegisteredSigns();
        foreach ($registeredSigns['purchase'] as $registeredSign) {
            $registeredSign->name = $backendTranslations->getTranslation($registeredSign->name);
            $registeredSign->description = $backendTranslations->getTranslation($registeredSign->description);
        }
        foreach ($registeredSigns['cart'] as $registeredSign) {
            $registeredSign->name = $backendTranslations->getTranslation($registeredSign->name);
            $registeredSign->description = $backendTranslations->getTranslation($registeredSign->description);
        }
        $this->design->assign('registered_signs', $registeredSigns);
        $this->design->assign('purchase_sets', $this->settings->purchase_discount_sets);
        $this->design->assign('cart_sets', $this->settings->cart_discount_sets);

        $this->response->setContent($this->design->fetch('discounts_settings.tpl'));
    }
}