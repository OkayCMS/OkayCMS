<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\VO;

use Okay\Core\Settings;

class NPCalcVO
{
    private int $totalPrice;
    private float $totalWeight = 0;
    private float $totalVolume = 0;
    private Settings $settings;

    public function __construct(Settings $settings, int $totalPrice)
    {
        $this->settings = $settings;
        $this->totalPrice = $totalPrice;
    }

    /**
     * @param float $weight
     * @param int $amount
     * @return void
     */
    public function addPurchaseWeight(float $weight, int $amount = 1): void
    {
        $this->totalWeight += ($weight > 0 ? $weight : $this->settings->get('newpost_weight')) * $amount;
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
        } elseif (!empty($this->settings->get('newpost_volume'))) {
            $vol = $this->settings->get('newpost_volume');
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