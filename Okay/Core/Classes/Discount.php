<?php


namespace Okay\Core\Classes;


use Okay\Core\Modules\Extender\ExtenderFacade;

class Discount
{
    /** @var string */
    public $sign;

    /** @var string */
    public $type;

    /** @var string|int|float */
    public $value;

    /** @var bool */
    public $fromLastDiscount;

    /** @var string|int|float */
    public $priceBeforeDiscount;

    /** @var string|int|float */
    public $priceAfterDiscount;

    /** @var string|int|float */
    public $absoluteDiscount;

    /** @var string|int|float */
    public $percentDiscount;

    /** @var array */
    public $lang = [];

    /** @var array */
    public $langParts = [];

    /** @var string */
    public $name;

    /** @var string */
    public $description;

    /**
     * @param string $entity
     * @param string|int $entityId
     * @return object
     */
    public function getForDB($entity, $entityId)
    {
        $discount = (object) [
            'entity' => $entity,
            'entity_id' => $entityId,
            'type' => $this->type,
            'value' => $this->value,
            'from_last_discount' => (int) $this->fromLastDiscount,
            'name' => $this->name,
            'description' => $this->description
        ];

        return ExtenderFacade::execute(__METHOD__, $discount, func_get_args());
    }

    /**
     * @param string|int|float $undiscountedInitialPrice
     */
    public function calculate($undiscountedInitialPrice)
    {
        switch ($this->type) {
            case 'absolute':
                $this->absoluteDiscount = $this->value;
                $this->percentDiscount = round($this->absoluteDiscount / ($undiscountedInitialPrice / 100), 2);
                break;

            case 'percent':
                if ($this->fromLastDiscount) {
                    $this->absoluteDiscount = $this->priceBeforeDiscount * ($this->value / 100);
                    $this->percentDiscount = round($this->absoluteDiscount / ($undiscountedInitialPrice / 100), 2);
                } else {
                    $this->absoluteDiscount = $undiscountedInitialPrice * ($this->value / 100);
                    $this->percentDiscount = $this->value;
                }
                break;
        }
        $this->priceAfterDiscount = $this->priceBeforeDiscount - $this->absoluteDiscount;
        if ($this->priceAfterDiscount < 0)
            $this->priceAfterDiscount = 0;
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
    }
}