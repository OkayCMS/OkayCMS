<?php


namespace Okay\Core;


use Okay\Entities\FeaturesValuesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\UserComparisonItemsEntity;
use Okay\Entities\VariantsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\ImagesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Helpers\MainHelper;
use Okay\Helpers\MoneyHelper;
use Okay\Helpers\ProductsHelper;

class Comparison
{
    /** @var ProductsEntity */
    private $productsEntity;

    /** @var VariantsEntity */
    private $variantsEntity;

    /** @var ImagesEntity */
    private $imagesEntity;

    /** @var FeaturesValuesEntity */
    private $featuresValuesEntity;

    /** @var FeaturesEntity */
    private $featuresEntity;
    
    private $settings;

    /**
     * @var MoneyHelper
     */
    private $moneyHelper;
    private $entityFactory;
    private $mainHelper;

    public function __construct(
        EntityFactory $entityFactory,
        Settings      $settings,
        MoneyHelper   $moneyHelper,
        MainHelper    $mainHelper
    ){
        $this->productsEntity         = $entityFactory->get(ProductsEntity::class);
        $this->variantsEntity         = $entityFactory->get(VariantsEntity::class);
        $this->imagesEntity           = $entityFactory->get(ImagesEntity::class);
        $this->featuresEntity         = $entityFactory->get(FeaturesEntity::class);
        $this->featuresValuesEntity   = $entityFactory->get(FeaturesValuesEntity::class);
        $this->entityFactory          = $entityFactory;
        $this->settings               = $settings;
        $this->moneyHelper            = $moneyHelper;
        $this->mainHelper             = $mainHelper;
    }

    public function get()
    {
        $comparison = new \stdClass();
        $comparison->products = [];
        $comparison->features = [];
        $comparison->ids = [];

        $items = !empty($_COOKIE['comparison']) ? json_decode($_COOKIE['comparison']) : [];
        if (!empty($items) && is_array($items)) {
            $products = [];
            $images_ids = [];
            foreach ($this->productsEntity->find(['id'=>$items, 'visible'=>1]) as $p) {
                $products[$p->id] = $p;
                $images_ids[] = $p->main_image_id;
            }
            if (!empty($products)) {
                $products_ids = array_keys($products);
                $comparison->ids = $products_ids;
                foreach ($products as $product) {
                    $product->variants = [];
                    $product->features = [];
                }

                $variants = $this->variantsEntity->find(['product_id'=>$products_ids]);
                $variants = $this->moneyHelper->convertVariantsPriceToMainCurrency($variants);

                foreach ($variants as $variant) {
                    $products[$variant->product_id]->variants[] = $variant;
                }

                if (!empty($images_ids)) {
                    $images = $this->imagesEntity->find(['id'=>$images_ids]);
                    foreach ($images as $image) {
                        if (isset($products[$image->product_id])) {
                            $products[$image->product_id]->image = $image;
                        }
                    }
                }

                if ($featuresValues = $this->featuresValuesEntity->mappedBy('id')->find(['product_id'=>$products_ids])) {
                    $productsValues = [];
                    foreach ($this->featuresValuesEntity->getProductValuesIds($products_ids) as $pv) {
                        $productsValues[$pv->product_id][$pv->value_id] = $pv->value_id;
                    }
                    
                    foreach ($featuresValues as $fv) {
                        $featuresIds[] = $fv->feature_id;
                    }
                }
                
                if (!empty($featuresIds)) {
                    $features = $this->featuresEntity->mappedBy('id')->find(['id' => $featuresIds]);
                    foreach ($featuresValues as $fv) {
                        if (isset($features[$fv->feature_id])) {
                            $features[$fv->feature_id]->value = $fv->value;
                        }

                        foreach ($products as $p) {
                            if (isset($productsValues[$p->id][$fv->id])) {
                                $features[$fv->feature_id]->products[$p->id][] = $fv->value;
                            } else {
                                $features[$fv->feature_id]->products[$p->id] = null;
                            }
                        }
                    }

                    foreach ($featuresValues as $fv) {
                        foreach ($products as $p) {
                            if (is_array($features[$fv->feature_id]->products[$p->id])){
                                $features[$fv->feature_id]->products[$p->id] = implode(", ", $features[$fv->feature_id]->products[$p->id]);
                            }
                        }
                        $features[$fv->feature_id]->not_unique = (count(array_unique($features[$fv->feature_id]->products)) == 1);
                    }
    
                    if (!empty($features)) {
                        $comparison->features = $features;
                    }
                }

                foreach ($products as $product) {
                    if (isset($product->variants[0])) {
                        $product->variant = $product->variants[0];
                    }

                    $productFeatures = [];
                    if (isset($productsValues[$product->id])) {
                        foreach ($productsValues[$product->id] as $valueId) {
                            if ($featureValue = $featuresValues[$valueId]) {
                                $productFeatures[$featureValue->feature_id][] = $featureValue->value;
                            }
                        }
                    }
                    
                    if (!empty($features)) {
                        foreach ($features as $f) {
                            if (isset($productFeatures[$f->id])) {
                                $product->features[$f->id] = implode(", ", $productFeatures[$f->id]);
                            } else {
                                $product->features[$f->id] = null;
                            }
                        }
                    }
                }
                $comparison->products = $products;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $comparison,func_get_args());
    }

    public function addItem($productId, $onlyLocal = false, $delayedDispatch = false)
    {
        $items = !empty($_COOKIE['comparison']) ? json_decode($_COOKIE['comparison']) : [];
        $items = $items && is_array($items) ? $items : [];
        if (!in_array($productId, $items)) {
            $items[] = $productId;
            if ($this->settings->get('comparison_count') && $this->settings->get('comparison_count') < count($items)) {
                array_shift($items);
            }
        }
        $_COOKIE['comparison'] = json_encode($items);
        if ($delayedDispatch === false) {
            $this->save();
        }

        if ($onlyLocal === false && ($user = $this->mainHelper->getCurrentUser())) {
            /** @var UserComparisonItemsEntity $userComparisonItemsEntity */
            $userComparisonItemsEntity = $this->entityFactory->get(UserComparisonItemsEntity::class);

            if (!$userComparisonItemsEntity->findOne(['user_id' => $user->id, 'product_id' => $productId])) {
                $userComparisonItemsEntity->add([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                ]);
            }
        }
        
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /*Удаление товара из корзины*/
    public function deleteItem($productId, $onlyLocal = false, $delayedDispatch = false)
    {
        $items = !empty($_COOKIE['comparison']) ? json_decode($_COOKIE['comparison']) : [];
        if (!is_array($items)) {
            ExtenderFacade::execute(__METHOD__, null, func_get_args());
            return;
        }
        $i = array_search($productId, $items);
        if ($i !== false) {
            unset($items[$i]);
        }
        $items = array_values($items);
        $_COOKIE['comparison'] = json_encode($items);
        if ($delayedDispatch === false) {
            $this->save();
        }

        if ($onlyLocal === false && ($user = $this->mainHelper->getCurrentUser())) {
            /** @var UserComparisonItemsEntity $userComparisonItemsEntity */
            $userComparisonItemsEntity = $this->entityFactory->get(UserComparisonItemsEntity::class);

            $userComparisonItemsEntity->deleteByProductId($user->id, $productId);
        }
        
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function save()
    {
        if (!empty($_COOKIE['comparison'])) {
            setcookie('comparison', $_COOKIE['comparison'], time() + 30 * 24 * 3600, '/');
        }
    }
    
    /*Очистка списка сравнения*/
    public function emptyComparison($onlyLocal = false)
    {

        if ($onlyLocal === false) {
            if ($user = $this->mainHelper->getCurrentUser()) {
                /** @var UserComparisonItemsEntity $userComparisonItemsEntity */
                $userComparisonItemsEntity = $this->entityFactory->get(UserComparisonItemsEntity::class);

                $userComparisonItemsEntity->deleteByProductId($user->id, array_keys(json_decode($_COOKIE['comparison'])));
            }
        }
        
        unset($_COOKIE['comparison']);
        setcookie('comparison', '', time()-3600, '/');

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}
