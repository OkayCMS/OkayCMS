<?php

namespace Okay\Helpers;

use Okay\Core\EntityFactory;
use Okay\Core\Settings;
use Okay\Entities\CurrenciesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

/**
 * @phpstan-type VariantPriceRow object{price: int|float|string, currency_id: int|string|null, compare_price?: int|float|string|null}&\stdClass
 */
class MoneyHelper
{
    private $entityFactory;
    private $settings;

    private static $currencies;

    public function __construct(EntityFactory $entityFactory, Settings $settings)
    {
        $this->entityFactory = $entityFactory;
        $this->settings = $settings;
    }

    /**
     * @template TKey of array-key
     * @param array<TKey, VariantPriceRow> $variants
     * @return array<TKey, VariantPriceRow>
     */
    public function convertVariantsPriceToMainCurrency(array $variants = []): array
    {
        if (empty($variants)) {
            return ExtenderFacade::execute(__METHOD__, $variants, func_get_args());
        }

        foreach ($variants as &$variant) {
            $variant = $this->convertVariantPriceToMainCurrency($variant);
        }

        return ExtenderFacade::execute(__METHOD__, $variants, func_get_args());
    }

    /**
     * @param object|null|false $variant
     * @return object|null|false
     */
    public function convertVariantPriceToMainCurrency($variant)
    {
        if (empty($variant)) {
            return ExtenderFacade::execute(__METHOD__, $variant, func_get_args());
        }

        /** @var VariantPriceRow $variant */
        // Если скидкидочная цена меньше или равна обычной цене, такую скидку не выводим
        if ($this->settings->get('hide_equal_compare_price') && isset($variant->compare_price) && $variant->compare_price <= $variant->price) {
            $variant->compare_price = null;
        }

        $currencies = $this->getCurrenciesList();
        if (!isset($currencies[$variant->currency_id])) {
            return ExtenderFacade::execute(__METHOD__, $variant, func_get_args());
        }

        $variantCurrency = $currencies[$variant->currency_id];
        if (!empty($variant->currency_id) && $variantCurrency->rate_from != $variantCurrency->rate_to) {
            $variant->price = round($variant->price * $variantCurrency->rate_to / $variantCurrency->rate_from, 2);
            if (!empty($variant->compare_price)) {
                $variant->compare_price = round($variant->compare_price * $variantCurrency->rate_to / $variantCurrency->rate_from, 2);
            }
        }

        return ExtenderFacade::execute(__METHOD__, $variant, func_get_args());
    }

    private function getCurrenciesList()
    {
        if (empty(self::$currencies)) {
            /** @var CurrenciesEntity $currenciesEntity */
            $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);
            self::$currencies = $currenciesEntity->mappedBy('id')->find();
        }

        return self::$currencies;
    }
}
