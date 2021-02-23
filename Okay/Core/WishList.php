<?php


namespace Okay\Core;


use Okay\Entities\UserWishlistItemsEntity;
use Okay\Entities\VariantsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\ImagesEntity;
use Okay\Helpers\MainHelper;
use Okay\Helpers\MoneyHelper;
use Okay\Core\Modules\Extender\ExtenderFacade;

class WishList
{
    /** @var ProductsEntity */
    private $productsEntity;

    /** @var VariantsEntity */
    private $variantsEntity;

    /** @var ImagesEntity */
    private $imagesEntity;

    /** @var MoneyHelper */
    private $moneyHelper;

    private $entityFactory;

    private $settings;
    
    private $mainHelper;

    public function __construct(
        EntityFactory $entityFactory,
        Settings $settings,
        MoneyHelper $moneyHelper,
        MainHelper $mainHelper
    ) {
        $this->productsEntity = $entityFactory->get(ProductsEntity::class);
        $this->variantsEntity = $entityFactory->get(VariantsEntity::class);
        $this->imagesEntity   = $entityFactory->get(ImagesEntity::class);
        $this->entityFactory  = $entityFactory;
        $this->settings       = $settings;
        $this->moneyHelper    = $moneyHelper;
        $this->mainHelper     = $mainHelper;
    }

    public function get()
    {
        $wishList = new \stdClass();
        $wishList->products = [];
        $wishList->ids = [];

        $items = !empty($_COOKIE['wishlist']) ? json_decode($_COOKIE['wishlist']) : [];
        if (empty($items) || !is_array($items)) {
            return ExtenderFacade::execute(__METHOD__, $wishList, func_get_args());
        }

        $products = [];
        $images_ids = [];
        foreach ($this->productsEntity->find(['id'=>$items, 'visible'=>1]) as $p) {
            $products[$p->id] = $p;
            $images_ids[] = $p->main_image_id;
        }

        if (empty($products)) {
            return ExtenderFacade::execute(__METHOD__, $wishList, func_get_args());
        }

        $products_ids = array_keys($products);
        $wishList->ids = $products_ids;
        foreach($products as $product) {
            $product->variants = [];
        }

        $variants = $this->variantsEntity->find(['product_id'=>$products_ids]);
        foreach($variants as $variant) {
            $products[$variant->product_id]->variants[] = $this->moneyHelper->convertVariantPriceToMainCurrency($variant);;
        }

        if (!empty($images_ids)) {
            $images = $this->imagesEntity->find(['id'=>$images_ids]);
            foreach ($images as $image) {
                if (isset($products[$image->product_id])) {
                    $products[$image->product_id]->image = $image;
                }
            }
        }

        foreach($products as $product) {
            if(isset($product->variants[0])) {
                $product->variant = $product->variants[0];
            }
        }

        $wishList->products = $products;
        return ExtenderFacade::execute(__METHOD__, $wishList, func_get_args());
    }

    public function addItem($productId, $onlyLocal = false, $delayedDispatch = false)
    {
        $items = !empty($_COOKIE['wishlist']) ? json_decode($_COOKIE['wishlist']) : [];
        $items = $items && is_array($items) ? $items : [];
        if (!in_array($productId, $items)) {
            $items[] = $productId;
        }
        $_COOKIE['wishlist'] = json_encode($items);
        if ($delayedDispatch === false) {
            $this->save();
        }

        if ($onlyLocal === false && ($user = $this->mainHelper->getCurrentUser())) {
            /** @var UserWishlistItemsEntity $userWishlistItemsEntity */
            $userWishlistItemsEntity = $this->entityFactory->get(UserWishlistItemsEntity::class);

            if (!$userWishlistItemsEntity->findOne(['user_id' => $user->id, 'product_id' => $productId])) {
                $userWishlistItemsEntity->add([
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
        $items = !empty($_COOKIE['wishlist']) ? json_decode($_COOKIE['wishlist']) : [];
        if (!is_array($items)) {
            return;
        }
        $i = array_search($productId, $items);
        if ($i !== false) {
            unset($items[$i]);
        }
        $items = array_values($items);
        $_COOKIE['wishlist'] = json_encode($items);
        
        if ($delayedDispatch === false) {
            $this->save();
        }

        if ($onlyLocal === false && ($user = $this->mainHelper->getCurrentUser())) {
            /** @var UserWishlistItemsEntity $userWishlistItemsEntity */
            $userWishlistItemsEntity = $this->entityFactory->get(UserWishlistItemsEntity::class);

            $userWishlistItemsEntity->deleteByProductId($user->id, $productId);
        }
        
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    public function save()
    {
        if (!empty($_COOKIE['wishlist'])) {
            setcookie('wishlist', $_COOKIE['wishlist'], time() + 30 * 24 * 3600, '/');
        }
    }
    
    /*Очистка списка сравнения*/
    public function emptyWishList($onlyLocal = false)
    {

        if ($onlyLocal === false) {
            if ($user = $this->mainHelper->getCurrentUser()) {
                /** @var UserWishlistItemsEntity $userWishlistItemsEntity */
                $userWishlistItemsEntity = $this->entityFactory->get(UserWishlistItemsEntity::class);

                $userWishlistItemsEntity->deleteByProductId($user->id, array_keys(json_decode($_COOKIE['wishlist'])));
            }
        }
        
        unset($_COOKIE['wishlist']);
        setcookie('wishlist', '', time()-3600, '/');

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}
