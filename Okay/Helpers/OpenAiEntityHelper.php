<?php

namespace Okay\Helpers;

use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Money;
use Okay\Core\Router;
use Okay\Core\ServiceLocator;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Helpers\AiRequests\AbstractAiRequest;
use Okay\Helpers\AiRequests\AiBrandRequest;
use Okay\Helpers\AiRequests\AiCategoryRequest;
use Okay\Helpers\AiRequests\AiProductRequest;
use Okay\Helpers\ProductsHelper;

class OpenAiEntityHelper
{
    public const ENTITY_TYPE_PRODUCT    = 'product';
    public const ENTITY_TYPE_CATEGORY   = 'category';
    public const ENTITY_TYPE_BRAND      = 'brand';

    public $serviceLocator;

    public function getRequest(string $entity, ?int $entityId, ?string $name): ?AbstractAiRequest
    {
        if (empty($entity) || empty($entityId)) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        }

        $this->serviceLocator = ServiceLocator::getInstance();

        if ($entity == self::ENTITY_TYPE_PRODUCT) {
            list($parts, $additionalInfoData) = $this->getProductParts($entityId);

            return new AiProductRequest($entityId, $parts, $name, $additionalInfoData);
        }
        elseif ($entity == self::ENTITY_TYPE_CATEGORY) {
            list($parts, $additionalInfoData) = $this->getCategoryParts($entityId);

            return new AiCategoryRequest($entityId, $parts, $name, $additionalInfoData);
        }
        elseif ($entity == self::ENTITY_TYPE_BRAND) {
            list($parts, $additionalInfoData) = $this->getBrandParts($entityId);

            return new AiBrandRequest($entityId, $parts, $name, $additionalInfoData);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function compileMetadata($pattern, $getParts)
    {
        $data = strtr($pattern, $getParts);
        $data = trim(preg_replace('/{\$[^$]*}/', '', $data));

        return ExtenderFacade::execute([static::class, __FUNCTION__], $data, func_get_args());
    }

    protected function getProductParts($productId): array
    {
        /** @var EntityFactory $entityFactory */
        $entityFactory = $this->serviceLocator->getService(EntityFactory::class);

        /* Money $money */
        $money = $this->serviceLocator->getService(Money::class);

        /* ProductsHelper $productsHelper */
        $productsHelper = $this->serviceLocator->getService(ProductsHelper::class);

        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $entityFactory->get(BrandsEntity::class);

        /** @var CategoriesEntity $categoriesEntity */
        $categoriesEntity = $entityFactory->get(CategoriesEntity::class);

        $currency = $this->getCurrentCurrency();

        $brandRoute = '';
        $product = $productsHelper->getList(['id' => $productId]);

        if (!empty($product = reset($product))
            && !empty($product->brand_id)
            && !empty ($brand = $brandsEntity->findOne(['id' => $product->brand_id]))
            && !empty ($brand->url)
        ) {
            $brandRoute = ltrim(Router::generateUrl('brand', ['url' => $brand->url]), '/');
        }

        $parts = [
            '{$brand}'         => ((!empty($brand) && !empty($brand->name)) ? $brand->name : ''),
            '{$brand_h1}'      => ((!empty($brand) && !empty($brand->name_h1)) ? $brand->name_h1 : ''),
            '{$brand_url}'     => $brandRoute,
            '{$product}'       => (!empty($product) ? $product->name : ''),
            '{$price}'         => (($product->variant->price != null && !empty($currency)) ? $money->convert($product->variant->price, $currency->id, false) . ' ' . $currency->sign : ''),
            '{$compare_price}' => (($product->variant->compare_price != null && !empty($currency)) ? $money->convert($product->variant->compare_price, $currency->id, false) . ' ' . $currency->sign : ''),
            '{$sku}'           => ($product->variant->sku != null ? $product->variant->sku : ''),
        ];

        if (!empty($product)
            && !empty($product->main_category_id)
        ) {
            $category = $categoriesEntity->findOne(['id' => $product->main_category_id]);
        }

        $parts['{$category}'] = '';
        $parts['{$category_h1}'] = '';

        if (!empty($category)) {
            $parts['{$category}'] = ($category->name ? $category->name : '');
            $parts['{$category_h1}'] = ($category->name_h1 ? $category->name_h1 : '');
        }

        $additionalInfo = $this->getProductAdditionalInfo($product);

        return $parts = ExtenderFacade::execute(__METHOD__, [$parts, $additionalInfo], func_get_args());
    }

    protected function getProductAdditionalInfo($product): array
    {
        $entityFactory = $this->serviceLocator->getService(EntityFactory::class);
        $featuresValuesEntity = $entityFactory->get(FeaturesValuesEntity::class);
        $featuresEntity = $entityFactory->get(FeaturesEntity::class);

        $featuresValues = [];
        foreach ($featuresValuesEntity->find(['product_id' => $product->id]) as $fv) {
            $featuresValues[$fv->feature_id][$fv->id] = $fv;
        }
        $featuresIds = array_keys($featuresValues);
        if (!empty($featuresIds)) {
            $additionalInfo = [];
            foreach ($featuresEntity->find(['id' => $featuresIds]) as $f) {
                if (!empty($featuresValues[$f->id])) {
                    $values = [];
                    foreach ($featuresValues[$f->id] as $fv) {
                        $values[] = $fv->value;
                    }
                    $additionalInfo[] = sprintf(
                        '%s: %s',
                        $f->name,
                        implode(', ', $values)
                    );
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $additionalInfo, func_get_args());
    }

    /**
     * Метод возвращает валюту
     * @return array
     */
    protected function getCurrentCurrency(): object {

        $currentCurrency = null;

        /** @var EntityFactory $entityFactory */
        $entityFactory = $this->serviceLocator->getService(EntityFactory::class);

        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $entityFactory->get(CurrenciesEntity::class);

        $allCurrencies = $currenciesEntity->find([]);

        // Берем валюту из сессии
        if (isset($_SESSION['currency_id'])) {
            $currentCurrency = $currenciesEntity->get((int)$_SESSION['currency_id']);
        } elseif (!empty($allCurrencies)) {
            $currentCurrency = reset($allCurrencies);
        }

        return $currentCurrency;
    }

    protected function getCategoryParts($categoryId): array
    {
        if (empty($categoryId)) {
            return null;
        }

        /** @var EntityFactory $entityFactory */
        $entityFactory = $this->serviceLocator->getService(EntityFactory::class);

        /** @var BrandsEntity $brandsEntity */
        $categoriesEntity = $entityFactory->get(CategoriesEntity::class);

        $category = $categoriesEntity->findOne(['id' => $categoryId]);

        if (empty($category)) {
            return null;
        }

        $parts = [
            '{$category}'    => (!empty($category->name) ? $category->name : ''),
            '{$category_h1}' => (!empty($category->name_h1) ? $category->name_h1 : ''),
            '{$category_url}' => ltrim(Router::generateUrl('category', ['url' => $category->url]), '/'),
        ];

        $additionalInfo = $this->getCategoryAdditionalInfo($category);

        return $parts = ExtenderFacade::execute(__METHOD__, [$parts, $additionalInfo], func_get_args());
    }

    protected function getCategoryAdditionalInfo($category): array
    {
        if (empty($category)) {
            return [];
        }

        $additionalInfo = [];
        $entityFactory = $this->serviceLocator->getService(EntityFactory::class);

        /* CategoriesEntity $categoriesEntity */
        $categoriesEntity = $entityFactory->get(CategoriesEntity::class);

        if (!empty($category)
            && !empty($category->parent_id)
            && !empty($parentCategory = $categoriesEntity->cols(['id', 'name'])->findOne(['id' => $category->parent_id]))
            && !empty($parentCategory->name)
        ) {
            $additionalInfo[] = $category->name;
            $additionalInfo[] = $parentCategory->name;
        }

        return ExtenderFacade::execute(__METHOD__, $additionalInfo, func_get_args());
    }

    protected function getBrandParts($brandId): array
    {
        if (empty($brandId)) {
            return null;
        }

        /** @var EntityFactory $entityFactory */
        $entityFactory = $this->serviceLocator->getService(EntityFactory::class);

        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $entityFactory->get(BrandsEntity::class);

        $brand = $brandsEntity->findOne(['id' => $brandId]);

        if (empty($brand)) {
            return null;
        }

        $parts = [
            '{$brand}'    => (!empty($brand->name) ? $brand->name : ''),
            '{$brand_h1}' => (!empty($brand->name_h1) ? $brand->name_h1 : ''),
            '{$brand_url}' => ltrim(Router::generateUrl('brand', ['url' => $brand->url]), '/'),
        ];

        $additionalInfo = $this->getBrandAdditionalInfo($brand);

        return $parts = ExtenderFacade::execute(__METHOD__, [$parts, $additionalInfo], func_get_args());
    }

    protected function getBrandAdditionalInfo($brand): array
    {
        $additionalInfo = [];

        if (!empty($brand->name)) {
            $additionalInfo[] = $brand->name;
        }

        return ExtenderFacade::execute(__METHOD__, $additionalInfo, func_get_args());
    }

}