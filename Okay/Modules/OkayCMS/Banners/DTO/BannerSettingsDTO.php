<?php

namespace Okay\Modules\OkayCMS\Banners\DTO;

class BannerSettingsDTO implements \JsonSerializable
{
    public const DEFAULT_ROTATION_SPEED = 2500;

    private bool $asSlider = true;
    private bool $autoplay = true;
    private bool $loop = false;
    private bool $nav = false;
    private bool $dots = false;
    private int $rotationSpeed = self::DEFAULT_ROTATION_SPEED;

    /**
     * @return bool
     */
    public function isAsSlider(): bool
    {
        return $this->asSlider;
    }

    /**
     * @param bool $asSlider
     */
    public function setAsSlider(bool $asSlider): void
    {
        $this->asSlider = $asSlider;
    }

    /**
     * @return bool
     */
    public function isAutoplay(): bool
    {
        return $this->autoplay;
    }

    /**
     * @param bool $autoplay
     */
    public function setAutoplay(bool $autoplay): void
    {
        $this->autoplay = $autoplay;
    }

    /**
     * @return bool
     */
    public function isLoop(): bool
    {
        return $this->loop;
    }

    /**
     * @param bool $loop
     */
    public function setLoop(bool $loop): void
    {
        $this->loop = $loop;
    }

    /**
     * @return bool
     */
    public function isNav(): bool
    {
        return $this->nav;
    }

    /**
     * @param bool $nav
     */
    public function setNav(bool $nav): void
    {
        $this->nav = $nav;
    }

    /**
     * @return bool
     */
    public function isDots(): bool
    {
        return $this->dots;
    }

    /**
     * @param bool $dots
     */
    public function setDots(bool $dots): void
    {
        $this->dots = $dots;
    }

    /**
     * @return int
     */
    public function getRotationSpeed(): int
    {
        return $this->rotationSpeed;
    }

    /**
     * @param int $rotationSpeed
     */
    public function setRotationSpeed(int $rotationSpeed): void
    {
        $this->rotationSpeed = $rotationSpeed;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function fromArray(array $array)
    {
        $this->setAsSlider((bool)($array['asSlider'] ?? true));
        $this->setAutoplay((bool)($array['autoplay'] ?? true));
        $this->setLoop((bool)($array['loop'] ?? false));
        $this->setNav((bool)($array['nav'] ?? false));
        $this->setDots((bool)($array['dots'] ?? false));
        $this->setRotationSpeed((int)($array['rotationSpeed'] ?? self::DEFAULT_ROTATION_SPEED));
    }
}