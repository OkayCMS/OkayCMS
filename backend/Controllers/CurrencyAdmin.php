<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendCurrenciesHelper;
use Okay\Admin\Requests\BackendCurrenciesRequest;

class CurrencyAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendCurrenciesHelper  $backendCurrenciesHelper,
        BackendCurrenciesRequest $currenciesRequest
    ){
        // Обработка действий
        if ($this->request->method('post')) {
            $currencies = $currenciesRequest->postCurrencies();
            $currencies = $backendCurrenciesHelper->prepareUpdateCurrencies($currencies);
            $currencies = $backendCurrenciesHelper->updateCurrencies($currencies);

            $wrongIso = $backendCurrenciesHelper->checkWrongIso($currencies);
            if (count($wrongIso) > 0) {
                $this->design->assign('message_error', 'wrong_iso');
                $this->design->assign('wrong_iso', $wrongIso);
            }

            $backendCurrenciesHelper->recalculateCurrencies($currencies);
            $backendCurrenciesHelper->sortCurrencies($currencies);
            
            // Действия с выбранными
            $action = $currenciesRequest->postAction();
            $id     = $currenciesRequest->postactionId();
            if (!empty($action) && !empty($id)) {
                switch ($action) {
                    case 'disable': {
                        $backendCurrenciesHelper->disable($id);
                        break;
                    }
                    case 'enable': {
                        $backendCurrenciesHelper->disable($id);
                        break;
                    }
                    case 'show_cents': {
                        $backendCurrenciesHelper->showCents($id);
                        break;
                    }
                    case 'hide_cents': {
                        $backendCurrenciesHelper->hideCents($id);
                        break;
                    }
                    case 'delete': {
                        $backendCurrenciesHelper->delete($id);
                        break;
                    }
                }
            }
        }

        $currencies = $backendCurrenciesHelper->findAllCurrencies();
        $currency   = $backendCurrenciesHelper->getMainCurrency();
        $this->design->assign('currency', $currency);
        $this->design->assign('currencies', $currencies);
        
        $this->response->setContent($this->design->fetch('currency.tpl'));
    }
    
}
