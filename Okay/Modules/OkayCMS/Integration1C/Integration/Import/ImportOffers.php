<?php


namespace Okay\Modules\OkayCMS\Integration1C\Integration\Import;


use Okay\Entities\CurrenciesEntity;
use Okay\Entities\VariantsEntity;
use Okay\Modules\OkayCMS\Integration1C\Integration\Integration1C;

class ImportOffers extends AbstractImport
{

    /**
     * @var array список валют, ключ массива - code
     */
    protected $currenciesByCode;

    /**
     * @var array список валют, ключ массива - sign
     */
    protected $currenciesBySign;

    /**
     * @var bool false - на сайте одна валюта, true - много. 
     */
    protected $isMultiCurrency = false;

    /**
     * @var object основная валюта сайта
     */
    protected $baseCurrency;
    
    public function __construct(Integration1C $integration1C)
    {
        parent::__construct($integration1C);
        $this->initCurrencies();
    }
    
    /**
     * @param string $xml_file Full path to xml file
     * @return string
     */
    public function import($xml_file) {
        
        // Варианты
        $z = new \XMLReader;
        $z->open($xml_file);

        while ($z->read() && $z->name !== 'Предложение');

        // Последний вариант, на котором остановились
        $lastVariantNum = 0;
        if (!empty($this->integration1C->getFromStorage('imported_variant_num'))) {
            $lastVariantNum = $this->integration1C->getFromStorage('imported_variant_num');
        }

        // Номер текущего товара
        $currentVariantNum = 0;

        while ($z->name === 'Предложение') {
            if ($currentVariantNum >= $lastVariantNum) {
                $xml = new \SimpleXMLElement($z->readOuterXML());
                // Варианты
                $this->importVariant($xml);

                $execTime = microtime(true) - $this->integration1C->startTime;
                if ($execTime+1 >= $this->integration1C->maxExecTime) {
                    
                    // Запоминаем на каком предложении остановились
                    $this->integration1C->setToStorage('imported_variant_num', $currentVariantNum);

                    $result =  "progress\n";
                    $result .=  "Выгружено ценовых предложений: $currentVariantNum\n";
                    return $result;
                }
            }
            $z->next('Предложение');
            $currentVariantNum ++;
        }
        $z->close();
        
        $this->integration1C->setToStorage('imported_product_num', '');
        return "success\n";
    }

    /**
     * @param $xmlVariant \SimpleXMLElement()
     * @return bool
     */
    protected function importVariant($xmlVariant)
    {
        
        /** @var VariantsEntity $variantsEntity */
        $variantsEntity = $this->integration1C->entityFactory->get(VariantsEntity::class);
        
        $variant = new \stdClass;
        
        //  Id товара и варианта (если есть) по 1С
        @list($product1cId, $variant1cId) = explode('#', $xmlVariant->Ид);
        if (empty($variant1cId)) {
            $variant1cId = '';
        }
        
        if (empty($product1cId)) {
            return false;
        }

        $select = $this->integration1C->queryFactory->newSelect();
        $select->cols(['v.id'])
            ->from('__variants v')
            ->where('v.external_id=:v_external_id')
            ->where('product_id=(SELECT p.id FROM __products p WHERE p.external_id=:p_external_id LIMIT 1)')
            ->bindValues([
                'v_external_id' => $variant1cId,
                'p_external_id' => $product1cId,
            ]);
        $this->integration1C->db->query($select);
        $variantId = $this->integration1C->db->result('id');

        $select = $this->integration1C->queryFactory->newSelect();
        $select->cols(['p.id'])
            ->from('__products p')
            ->where('p.external_id=:external_id')
            ->bindValue('external_id', $product1cId);
        $this->integration1C->db->query($select);
        $variant->external_id = $variant1cId;
        $variant->product_id = $this->integration1C->db->result('id');
        if (empty($variant->product_id)) {
            return false;
        }

        if ($this->integration1C->guidComparePriceFrom1C) {
            foreach ($xmlVariant->Цены->Цена as $priceElement) {
                if ($this->integration1C->guidComparePriceFrom1C == (string)$priceElement->ИдТипаЦены) {
                    $variant->compare_price = (float)$priceElement->ЦенаЗаЕдиницу;
                }
            }
        }
        
        if ($this->integration1C->guidPriceFrom1C) {
            foreach ($xmlVariant->Цены->Цена as $priceElement) {
                if ($this->integration1C->guidPriceFrom1C == (string)$priceElement->ИдТипаЦены) {
                    $xmlVariantPrice = $priceElement;
                }
            }
        } elseif (isset($xmlVariant->Цены->Цена->ЦенаЗаЕдиницу)) {
            $xmlVariantPrice = $xmlVariant->Цены->Цена;
        }
        
        if (!empty($xmlVariantPrice)) {
            $variant->price = (float)$xmlVariantPrice->ЦенаЗаЕдиницу;
        }

        if (isset($xmlVariant->ХарактеристикиТовара->ХарактеристикаТовара)) {
            foreach ($xmlVariant->ХарактеристикиТовара->ХарактеристикаТовара as $xmlProperty) {
                $values[] = $xmlProperty->Значение;
            }
        }
        if (!empty($values)) {
            $variant->name = implode(', ', $values);
        }
        $sku = (string)$xmlVariant->Артикул;
        if (!empty($sku)) {
            $variant->sku = $sku;
        }

        $variantCurrency = null;
        // Конвертируем цену из валюты 1С в базовую валюту магазина
        if (!empty($xmlVariantPrice->Валюта)) {
            
            $currency_code = (string)$xmlVariantPrice->Валюта;
            // Ищем валюту по коду или обозначению
            if (isset($this->currenciesByCode[$currency_code])) {
                $variantCurrency = $this->currenciesByCode[$currency_code];
            } elseif (isset($this->currenciesBySign[$currency_code])) {
                $variantCurrency = $this->currenciesBySign[$currency_code];
            }
            
            // Если нашли валюту - конвертируем из нее в базовую
            if ($variantCurrency && $variantCurrency->rate_from>0 && $variantCurrency->rate_to>0 && !$this->isMultiCurrency) {
                $variant->price = floatval($variant->price)*$variantCurrency->rate_to/$variantCurrency->rate_from;
            }
        }

        // Если $stockFrom1c = true берем кол-во из 1с или у нас бесконечное количество товара.
        if ($this->integration1C->stockFrom1c) {
            $variant->stock = (int)$xmlVariant->Количество;
        } else {
            $variant->stock = NULL;
        }
        
        // Устанавливаем валюту товара или оригинал или если пересчитали то базовую (единственную активную)
        $variant->currency_id = ($this->isMultiCurrency === true && !empty($variantCurrency->id) ? $variantCurrency->id : $this->baseCurrency->id);

        // Устанавливаем единицу измерения
        if (!empty($xmlVariantPrice) && !$variant->units = (string)$xmlVariant->БазоваяЕдиница) {
            $variant->units = (string)$xmlVariantPrice->Единица;
        }

        if (empty($variantId)) {
            $variantsEntity->add($variant);
        } else {
            $variantsEntity->update($variantId, $variant);
        }
        return true;
    }

    /**
     * Метод инициализирует валюты для импорта
     */
    protected function initCurrencies()
    {
        
        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->integration1C->entityFactory->get(CurrenciesEntity::class);
        
        $currencyFilter = [];
        if ($this->integration1C->onlyEnabledCurrencies) {
            $currencyFilter['enabled'] = 1;
        }

        foreach ($currenciesEntity->find($currencyFilter) as $c) {
            $this->currenciesByCode[$c->code] = $c;
            $this->currenciesBySign[$c->sign] = $c;
        }

        $this->isMultiCurrency = count($this->currenciesByCode) > 1;
        $this->baseCurrency = reset($this->currenciesByCode);
    }
}
