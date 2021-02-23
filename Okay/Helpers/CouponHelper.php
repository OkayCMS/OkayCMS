<?php


namespace Okay\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\CouponsEntity;

class CouponHelper
{
    /**
     * @var EntityFactory
     */
    private $entityFactory;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }

    public function registerUseIfExists($couponCode)
    {
        if (!empty($couponCode)) {
            $couponsEntity = $this->entityFactory->get(CouponsEntity::class);
            $coupon = $couponsEntity->get((string) $couponCode);

            $couponsEntity->update($coupon->id, [
                'usages' => $coupon->usages+1
            ]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}