<?php


namespace Okay\Helpers;


use Okay\Core\EntityFactory;
use Okay\Entities\CurrenciesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class MoneyHelper
{
    private $entityFactory;
    private static $currencies;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }

    public function convertVariantsPriceToMainCurrency(array $variants = [])
    {
        if (empty($variants)) {
            return ExtenderFacade::execute(__METHOD__, $variants, func_get_args());
        }
        
        foreach ($variants as &$variant) {
            $variant = $this->convertVariantPriceToMainCurrency($variant);
        }

        return ExtenderFacade::execute(__METHOD__, $variants, func_get_args());
    }

    public function convertVariantPriceToMainCurrency($variant)
    {
        if (empty($variant)) {
            return ExtenderFacade::execute(__METHOD__, $variant, func_get_args());
        }

        $currencies = $this->getCurrenciesList();
        if (!isset($currencies[$variant->currency_id])) {
            return ExtenderFacade::execute(__METHOD__, $variant, func_get_args());
        }

        $variantCurrency = $currencies[$variant->currency_id];
        if (!empty($variant->currency_id) && $variantCurrency->rate_from != $variantCurrency->rate_to) {
            $variant->price = round($variant->price * $variantCurrency->rate_to / $variantCurrency->rate_from, 2);
            $variant->compare_price = round($variant->compare_price * $variantCurrency->rate_to / $variantCurrency->rate_from, 2);
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