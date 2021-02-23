<?php


namespace Okay\Core;


use Okay\Entities\UserBrowsedProductsEntity;
use Okay\Helpers\MainHelper;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Helpers\ProductsHelper;

class BrowsedProducts
{
    private $productsHelper;
    private $mainHelper;
    private $entityFactory;

    private $maxVisitedProducts = 100; // Максимальное число хранимых товаров в истории
    
    public function __construct(
        ProductsHelper $productsHelper,
        MainHelper $mainHelper,
        EntityFactory $entityFactory
    ) {
        $this->productsHelper = $productsHelper;
        $this->mainHelper = $mainHelper;
        $this->entityFactory = $entityFactory;
    }

    public function get($limit = null)
    {
        $browsedProducts = [];

        $browsedProductsIds = !empty($_COOKIE['browsed_products']) ? explode(',', $_COOKIE['browsed_products']) : [];
        $browsedProductsIds = array_reverse($browsedProductsIds);

        if (!empty($limit)) {
            $browsedProductsIds = array_slice($browsedProductsIds, 0, $limit);
        }
        
        if (empty($browsedProductsIds) || !is_array($browsedProductsIds)) {
            return ExtenderFacade::execute(__METHOD__, $browsedProducts, func_get_args());
        }

        $products = $this->productsHelper->getList(['id' => $browsedProductsIds]);

        foreach($browsedProductsIds as  $browsedProductId) {
            if (!empty($products[$browsedProductId])) {
                $browsedProducts[$browsedProductId] = $products[$browsedProductId];
            }
        }

        return ExtenderFacade::execute(__METHOD__, $browsedProducts, func_get_args());
    }

    public function addItem($productId, $onlyLocal = false, $delayedDispatch = false)
    {
        if (!empty($_COOKIE['browsed_products'])) {
            $browsedProducts = explode(',', $_COOKIE['browsed_products']);
            // Удалим текущий товар, если он был
            if (($exists = array_search($productId, $browsedProducts)) !== false) {
                unset($browsedProducts[$exists]);
            }
        }
        // Добавим текущий товар
        $browsedProducts[] = $productId;
        $cookieVal = implode(',', array_slice($browsedProducts, -$this->maxVisitedProducts, $this->maxVisitedProducts));
        $_COOKIE['browsed_products'] = $cookieVal;
        
        if ($delayedDispatch === false) {
            $this->save();
        }

        if ($onlyLocal === false && ($user = $this->mainHelper->getCurrentUser())) {
            /** @var UserBrowsedProductsEntity $userBrowsedProductsEntity */
            $userBrowsedProductsEntity = $this->entityFactory->get(UserBrowsedProductsEntity::class);

            if (!$userBrowsedProductsEntity->findOne(['user_id' => $user->id, 'product_id' => $productId])) {
                $userBrowsedProductsEntity->add([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                ]);
                $userBrowsedProductsEntity->sliceToLimit($user->id, $this->maxVisitedProducts);
            }
        }
        
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function save()
    {
        if (!empty($_COOKIE['browsed_products'])) {
            setcookie('browsed_products', $_COOKIE['browsed_products'], time() + 60 * 60 * 24 * 30, '/');
        }
    }
    
}
