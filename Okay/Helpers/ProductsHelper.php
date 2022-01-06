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
        EntityFactory         $entityFactory,
        MoneyHelper           $moneyHelper,
        Settings              $settings,
        ProductMetadataHelper $productMetadataHelper,
        CatalogHelper         $catalogHelper,
        FilterHelper          $filterHelper
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

    public function assignFilterProcedure(
        array   $productsFilter,
        array   $catalogFeatures,
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

    public function getCatalogFeatures(): array
    {
        return ExtenderFacade::execute(__METHOD__, $this->catalogHelper->getCatalogFeatures(), func_get_args());
    }

    public function isFilterPage(array $filter): bool
    {
        return ExtenderFacade::execute(__METHOD__, $this->filterHelper->isFilterPage($filter), func_get_args());
    }

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

    public function attachVariants(array $products, array $variantsFilter = [])
    {
        $obj = new \ArrayObject($products);
        $copyProducts = $obj->getArrayCopy();

        $productsIds = array_keys($copyProducts);

        $variantsFilter['product_id'] = $productsIds;
        
        /** @var VariantsEntity $variantsEntity */
        $variantsEntity = $this->entityFactory->get(VariantsEntity::class);
        $variants = $variantsEntity->order('in_stock_first')->find($variantsFilter);

        $variants = $this->moneyHelper->convertVariantsPriceToMainCurrency($variants);
        foreach ($variants as $variant) {
            $copyProducts[$variant->product_id]->variants[$variant->id] = $variant;
        }

        foreach ($copyProducts as $copyProduct) {
            if (!empty($copyProduct->variants) && count($copyProduct->variants) > 0) {
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
    public function attachFeatures(array $products, array $featuresFilter = [], array $featuresValuesFilter = [])
    {
        /** @var FeaturesValuesEntity $featuresValuesEntity */
        $featuresValuesEntity = $this->entityFactory->get(FeaturesValuesEntity::class);
        
        /** @var FeaturesEntity $featuresEntity */
        $featuresEntity = $this->entityFactory->get(FeaturesEntity::class);
        
        $productsIds = array_keys($products);

        $featuresValuesFilter['product_id'] = $productsIds;
        $featuresValues = [];
        $features = [];
        
        if (isset($featuresFilter['id'])) {
            $featuresValuesFilter['feature_id'] = $featuresFilter['id'];
        }
        
        foreach ($featuresValuesEntity->find($featuresValuesFilter) as $fv) {
            $featuresValues[$fv->feature_id][$fv->id] = $fv;
        }

        $featuresIds = array_keys($featuresValues);
        if (!empty($featuresFilter['id'])) {
            $featuresFilter['id'] = array_intersect($featuresIds, $featuresFilter['id']);
        } else {
            $featuresFilter['id'] = $featuresIds;
        }

        $featuresFilter['visible'] = true;
        
        foreach ($featuresEntity->find($featuresFilter) as $f) {
            $features[$f->id] = $f;
        }

        $productsValuesIds = [];
        foreach ($featuresValuesEntity->getProductValuesIds($productsIds) as $productValueId) {
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
        foreach ($images as $image) {
            $copyProducts[$image->product_id]->image = $image;
        }

        return ExtenderFacade::execute(__METHOD__, $copyProducts, func_get_args());
    }

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
        foreach ($images as $image) {
            $copyProducts[$image->product_id]->images[] = $image;
        }

        foreach ($copyProducts as $copyProduct) {
            if (!empty($copyProduct->images) && count($copyProduct->images) > 0) {
                $copyProduct->image = reset($copyProduct->images);
            }
        }
        return ExtenderFacade::execute(__METHOD__, $copyProducts, func_get_args());
    }

    /**
     * @param array $products
     * @return array
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
                $this->productMetadataHelper->setUp(
                    $product,
                    $categoriesEntity->findOne(['id' => $product->main_category_id]),
                    $brands[$product->brand_id] ?? null
                );

                if (isset($product->annotation)) {
                    $product->annotation = $this->productMetadataHelper->getAnnotation();
                }
                if (isset($product->description)) {
                    $product->description = $this->productMetadataHelper->getDescription();;
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
     * @param object $product
     * @return object
     */
    public function setProduct($product)
    {
        if (empty($product) || (!$product->visible && empty($_SESSION['admin']))) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getProductsFilter(?string $filtersUrl = null, array $filter = []): ?array
    {
        if (($filter = $this->catalogHelper->getProductsFilter($filtersUrl, $filter)) === null) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }
}