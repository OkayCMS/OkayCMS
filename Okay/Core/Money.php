<?php


namespace Okay\Core;


use Okay\Entities\CurrenciesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class Money
{

    /**
     * @var EntityFactory 
     */
    private $entityFactory;
    
    private $decimalsPoint;
    private $thousandsSeparator;
    private static $currentCurrency;
    private static $currencies;
    
    public function __construct(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }
    
    public function setCurrency($currency)
    {
        if (empty($currency->id)) {
            throw new \Exception('Wrong currency');
        }
        self::$currencies[$currency->id] = $currency;
    }

    public function getCoefMoney()
    {
        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);
        $mainCurrency = $currenciesEntity->getMainCurrency();
        
        $coef = 1;
        if (isset($_SESSION['currency_id']) && $mainCurrency->id != $_SESSION['currency_id']) {
            $currency = $this->entityFactory->get(CurrenciesEntity::class)->get(intval($_SESSION['currency_id']));

            if (empty($currency)) {
                $_SESSION['currency_id'] = $mainCurrency->id;
                return $coef;
            }

            $coef = $currency->rate_from / $currency->rate_to;
        }

        return ExtenderFacade::execute(__METHOD__, $coef, func_get_args());
    }
    
    public function convert($price, $currencyId = null, $format = true, $revers = false) : string
    {
        if ($currencyId !== null && !is_numeric($currencyId)) {
            trigger_error('$currencyId must be is integer', E_USER_WARNING);
        }
        
        if ($currencyId !== null && !empty(self::$currencies[$currencyId])) {
            $currency = self::$currencies[$currencyId];
        } elseif ($currencyId === null && !empty(self::$currentCurrency)) {
            $currency = self::$currentCurrency;
        }
        
        if (empty($currency)) {
            /** @var CurrenciesEntity $currenciesEntity */
            $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);
            if ($currencyId !== null) {
                $currency = $currenciesEntity->get((int) $currencyId);
                self::$currencies[$currency->id] = $currency;
            } elseif (isset($_SESSION['currency_id'])) { // todo работа со storage
                $currency = self::$currentCurrency = $currenciesEntity->get((int)$_SESSION['currency_id']);
            } else {
                $currency = self::$currentCurrency = current($currenciesEntity->find(['enabled' => 1]));
            }
        }
        
        $result = $this->priceConvert($price, $currency, $revers);
        $result = $this->formatPrice($result, $currency, $format);
        
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }
    
    public function configure($decimalsPoint, $thousandsSeparator)
    {
        $this->decimalsPoint = $decimalsPoint;
        $this->thousandsSeparator = $thousandsSeparator;

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    private function priceConvert($price, $currency, $revers = false) : float
    {
        $result = $price;
        if (!empty($currency)) {
            // Умножим на курс валюты
            if ($revers === true) {
                $result = $result*$currency->rate_to/$currency->rate_from;
            } else {
                $result = $result*$currency->rate_from/$currency->rate_to;
            }
        }
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }
    
    private function formatPrice($price, $currency, $format = true) : string
    {
        // Точность отображения, знаков после запятой
        $precision = 0;
        if (!empty($currency)) {
            $precision = isset($currency->cents) ? $currency->cents : 2;
        }
        
        // Форматирование цены
        if ($format) {
            $result = number_format($price, $precision, $this->decimalsPoint, $this->thousandsSeparator);
        } else {
            $result = round($price, $precision);
        }
        
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }
    
}
