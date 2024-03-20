<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\VO;

class NPCalcVO
{
    private int $totalPrice;
    private float $totalWeight = 0;
    private float $totalVolume = 0;
    private float $defaultWeight;
    private float $defaultVolume;

    public function __construct(int $totalPrice, float $defaultWeight, float $defaultVolume)
    {
        $this->totalPrice = $totalPrice;
        $this->defaultWeight = $defaultWeight;
        $this->defaultVolume = $defaultVolume;
    }

    /**
     * @param float $weight
     * @param int $amount
     * @return void
     */
    public function addPurchaseWeight(float $weight, int $amount = 1): void
    {
        $this->totalWeight += ($weight > 0 ? $weight : $this->defaultWeight) * $amount;
    }

    /**
     * @param float $volume
     * @param int $amount
     * @return void
     */
    public function addPurchaseVolume(float $volume, int $amount = 1): void
    {
        if ($volume > 0) {
            $vol = $volume;
        } elseif (!empty($this->defaultVolume)) {
            $vol = $this->defaultVolume;
        } else {
            $vol = 0.001;
        }

        $this->totalVolume += $vol * $amount;
    }

    /**
     * @return int
     */
    public function getTotalPrice(): int
    {
        return $this->totalPrice;
    }

    /**
     * @return float
     */
    public function getTotalWeight(): float
    {
        return $this->totalWeight;
    }

    /**
     * @return float
     */
    public function getTotalVolume(): float
    {
        return $this->totalVolume;
    }
}