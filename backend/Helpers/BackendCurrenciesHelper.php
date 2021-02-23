<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Database;
use Okay\Core\EntityFactory;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Entities\CurrenciesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\DiscountsEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\CouponsEntity;
use Okay\Entities\VariantsEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\DeliveriesEntity;

class BackendCurrenciesHelper
{
    /**
     * @var CurrenciesEntity
     */
    private $currenciesEntity;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var Request
     */
    private $request;

    public function __construct(
        EntityFactory $entityFactory,
        QueryFactory  $queryFactory,
        Database      $db,
        Request       $request
    ){
        $this->currenciesEntity = $entityFactory->get(CurrenciesEntity::class);
        $this->queryFactory     = $queryFactory;
        $this->db               = $db;
        $this->request          = $request;
    }

    public function findAllCurrencies()
    {
        $currencies = $this->currenciesEntity->mappedBy('id')->find();
        return ExtenderFacade::execute(__METHOD__, $currencies, func_get_args());
    }

    public function prepareUpdateCurrencies($currencies)
    {
        return ExtenderFacade::execute(__METHOD__, $currencies, func_get_args());
    }

    public function validateCurrencies($currencies)
    {
        $wrongIso = [];

        foreach ($currencies as $currency) {
            if (!preg_match('(^[a-zA-Z]{1,3}$)', $currency->code)) {
                $wrongIso[] = $currency->name;
            }
        }

        $error = '';
        if (count($wrongIso) > 0) {
            $error = 'wrong_iso';
        }

        return ExtenderFacade::execute(__METHOD__, [$error, $wrongIso], func_get_args());
    }

    public function updateCurrencies($currencies)
    {
        foreach ($currencies as $currency) {
            if ($currency->id) {
                $this->currenciesEntity->update($currency->id, $currency);
            } else {
                unset($currency->id);
                $currency->id = $this->currenciesEntity->add($currency);
            }
        }

        $currenciesIds = [];
        foreach ($currencies as $currency) {
            $currenciesIds[] = $currency->id;
        }

        $currenciesIdsToDelete = $this->currenciesEntity->find(['not_in_ids' => $currenciesIds]);
        $this->currenciesEntity->delete($currenciesIdsToDelete);

        return ExtenderFacade::execute(__METHOD__, $currencies, func_get_args());
    }

    public function recalculateCurrencies($currencies)
    {
        // Пересчитать курсы
        $oldCurrency = $this->currenciesEntity->getMainCurrency();
        $newCurrency = reset($currencies);
        if (!empty($oldCurrency) && $oldCurrency->id != $newCurrency->id) {
            $coef = $newCurrency->rate_from/$newCurrency->rate_to;
            /*Пересчет цен по курсу валюты*/
            if ($this->request->post('recalculate') == 1) {
                $sql = $this->queryFactory->newSqlQuery();
                $sql->setStatement("UPDATE ".VariantsEntity::getTable()." SET price=price*{$coef}, compare_price=compare_price*{$coef} where currency_id=0");
                $this->db->query($sql);

                $sql = $this->queryFactory->newSqlQuery();
                $sql->setStatement("UPDATE ".DeliveriesEntity::getTable()." SET price=price*{$coef}, free_from=free_from*{$coef}");
                $this->db->query($sql);

                $sql = $this->queryFactory->newSqlQuery();
                $sql->setStatement("UPDATE ".OrdersEntity::getTable()." SET delivery_price=delivery_price*{$coef}");
                $this->db->query($sql);

                $sql = $this->queryFactory->newSqlQuery();
                $sql->setStatement("UPDATE ".OrdersEntity::getTable()." SET undiscounted_total_price=undiscounted_total_price*{$coef}");
                $this->db->query($sql);

                $sql = $this->queryFactory->newSqlQuery();
                $sql->setStatement("UPDATE ".OrdersEntity::getTable()." SET total_price=total_price*{$coef}");
                $this->db->query($sql);

                $sql = $this->queryFactory->newSqlQuery();
                $sql->setStatement("UPDATE ".PurchasesEntity::getTable()." SET undiscounted_price=undiscounted_price*{$coef}");
                $this->db->query($sql);

                $sql = $this->queryFactory->newSqlQuery();
                $sql->setStatement("UPDATE ".PurchasesEntity::getTable()." SET price=price*{$coef}");
                $this->db->query($sql);

                $sql = $this->queryFactory->newSqlQuery();
                $sql->setStatement("UPDATE ".CouponsEntity::getTable()." SET value=value*{$coef} WHERE type='absolute'");
                $this->db->query($sql);

                $sql = $this->queryFactory->newSqlQuery();
                $sql->setStatement("UPDATE ".CouponsEntity::getTable()." SET min_order_price=min_order_price*{$coef}");
                $this->db->query($sql);

                $sql = $this->queryFactory->newSqlQuery();
                $sql->setStatement("UPDATE ".DiscountsEntity::getTable()." SET value=value*{$coef} WHERE type='absolute'");
                $this->db->query($sql);
            }

            $sql = $this->queryFactory->newSqlQuery();
            $sql->setStatement("UPDATE ".CurrenciesEntity::getTable()." SET rate_from=1.0*rate_from*$newCurrency->rate_to/$oldCurrency->rate_to");
            $this->db->query($sql);

            $sql = $this->queryFactory->newSqlQuery();
            $sql->setStatement("UPDATE ".CurrenciesEntity::getTable()." SET rate_to=1.0*rate_to*$newCurrency->rate_from/$oldCurrency->rate_from");
            $this->db->query($sql);

            $sql = $this->queryFactory->newSqlQuery();
            $sql->setStatement("UPDATE ".CurrenciesEntity::getTable()." SET rate_to = rate_from WHERE id={$newCurrency->id}");
            $this->db->query($sql);

            $sql = $this->queryFactory->newSqlQuery();
            $sql->setStatement("UPDATE ".CurrenciesEntity::getTable()." SET rate_to = 1, rate_from = 1 WHERE (rate_to=0 OR rate_from=0) AND id={$newCurrency->id}");
            $this->db->query($sql);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function checkWrongIso($currencies)
    {
        $wrongIso = [];
        foreach ($currencies as $currency) {
            if (!preg_match('(^[a-zA-Z]{1,3}$)', $currency->code)) {
                $wrongIso[] = $currency->name;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $wrongIso, func_get_args());
    }

    public function sortCurrencies($currencies)
    {
        $currenciesIds = [];
        foreach ($currencies as $currency) {
            $currenciesIds[] = $currency->id;
        }

        asort($currenciesIds);
        $i = 0;
        foreach ($currenciesIds as $currencyId) {
            $this->currenciesEntity->update($currenciesIds[$i], ['position'=>$currencyId]);
            $i++;
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function disable($id)
    {
        $this->currenciesEntity->update($id, ['enabled'=>0]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function enable($id)
    {
        $this->currenciesEntity->update($id, ['enabled'=>1]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function showCents($id)
    {
        $this->currenciesEntity->update($id, ['cents'=>2]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function hideCents($id)
    {
        $this->currenciesEntity->update($id, ['cents'=>0]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete($id)
    {
        $this->currenciesEntity->delete($id);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getMainCurrency()
    {
        $currency = $this->currenciesEntity->getMainCurrency();
        return ExtenderFacade::execute(__METHOD__, $currency, func_get_args());
    }
}