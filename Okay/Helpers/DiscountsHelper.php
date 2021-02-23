<?php


namespace Okay\Helpers;


use Okay\Core\Classes\Discount;
use Okay\Core\Discounts;
use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Languages;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Settings;
use Okay\Entities\DiscountsEntity;

class DiscountsHelper
{
    /** @var Discounts */
    private $discountsCore;

    /** @var Languages */
    private $languagesCore;

    /** @var FrontTranslations */
    private $frontTranslations;

    /** @var Settings */
    private $settings;


    /** @var DiscountsEntity */
    private $discountsEntity;

    public function __construct(
        Discounts         $discountsCore,
        FrontTranslations $frontTranslations,
        Languages         $languagesCore,
        EntityFactory     $entityFactory,
        Settings          $settings
    ) {
        $this->discountsCore     = $discountsCore;
        $this->frontTranslations = $frontTranslations;
        $this->languagesCore     = $languagesCore;
        $this->settings          = $settings;

        $this->discountsEntity = $entityFactory->get(DiscountsEntity::class);
    }

    /**
     * Get cart sets of discounts, added by user in admin panel.
     *
     * @return array|null
     */
    public function getCartSets()
    {
        return ExtenderFacade::execute(__METHOD__, $this->settings->get('cart_discount_sets'), func_get_args());
    }

    /**
     * Get purchase sets of discounts, added by user in admin panel.
     *
     * @return array|null
     */
    public function getPurchaseSets()
    {
        return ExtenderFacade::execute(__METHOD__, $this->settings->get('purchase_discount_sets'), func_get_args());
    }

    /**
     * Parse set of discounts, sort them on purchase and cart signs and check they registration in system
     *
     * @param $set string
     * @return array|bool
     */
    public function parseSet($set)
    {
        $registeredSigns = $this->discountsCore->getRegisteredSigns();
        preg_match_all('/\$(<?[A-z0-9][A-z0-9]*)/', $set->set, $matches);
        $signs = [];
        if ($matches) {
            foreach ($matches[1] as $match) {
                $fromLastDiscount = $match[0] == '<' ? false : true;
                $sign = trim($match, '<');
                if (isset($registeredSigns['cart'][$sign])) {
                    $signObject = clone $registeredSigns['cart'][$sign];
                    $signObject->fromLastDiscount = $fromLastDiscount;
                    $signObject->partial = $set->partial;
                    $signs['cart'][] = $signObject;
                } else if (isset($registeredSigns['purchase'][$sign])) {
                    $signObject = clone $registeredSigns['purchase'][$sign];
                    $signObject->fromLastDiscount = $fromLastDiscount;
                    $signObject->partial = $set->partial;
                    $signs['purchase'][] = $signObject;
                } else if (!$set->partial) {
                    return false;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $signs, func_get_args());
    }

    /**
     * Prepare discounts and combine them with information from parsed set
     *
     * @param array $signs
     * @param array $availableDiscounts
     * @return array
     */
    public function prepareDiscounts($signs, $availableDiscounts)
    {
        $discounts = [];
        if (!empty($signs)) {
            foreach ($signs as $sign) {
                if (isset($availableDiscounts[$sign->sign])) {
                    /** @var Discount $discount */
                    $discount = clone $availableDiscounts[$sign->sign];
                    $discount->fromLastDiscount = isset($discount->fromLastDiscount) ? $discount->fromLastDiscount : $sign->fromLastDiscount;
                    $discount->name = isset($discount->name) ? $discount->name : $sign->name;
                    $discount->description = isset($discount->description) ? $discount->description : $sign->description;
                    $discount->lang['name'] = $discount->name;
                    $discount->lang['description'] = $discount->description;
                    $substitutes = $this->buildReplacements($discount->langParts);
                    $discount->name = strtr($this->frontTranslations->getTranslation($discount->name), $substitutes);
                    $discount->description = strtr($this->frontTranslations->getTranslation($discount->description), $substitutes);
                    $discounts[] = $discount;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $discounts, func_get_args());
    }

    /**
     * Build replacement pairs for name and description
     *
     * @param array $parts
     * @return array
     */
    private function buildReplacements($parts)
    {
        $replacements = [];
        foreach ($parts as $key => $part) {
            $replacements["{\${$key}}"] = $part;
        }

        return ExtenderFacade::execute(__METHOD__, $replacements, func_get_args());
    }

    /**
     * Calculate discounts and price after all discounts
     *
     * @param array $discounts
     * @param string|int|float $undiscountedPrice
     * @return array
     */
    public function calculateDiscounts($discounts, $undiscountedPrice)
    {
        $discountedPrice = $undiscountedPrice;
        if (!empty($discounts)) {
            /** @var Discount $discount */
            foreach ($discounts as $discount) {
                $discount->priceBeforeDiscount = $discountedPrice;
                $discount->calculate($undiscountedPrice);
                $discountedPrice = $discount->priceAfterDiscount;
            }
        }

        return ExtenderFacade::execute(__METHOD__, [$discounts, $discountedPrice], func_get_args());
    }

    /**
     * @param Discount $discount
     * @param string $entity
     * @param string|int $entityId
     * @return array
     */
    public function prepareForDB($discount, $entity, $entityId)
    {
        $discountsToDB = $discount->getForDB($entity, $entityId);
        $langDiscountsToDB = $this->getTranslations($discount);

        return ExtenderFacade::execute(__METHOD__, [$discountsToDB, $langDiscountsToDB], func_get_args());
    }

    /**
     * Get all translations of discount from lang files
     *
     * @param Discount $discount
     * @return array
     */
    private function getTranslations($discount)
    {
        $languages = $this->languagesCore->getAllLanguages();
        $mainLanguage = $this->languagesCore->getMainLanguage();
        $langFields = $this->discountsEntity->getLangFields();
        $substitutes = $this->buildReplacements($discount->langParts);
        $langDiscount = [];
        foreach ($languages as $language) {
            $this->languagesCore->setLangId($language->id);
            $this->frontTranslations->init();
            foreach ($langFields as $langField) {
                if (isset($discount->lang[$langField])) {
                    $translation = strtr($this->frontTranslations->getTranslation($discount->lang[$langField]), $substitutes);
                    $langDiscount[$language->id][$langField] = $translation;
                }
            }
        }
        $this->languagesCore->setLangId($mainLanguage->id);
        $this->frontTranslations->init();

        return ExtenderFacade::execute(__METHOD__, $langDiscount, func_get_args());
    }

    /**
     * @param array $discountsDB
     * @return array
     */
    public function buildFromDB($discountsDB)
    {
        $discounts = [];
        foreach ($discountsDB as $discountDB) {
            $discount = new Discount();
            $discount->id = $discountDB->id;
            $discount->type = $discountDB->type;
            $discount->value = $discountDB->value;
            $discount->name = $discountDB->name;
            $discount->description = $discountDB->description;
            $discount->fromLastDiscount = $discountDB->from_last_discount;
            $discount->position = $discountDB->position;
            $discounts[] = $discount;
        }

        return ExtenderFacade::execute(__METHOD__, $discounts, func_get_args());
    }
}