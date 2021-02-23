<?php


namespace Okay\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Routes\ProductRoute;
use Okay\Core\Settings;
use Okay\Entities\ProductsEntity;
use Okay\Entities\UserBrowsedProductsEntity;
use Okay\Entities\VariantsEntity;
use Okay\Entities\ImagesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class ProductsHelper implements GetListInterface
{
    private $entityFactory;
    private $moneyHelper;
    private $settings;
    private $mainHelper;

    public function __construct(
        EntityFactory $entityFactory,
        MoneyHelper $moneyHelper,
        Settings $settings,
        MainHelper $mainHelper
    ) {
        $this->entityFactory = $entityFactory;
        $this->moneyHelper = $moneyHelper;
        $this->settings = $settings;
        $this->mainHelper = $mainHelper;
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

}