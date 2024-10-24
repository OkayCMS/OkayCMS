<?php

namespace Okay\Modules\OkayCMS\Banners\DTO;

class BannerImageSettingsDTO implements \JsonSerializable
{
    const SHOW_DEFAULT = 'default';
    const SHOW_DARK = 'dark';
    const SHOW_IMAGE_LEFT = 'image_left';
    const SHOW_IMAGE_RIGHT = 'image_right';
    const DEFAULT_DESKTOP_W = 1200;
    const DEFAULT_DESKTOP_H = 700;
    const DEFAULT_MOBILE_W = 500;
    const DEFAULT_MOBILE_H = 320;

    private string $variantShow = self::SHOW_DEFAULT;
    private ?string $mobileVariantShow = self::SHOW_DEFAULT;
    private int $desktopWidth = self::DEFAULT_DESKTOP_W;
    private int $desktopHeight = self::DEFAULT_DESKTOP_H;
    private int $mobileWidth = self::DEFAULT_MOBILE_W;
    private int $mobileHeight = self::DEFAULT_MOBILE_H;

    /**
     * @return string
     */
    public function getVariantShow(): string
    {
        return $this->variantShow;
    }

    /**
     * @param string $variantShow
     */
    public function setVariantShow(string $variantShow): void
    {
        if (!in_array($variantShow, [
            self::SHOW_DEFAULT,
            self::SHOW_DARK,
            self::SHOW_IMAGE_LEFT,
            self::SHOW_IMAGE_RIGHT,
        ])) {
            return;
        }
        $this->variantShow = $variantShow;
    }

    /**
     * @return string|null
     */
    public function getMobileVariantShow(): ?string
    {
        return $this->mobileVariantShow;
    }

    /**
     * @param string|null $mobileVariantShow
     */
    public function setMobileVariantShow(?string $mobileVariantShow): void
    {
        if (!in_array($mobileVariantShow, [
                self::SHOW_DEFAULT,
                self::SHOW_DARK,
                self::SHOW_IMAGE_LEFT,
                self::SHOW_IMAGE_RIGHT,
            ])) {
            return;
        }

        $this->mobileVariantShow = $mobileVariantShow;
    }

    /**
     * @return int
     */
    public function getDesktopWidth(): int
    {
        return $this->desktopWidth;
    }

    /**
     * @param int $desktopWidth
     */
    public function setDesktopWidth(int $desktopWidth): void
    {
        if ($desktopWidth) {
            $this->desktopWidth = $desktopWidth;
        }
    }

    /**
     * @return int
     */
    public function getDesktopHeight(): int
    {
        return $this->desktopHeight;
    }

    /**
     * @param int $desktopHeight
     */
    public function setDesktopHeight(int $desktopHeight): void
    {
        if ($desktopHeight) {
            $this->desktopHeight = $desktopHeight;
        }
    }

    /**
     * @return int
     */
    public function getMobileWidth(): int
    {
        return $this->mobileWidth;
    }

    /**
     * @param int $mobileWidth
     */
    public function setMobileWidth(int $mobileWidth): void
    {
        if ($mobileWidth) {
            $this->mobileWidth = $mobileWidth;
        }
    }

    /**
     * @return int
     */
    public function getMobileHeight(): int
    {
        return $this->mobileHeight;
    }

    /**
     * @param int $mobileHeight
     */
    public function setMobileHeight(int $mobileHeight): void
    {
        if ($mobileHeight) {
            $this->mobileHeight = $mobileHeight;
        }
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function fromArray(array $array)
    {
        $this->setVariantShow($array['variantShow'] ?? self::SHOW_DEFAULT);
        $this->setMobileVariantShow($array['mobileVariantShow'] ?? '');
        $this->setDesktopWidth((int)($array['desktopWidth'] ?? self::DEFAULT_DESKTOP_W));
        $this->setDesktopHeight((int)($array['desktopHeight'] ?? self::DEFAULT_DESKTOP_H));
        $this->setMobileWidth((int)($array['mobileWidth'] ?? self::DEFAULT_MOBILE_W));
        $this->setMobileHeight((int)($array['mobileHeight'] ?? self::DEFAULT_MOBILE_H));
    }
}