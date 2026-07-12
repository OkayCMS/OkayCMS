<?php

namespace Okay\Helpers;

use Okay\Core\EntityFactory;
use Okay\Core\Routes\ProductRoute;
use Okay\Core\Settings;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\VariantsEntity;
use Okay\Entities\ImagesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Helpers\MetadataHelpers\ProductMetadataHelper;

/**
 * @phpstan-type ProductRow object{id: int|string, url: string, slug_url: string, name?: string, main_image_id?: int|string|null, brand_id?: int|string|null, main_category_id?: int|string|null, visible?: mixed, image?: ImageRow, images?: list<ImageRow>, variants?: array<int|string, VariantRow>, variant?: VariantRow, features?: array<int|string, FeatureRow>, annotation?: mixed, description?: mixed}&\stdClass
 * @phpstan-type VariantRow object{id: int|string, product_id: int|string, price: int|float|string, currency_id: int|string|null, compare_price?: int|float|string|null, name?: string|null, sku?: string|null}&\stdClass
 * @phpstan-type ImageRow object{product_id: int|string}&\stdClass
 * @phpstan-type FeatureRow object{values?: list<FeatureValueRow>, stingify_values?: string}&\stdClass
 * @phpstan-type FeatureValueRow object{id: int|string, feature_id: int|string, value: string}&\stdClass
 */
class ProductsHelper implements GetListInterface
{
    /** @var EntityFactory */
    private $entityFactory;

    /** @var MoneyHelper */
    private $moneyHelper;

    /** @var Settings */
    private $settings;

    /** @var ProductMetadataHelper */
    private $productMetadataHelper;

    /** @var CatalogHelper */
    private $catalogHelper;

    /** @var FilterHelper */
    private $filterHelper;


    /** @var FeaturesEntity */
    private $featuresEntity;

    /** @var CategoriesEntity */
    private $categoriesEntity;

    public function __construct(
        EntityFactory $entityFactory,
        MoneyHelper $moneyHelper,
        Settings $settings,
        ProductMetadataHelper $productMetadataHelper,
        CatalogHelper $catalogHelper,
        FilterHelper $filterHelper
    ) {
        $this->entityFactory         = $entityFactory;
        $this->moneyHelper           = $moneyHelper;
        $this->settings              = $settings;
        $this->productMetadataHelper = $productMetadataHelper;
        $this->catalogHelper         = $catalogHelper;
        $this->filterHelper          = $filterHelper;

        $this->featuresEntity   = $entityFactory->get(FeaturesEntity::class);
        $this->categoriesEntity = $entityFactory->get(CategoriesEntity::class);
    }

    /**
     * @param array<string, mixed> $productsFilter
     * @param array<int|string, object{id: int|string, features_values?: mixed}&\stdClass> $catalogFeatures
     */
    public function assignFilterProcedure(
        array $productsFilter,
        array $catalogFeatures,
        ?string $keyword = null
    ): void {
        if (isset($productsFilter['keyword'])) {
            $catalogCategories = $this->categoriesEntity->find(['product_keyword' => $productsFilter['keyword']]);
        } else {
            $catalogCategories = [];
        }

        $this->catalogHelper->assignCatalogDataProcedure(
            $productsFilter,
            $catalogFeatures,
            $catalogCategories,
            null,
            (int) $this->settings->get('features_max_count_products')
        );

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @return array<int, object{id: int|string, url: string, features_values?: array<int|string, object>}&\stdClass>
     */
    public function getCatalogFeatures(): array
    {
        return ExtenderFacade::execute(__METHOD__, $this->catalogHelper->getCatalogFeatures(), func_get_args());
    }

    /**
     * @param array<string, mixed> $filter
     */
    public function isFilterPage(array $filter): bool
    {
        return ExtenderFacade::execute(__METHOD__, $this->filterHelper->isFilterPage($filter), func_get_args());
    }

    /**
     * @param ProductRow|false $product
     * @return ProductRow|false
     */
    public function attachProductData($product)
    {
        if (empty($product->id)) {
            return ExtenderFacade::execute(__METHOD__, false);
        }
        $products[$product->id] = $product;

        $products = $this->attachVariants($products);
        $products = $this->attachImages($products);
        $products = $this->attachFeatures($products);

        return ExtenderFacade::execute(__METHOD__, reset($products), func_get_args());
    }

    /**
     * @inheritDoc
     * @param array<string, mixed> $filter
     * @param array<int, string>|false|null $excludedFields
     * @return array<int|string, ProductRow>
     */
    public function getList($filter = [], $sortName = null, $excludedFields = null)
    {
        if ($excludedFields === null) {
            $excludedFields = $this->getExcludeFields();
        }

        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);

        // Исключаем колонки, которые нам не нужны
        if (is_array($excludedFields) && !empty($excludedFields)) {
            $productsEntity->cols(ProductsEntity::getDifferentFields($excludedFields));
        }

        if (isset($filter['featured'])) {
            $productsEntity->addHighPriority('featured');
        }

        if ($this->settings->get('missing_products') === MISSING_PRODUCTS_HIDE) {
            $filter['in_stock'] = true;
        }

        $productsEntity->order($sortName, $this->getOrderProductsAdditionalData());

        $products = $productsEntity->mappedBy('id')->find($filter);
        /** @var array<int|string, ProductRow> $products */

        if (empty($products)) {
            return ExtenderFacade::execute(__METHOD__, [], func_get_args());
        }

        $products = $this->attachVariants($products);
        $products = $this->attachMainImages($products);

        // Укажем связки урла товара и его slug, чтобы уже были в оперативке
        foreach ($products as $p) {
            ProductRoute::setUrlSlugAlias($p->url, $p->slug_url);
        }

        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }

    public function getExcludeFields()
    {
        $excludedFields = [
            'description',
            'meta_title',
            'meta_keywords',
            'meta_description',
        ];
        return ExtenderFacade::execute(__METHOD__, $excludedFields, func_get_args());
    }

    // Данный метод остаётся для обратной совместимости, но объявлен как deprecated, и будет удалён в будущих версиях
    public function getProductList($filter = [], $sortProducts = null)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use getList', E_USER_DEPRECATED);
        $products = $this->getList($filter, $sortProducts, false);
        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }

    private function getOrderProductsAdditionalData()
    {
        $orderAdditionalData = [];

        if ($this->settings->get('missing_products') === MISSING_PRODUCTS_MOVE_END) {
            $orderAdditionalData['in_stock_first'] = true;
        }

        return ExtenderFacade::execute(__METHOD__, $orderAdditionalData, func_get_args());
    }

    /**
     * @param array<int|string, ProductRow> $products
     * @param array<string, mixed> $variantsFilter
     * @return array<int|string, ProductRow>
     */
    public function attachVariants(array $products, array $variantsFilter = [])
    {
        $obj = new \ArrayObject($products);
        $copyProducts = $obj->getArrayCopy();

        $productsIds = array_keys($copyProducts);

        $variantsFilter['product_id'] = $productsIds;

        /** @var VariantsEntity $variantsEntity */
        $variantsEntity = $this->entityFactory->get(VariantsEntity::class);
        /** @var list<VariantRow> $variants */
        $variants = $variantsEntity->order('in_stock_first')->find($variantsFilter);

        $variants = $this->moneyHelper->convertVariantsPriceToMainCurrency($variants);
        /** @var list<VariantRow> $variants */
        foreach ($variants as $variant) {
            $copyProducts[$variant->product_id]->variants[$variant->id] = $variant;
        }

        foreach ($copyProducts as $copyProduct) {
            if (!empty($copyProduct->variants)) {
                $copyProduct->variant = reset($copyProduct->variants);
            }
        }

        return ExtenderFacade::execute(__METHOD__, $copyProducts, func_get_args());
    }

    /**
     * Метод добавляет к списку товаров свойства и их значения
     *
     * @param array $products список товаров, к которому нужно добавить свойства
     * @param array $featuresFilter дополнительные параметры фильтрации по свойствам
     * @param array $featuresValuesFilter дополнительные параметры фильтрации по значениям свойств
     * @return array список товаров, к каждому товару добавлены его свойства в свойство объекта features (простите за тавтологию)
     * @throws \Exception
     */
    /**
     * @param array<int|string, ProductRow> $products
     * @param array<string, mixed> $featuresFilter
     * @param array<string, mixed> $featuresValuesFilter
     * @return array<int|string, ProductRow>
     */
    public function attachFeatures(array $products, array $featuresFilter = [], array $featuresValuesFilter = [])
    {
        /** @var FeaturesValuesEntity $featuresValuesEntity */
        $featuresValuesEntity = $this->entityFactory->get(FeaturesValuesEntity::class);

        /** @var FeaturesEntity $featuresEntity */
        $featuresEntity = $this->entityFactory->get(FeaturesEntity::class);

        $productsIds = array_keys($products);
        /** @var list<int> $productsIds */

        $featuresValuesFilter['product_id'] = $productsIds;
        $featuresValues = [];
        $features = [];

        if (isset($featuresFilter['id'])) {
            $featuresValuesFilter['feature_id'] = $featuresFilter['id'];
        }

        /** @var list<FeatureValueRow> $featureValuesRows */
        $featureValuesRows = $featuresValuesEntity->find($featuresValuesFilter);
        foreach ($featureValuesRows as $fv) {
            $featuresValues[$fv->feature_id][$fv->id] = $fv;
        }

        $featuresIds = array_keys($featuresValues);
        if (!empty($featuresFilter['id'])) {
            $featuresFilter['id'] = array_intersect($featuresIds, $featuresFilter['id']);
        } else {
            $featuresFilter['id'] = $featuresIds;
        }

        $featuresFilter['visible'] = true;
        $featuresFilter['show_in_product'] = 1;

        /** @var list<object{id: int|string}&\stdClass> $featureRows */
        $featureRows = $featuresEntity->find($featuresFilter);
        foreach ($featureRows as $f) {
            $features[$f->id] = $f;
        }

        $productsValuesIds = [];
        /** @var list<object{value_id: int|string, product_id: int|string}&\stdClass> $productValueRows */
        $productValueRows = $featuresValuesEntity->getProductValuesIds($productsIds);
        foreach ($productValueRows as $productValueId) {
            $productsValuesIds[$productValueId->value_id][] = $productValueId->product_id;
        }

        foreach ($features as $feature) {
            if (isset($featuresValues[$feature->id])) {
                foreach ($featuresValues[$feature->id] as $featureValue) {
                    if (isset($productsValuesIds[$featureValue->id])) {
                        foreach ($productsValuesIds[$featureValue->id] as $productId) {
                            if (!isset($products[$productId]->features[$featureValue->feature_id])) {
                                $products[$productId]->features[$featureValue->feature_id] = clone $features[$featureValue->feature_id];
                            }
                            $products[$productId]->features[$featureValue->feature_id]->values[] = $featureValue;
                        }
                    }
                }
            }
        }

        foreach ($products as $p) {
            if (!empty($p->features)) {
                foreach ($p->features as $feature) {
                    $values = [];
                    foreach ($feature->values as $featureValue) {
                        $values[] = $featureValue->value;
                    }

                    $feature->stingify_values = implode(',', $values);
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }

    /**
     * @param array<int|string, ProductRow> $products
     * @return array<int|string, ProductRow>
     */
    public function attachMainImages(array $products)
    {
        $obj = new \ArrayObject($products);
        $copyProducts = $obj->getArrayCopy();

        /** @var ImagesEntity $imagesEntity */
        $imagesEntity = $this->entityFactory->get(ImagesEntity::class);

        $imagesIds = [];
        foreach ($copyProducts as $copyProduct) {
            $imagesIds[] = $copyProduct->main_image_id;
        }

        if (empty($imagesIds)) {
            return ExtenderFacade::execute(__METHOD__, $copyProducts, func_get_args());
        }

        $images = $imagesEntity->find(['id' => $imagesIds]);
        /** @var list<ImageRow> $images */
        foreach ($images as $image) {
            $copyProducts[$image->product_id]->image = $image;
        }

        return ExtenderFacade::execute(__METHOD__, $copyProducts, func_get_args());
    }

    /**
     * @param array<int|string, ProductRow> $products
     * @return array<int|string, ProductRow>
     */
    public function attachImages(array $products)
    {
        $obj = new \ArrayObject($products);
        $copyProducts = $obj->getArrayCopy();

        $imagesEntity = $this->entityFactory->get(ImagesEntity::class);

        $productsIds = array_keys($copyProducts);

        if (empty($productsIds)) {
            return ExtenderFacade::execute(__METHOD__, $copyProducts, func_get_args());
        }

        $images = $imagesEntity->find(['product_id' => $productsIds]);
        /** @var list<ImageRow> $images */
        foreach ($images as $image) {
            $copyProducts[$image->product_id]->images[] = $image;
        }

        foreach ($copyProducts as $copyProduct) {
            if (!empty($copyProduct->images)) {
                $copyProduct->image = reset($copyProduct->images);
            }
        }
        return ExtenderFacade::execute(__METHOD__, $copyProducts, func_get_args());
    }

    /**
     * @param array<int|string, ProductRow> $products
     * @return array<int|string, ProductRow>
     * @throws \Exception
     */
    public function attachDescriptionByTemplate(array $products): array
    {
        if (!empty($products)) {
            /** @var CategoriesEntity $categoriesEntity */
            $categoriesEntity = $this->entityFactory->get(CategoriesEntity::class);

            $brandIds = array_reduce($products, function ($carry, $product) {
                if ($product->brand_id) {
                    $carry[] = $product->brand_id;
                }
                return $carry;
            }, []);

            if (!empty($brandIds)) {
                $brandsEntity = $this->entityFactory->get(BrandsEntity::class);
                $brands = $brandsEntity->find(['id' => $brandIds]);
            } else {
                $brands = [];
            }

            foreach ($products as $product) {
                $category = $categoriesEntity->findOne(['id' => $product->main_category_id]);
                /** @var object|null $category */
                $category = $category === false ? null : $category;

                $this->productMetadataHelper->setUp(
                    $product,
                    $category,
                    $brands[$product->brand_id] ?? null
                );

                if (isset($product->annotation)) {
                    $product->annotation = $this->productMetadataHelper->getAnnotation();
                }
                if (isset($product->description)) {
                    $product->description = $this->productMetadataHelper->getDescription();
                    ;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }

    /**
     * Метод проверяет доступность товара для показа в контроллере
     * можно переопределить логику работы контроллера и отменить дальнейшие действия
     * для этого после реализации другой логики необходимо вернуть true из экстендера
     *
     * @param object{visible: mixed}|false $product
     * @return mixed
     */
    public function setProduct($product)
    {
        if (empty($product) || (!$product->visible && empty($_SESSION['admin']))) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @param array<string, mixed> $filter
     * @return array<string, mixed>|null
     */
    public function getProductsFilter(?string $filtersUrl = null, array $filter = []): ?array
    {
        if (($filter = $this->catalogHelper->getProductsFilter($filtersUrl, $filter)) === null) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }
}
