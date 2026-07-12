<?php

namespace Okay\Entities;

use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Stock\VariantAvailabilityFactory;

class VariantsEntity extends Entity
{
    protected static $fields = [
        'id',
        'product_id',
        'sku',
        'weight',
        'price',
        'compare_price',
        'stock',
        'position',
        'external_id',
        'currency_id',
    ];

    protected static $additionalFields = [
        '(v.stock IS NULL) as infinity',
        'c.rate_from',
        'c.rate_to',
    ];

    protected static $langFields = [
        'name',
        'units',
    ];

    protected static $defaultOrderFields = [
        'position',
        'id',
    ];

    protected static $table = '__variants';
    protected static $langObject = 'variant';
    protected static $langTable = 'variants';
    protected static $tableAlias = 'v';

    public function get($id)
    {
        if (empty($id)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
        }

        $this->select->join('left', '__currencies AS c', 'c.id=v.currency_id');

        $variant = parent::get($id);

        return $this->resetInfo($variant);
    }

    public function find(array $filter = [])
    {
        $this->select->join('left', '__currencies AS c', 'c.id=v.currency_id');
        $variants = parent::find($filter);

        if (is_object(reset($variants))) {
            foreach ($variants as &$variant) {
                $variant = $this->resetInfo($variant);
            }
        }

        return $variants;
    }

    public function pricesToMainCurrencyByCurrencyId($id)
    {
        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->entity->get(CurrenciesEntity::class);

        $currency         = $currenciesEntity->get((int) $id);
        $mainCurrency     = $currenciesEntity->getMainCurrency();
        $mainCurrencyCoef = $this->getMainCurrencyCoef($currency);

        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement("UPDATE " . self::getTable() . " SET price=price*{$mainCurrencyCoef}, compare_price=compare_price*{$mainCurrencyCoef}, currency_id=:main_currency_id WHERE currency_id=:currency_id")
            ->bindValue('currency_id', $id)
            ->bindValue('main_currency_id', $mainCurrency->id);
        $this->db->query($sql);

        ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    protected function customOrder($order = null, array $orderFields = [], array $additionalData = [])
    {

        switch ($order) {
            case 'in_stock_first':
                if (!$this->settings->get('is_preorder')) {
                    $orderFields = [
                        'CASE WHEN stock > 0 THEN 2 WHEN stock IS NULL THEN 1 ELSE 0 END DESC',
                        'position',
                        'id',
                    ];
                }
                break;
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $orderFields, func_get_args());
    }

    protected function resetInfo($variant)
    {
        if (empty($variant)) {
            return $variant;
        }
        if (property_exists($variant, 'compare_price') && $variant->compare_price == 0) {
            $variant->compare_price = null;
        }

        if (property_exists($variant, 'stock')) {
            $rawStock = $variant->stock === null ? null : (int) $variant->stock;
            /** @var VariantAvailabilityFactory $availabilityFactory */
            $availabilityFactory = $this->serviceLocator->getService(VariantAvailabilityFactory::class);
            $availability = $availabilityFactory->fromRawStock(
                $rawStock,
                (bool) $this->settings->get('is_preorder'),
                (int) $this->settings->get('max_order_amount'),
                (bool) $this->settings->get('use_backorder_status')
            );

            $variant->stock_raw = $availability->rawStock();
            $variant->stock_status = $availability->status();
            $variant->stock_effective_status = $availability->effectiveStatus();
            $variant->stock_is_tracked = $availability->isTracked();
            $variant->available_to_order = $availability->isOrderable();
            $variant->order_amount_limit = $availability->orderLimit();
            $variant->schema_availability = $availability->schemaAvailabilityUrl();

            if ($variant->stock === null) {
                $variant->stock = $this->settings->max_order_amount;
            }
        }

        if (property_exists($variant, 'units') && $variant->units === null) {
            $variant->units = $this->settings->units;
        }

        return $variant;
    }

    protected function filter__in_stock()
    {
        if (empty($this->settings->get('is_preorder')) || ($this->settings->get('is_preorder') != 1)) {
            $this->select->where('(v.stock > 0 OR v.stock IS NULL)');
        }
    }

    protected function filter__not_id($id)
    {
        $this->select->where("id!=:not_id");
        $this->select->bindValue('not_id', (int)$id);
    }

    protected function filter__has_price()
    {
        $this->select->where("price > 0");
    }

    private function getMainCurrencyCoef($currency)
    {
        return $currency->rate_to / $currency->rate_from;
    }
}
