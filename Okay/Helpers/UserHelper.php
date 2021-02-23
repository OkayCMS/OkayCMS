<?php


namespace Okay\Helpers;


use Okay\Core\BrowsedProducts;
use Okay\Core\Cart;
use Okay\Core\Comparison;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\WishList;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\UserBrowsedProductsEntity;
use Okay\Entities\UserCartItemsEntity;
use Okay\Entities\UserComparisonItemsEntity;
use Okay\Entities\UsersEntity;
use Okay\Entities\UserWishlistItemsEntity;

class UserHelper
{

    private $entityFactory;
    private $cart;
    private $wishList;
    private $comparison;
    private $browsedProducts;

    public function __construct(
        EntityFactory $entityFactory,
        Cart $cart,
        WishList $wishList,
        Comparison $comparison,
        BrowsedProducts $browsedProducts
    ) {
        $this->entityFactory = $entityFactory;
        $this->cart = $cart;
        $this->wishList = $wishList;
        $this->comparison = $comparison;
        $this->browsedProducts = $browsedProducts;
    }
    
    public function getPaymentMethodsListForUser()
    {
        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);
        $payments = $paymentsEntity->mappedBy('id')->find(['enabled'=>1]);
        return ExtenderFacade::execute(__METHOD__, $payments, func_get_args());
    }
    
    public function getDeliveriesListForUser($payments)
    {
        /** @var DeliveriesEntity $deliveriesEntity */
        $deliveriesEntity = $this->entityFactory->get(DeliveriesEntity::class);
        $deliveries = $deliveriesEntity->mappedBy('id')->find(['enabled'=>1]);
        
        foreach ($deliveries as $delivery) {
            $delivery->payment_methods_ids = array_intersect(array_keys($payments), $deliveriesEntity->getDeliveryPayments($delivery->id));
        }
        return ExtenderFacade::execute(__METHOD__, $deliveries, func_get_args());
    }
    
    /**
     * Метод вызывается во время входа в личный кабинет.
     * 
     * @param string $email
     * @param string $password
     * @return int|false id пользователя или false при возникновении ошибки
     * @throws \Exception
     */
    public function login($email, $password)
    {
        /** @var UsersEntity $usersEntity */
        $usersEntity = $this->entityFactory->get(UsersEntity::class);
        if ($userId = $usersEntity->checkPassword($email, $password)) {
            $_SESSION['user_id'] = $userId;
            $usersEntity->update($userId, ['last_ip'=>$_SERVER['REMOTE_ADDR']]);
            
            $this->mergeCart();
            $this->mergeWishlist();
            $this->mergeComparison();
            $this->mergeBrowsedProducts();
            
        }
        return ExtenderFacade::execute(__METHOD__, $userId, func_get_args());
    }
    
    public function logout()
    {
        unset($_SESSION['user_id']);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    public function register($user)
    {
        /** @var UsersEntity $usersEntity */
        $usersEntity = $this->entityFactory->get(UsersEntity::class);

        $user->last_ip  = $_SERVER['REMOTE_ADDR'];
        if ($userId = $usersEntity->add($user)) {
            $_SESSION['user_id'] = $userId;
            
            $this->mergeCart();
            $this->mergeWishlist();
            $this->mergeComparison();
            $this->mergeBrowsedProducts();
            
        }
        return ExtenderFacade::execute(__METHOD__, $userId, func_get_args());
    }

    /**
     * Метод объединяет корзины (которая в базе и которая в сессии)
     * 
     * @var bool $onlyToLocal Объединять только с базы в локальное хранилище
     * @return array
     * @throws \Exception
     */
    public function mergeCart($onlyToLocal = false)
    {
        if (empty($_SESSION['user_id'])) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }
        
        /** @var UserCartItemsEntity $userCartItemsEntity */
        $userCartItemsEntity = $this->entityFactory->get(UserCartItemsEntity::class);
        
        $itemsInDb = $userCartItemsEntity->mappedBy('variant_id')->find(['user_id' => $_SESSION['user_id']]);
        
        if ($onlyToLocal === false) {
            // сохраняем текущие товары с сессии в базу
            if (!empty($_SESSION['shopping_cart'])) {
                foreach ($_SESSION['shopping_cart'] as $variantId => $amount) {
                    if (isset($itemsInDb[$variantId])) {
                        $amount = max($amount, $itemsInDb[$variantId]->amount);
                    }

                    $userCartItemsEntity->updateAmount($_SESSION['user_id'], $variantId, $amount);
                }
            }
        }

        if ($onlyToLocal === true) {
            unset($_SESSION['shopping_cart']);
        }

        // экстендер ставим здесь, чтобы до мержа модули могли корректировать содержимое корзины
        $itemsInDb = ExtenderFacade::execute(__METHOD__, $itemsInDb, func_get_args());

        // Объединяем товары с базы в сессию
        $purchaseVariants = [];
        if (!empty($itemsInDb)) {
            foreach ($itemsInDb as $item) {
                $purchaseVariants[$item->variant_id] = $item->amount;
            }
            $_SESSION['shopping_cart'] = $purchaseVariants;
        }
        $this->cart->getPurchases($purchaseVariants);

        return $itemsInDb; // no ExtenderFacade
    }

    /**
     * Метод объединяет списки избранного (который в базе и который в куках)
     * 
     * @var bool $onlyToLocal Объединять только с базы в локальное хранилище
     * @return array
     * @throws \Exception
     */
    public function mergeWishlist($onlyToLocal = false)
    {
        
        if (empty($_SESSION['user_id'])) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }
        
        /** @var UserWishlistItemsEntity $userWishlistItemsEntity */
        $userWishlistItemsEntity = $this->entityFactory->get(UserWishlistItemsEntity::class);
        
        $itemsInDb = $userWishlistItemsEntity->mappedBy('product_id')->find(['user_id' => $_SESSION['user_id']]);

        if ($onlyToLocal === false) {
            // сохраняем текущие товары с куков в базу
            foreach ($this->wishList->get()->products as $product) {
                if (!isset($itemsInDb[$product->id])) {
                    $userWishlistItemsEntity->add([
                        'user_id' => $_SESSION['user_id'],
                        'product_id' => $product->id,
                    ]);
                }
            }
        }

        if ($onlyToLocal === true) {
            $this->wishList->emptyWishList(true);
        }

        // экстендер ставим здесь, чтобы до мержа модули могли корректировать содержимое избранного
        $itemsInDb = ExtenderFacade::execute(__METHOD__, $itemsInDb, func_get_args());
        
        // Объединяем товары с базы в куки
        foreach ($itemsInDb as $item) {
            $this->wishList->addItem($item->product_id, true, true);
        }
        $this->wishList->save();
        
        return $itemsInDb; // no ExtenderFacade
    }

    /**
     * Метод объединяет списки сравнения (который в базе и который в куках)
     * 
     * @var bool $onlyToLocal Объединять только с базы в локальное хранилище
     * @return array
     * @throws \Exception
     */
    public function mergeComparison($onlyToLocal = false)
    {
        
        if (empty($_SESSION['user_id'])) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }
        
        /** @var UserComparisonItemsEntity $userComparisonItemsEntity */
        $userComparisonItemsEntity = $this->entityFactory->get(UserComparisonItemsEntity::class);
        
        $itemsInDb = $userComparisonItemsEntity->mappedBy('product_id')->find(['user_id' => $_SESSION['user_id']]);

        if ($onlyToLocal === false) {
            // сохраняем текущие товары с куков в базу
            foreach ($this->comparison->get()->products as $product) {
                if (!isset($itemsInDb[$product->id])) {
                    $userComparisonItemsEntity->add([
                        'user_id' => $_SESSION['user_id'],
                        'product_id' => $product->id,
                    ]);
                }
            }
        }

        if ($onlyToLocal === true) {
            $this->comparison->emptyComparison(true);
        }
        
        // экстендер ставим здесь, чтобы до мержа модули могли корректировать содержимое сравнения
        $itemsInDb = ExtenderFacade::execute(__METHOD__, $itemsInDb, func_get_args());
        
        // Объединяем товары с базы в куки
        foreach ($itemsInDb as $item) {
            $this->comparison->addItem($item->product_id, true, true);
        }
        $this->comparison->save();
        
        return $itemsInDb; // no ExtenderFacade
    }

    /**
     * Метод объединяет списки просмотренных товаров (который в базе и который в куках)
     * 
     * @var bool $onlyToLocal Объединять только с базы в локальное хранилище
     * @return array
     * @throws \Exception
     */
    public function mergeBrowsedProducts($onlyToLocal = false)
    {
        
        if (empty($_SESSION['user_id'])) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }
        
        /** @var UserBrowsedProductsEntity $userBrowsedProductsEntity */
        $userBrowsedProductsEntity = $this->entityFactory->get(UserBrowsedProductsEntity::class);
        
        $itemsInDb = $userBrowsedProductsEntity->mappedBy('product_id')->order('id_desc')->find(['user_id' => $_SESSION['user_id']]);

        if ($onlyToLocal === false) {
            // сохраняем текущие товары с куков в базу
            foreach ($this->comparison->get()->products as $product) {
                if (!isset($itemsInDb[$product->id])) {
                    $userBrowsedProductsEntity->add([
                        'user_id' => $_SESSION['user_id'],
                        'product_id' => $product->id,
                    ]);
                }
            }
        }

        if ($onlyToLocal === true) {
            setcookie("browsed_products", '', time()-3600, "/");
            $_COOKIE['browsed_products'] = '';
        }
        
        // экстендер ставим здесь, чтобы до мержа модули могли корректировать содержимое сравнения
        $itemsInDb = ExtenderFacade::execute(__METHOD__, $itemsInDb, func_get_args());
        
        // Объединяем товары с базы в куки
        foreach ($itemsInDb as $item) {
            $this->browsedProducts->addItem($item->product_id, true, true);
        }
        $this->browsedProducts->save();
        
        return $itemsInDb; // no ExtenderFacade
    }
}