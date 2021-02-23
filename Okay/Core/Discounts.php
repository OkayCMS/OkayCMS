<?php


namespace Okay\Core;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\DiscountsEntity;

class Discounts
{
    /** @var DiscountsEntity */
    private $discountsEntity;


    /** @var array */
    private $signs;

    public function __construct(
        EntityFactory $entityFactory
    ) {
        $this->discountsEntity = $entityFactory->get(DiscountsEntity::class);

        $this->signs = [
            'purchase' => [],
            'cart' => [
                'ok_coup' => (object) [
                    'sign' => 'ok_coup',
                    'name' => 'discount_coupon_name',
                    'description' => 'discount_coupon_description'
                ],
                'ok_gr' => (object) [
                    'sign' => 'ok_gr',
                    'name' => 'discount_user_group_name',
                    'description' => 'discount_user_group_description'
                ]
            ]
        ];
    }

    /**
     * @param string $sign
     * @param string $name
     * @param string $description
     * @throws \Exception
     */
    public function registerPurchaseSign($sign, $name, $description)
    {
        if (isset($this->signs['purchase'][$sign]) && isset($this->signs['cart'][$sign])) {
            throw new \Exception("Sign \"{$sign}\" is already exists");
        } else {
            $signObject = (object) [
                'sign' => $sign,
                'name' => $name,
                'description' => $description
            ];
            $signObject = ExtenderFacade::execute(__METHOD__, $signObject, func_get_args());
            $this->signs['purchase'][$sign] = $signObject;
        }
    }

    /**
     * @param string $sign
     * @param string $name
     * @param string $description
     * @throws \Exception
     */
    public function registerCartSign($sign, $name, $description)
    {
        if (isset($this->discountSigns['purchase'][$sign]) && isset($this->discountSigns['cart'][$sign])) {
            throw new \Exception("Sign \"{$sign}\" is already exists");
        } else {
            $signObject = (object) [
                'sign' => $sign,
                'name' => $name,
                'description' => $description
            ];
            $signObject = ExtenderFacade::execute(__METHOD__, $signObject, func_get_args());
            $this->signs['cart'][$sign] = $signObject;
        }
    }

    /**
     * @return array
     */
    public function getRegisteredSigns()
    {
        return ExtenderFacade::execute(__METHOD__, $this->signs, func_get_args());
    }
}