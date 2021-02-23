<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class CurrenciesEntity extends Entity {
    
    protected static $fields = [
        'id',
        'code',
        'rate_from',
        'rate_to',
        'cents',
        'position',
        'enabled',
    ];

    protected static $langFields = [
        'name',
        'sign',
    ];

    protected static $defaultOrderFields = [
        'position',
    ];

    protected static $table = '__currencies';
    protected static $langObject = 'currency';
    protected static $langTable = 'currencies';
    protected static $tableAlias = 'c';
    protected static $alternativeIdField = 'code';

    private $mainCurrency;
    
    public function getMainCurrency()
    {
        if (empty($this->mainCurrency)) {
            if ($currencies = $this->find()) {
                $this->mainCurrency = reset($currencies);
            } else {
                return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->mainCurrency, func_get_args());
    }

    public function delete($ids)
    {
        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->entity->get(PaymentsEntity::class);

        /** @var VariantsEntity $variantsEntity */
        $variantsEntity = $this->entity->get(VariantsEntity::class);

        $mainCurrency      = $this->getMainCurrency();
        $paymentMethodsIds = $paymentsEntity->cols(['id'])->find(['currency_id' => $ids]);
        $paymentsEntity->update($paymentMethodsIds, ['currency_id' => $mainCurrency->id]);

        if ($this->isSingleId($ids)) {
            $id = $ids;
            $variantsEntity->pricesToMainCurrencyByCurrencyId($id);
            return parent::delete($id);
        }

        foreach ($ids as $id) {
            $variantsEntity->pricesToMainCurrencyByCurrencyId($id);
        }

        return parent::delete($ids);
    }

    private function isSingleId($ids)
    {
        return !is_array($ids);
    }

    public function filter__not_in_ids($ids)
    {
        $this->select->where('id NOT IN(:id)')
            ->bindValue('id', $ids);
    }
}
