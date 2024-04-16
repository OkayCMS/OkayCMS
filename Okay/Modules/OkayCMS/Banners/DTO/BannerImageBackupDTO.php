<?php

namespace Okay\Modules\OkayCMS\Banners\DTO;

class BannerImageBackupDTO implements \JsonSerializable
{
    private BannerImageSettingsDTO $settings;

    /**
     * @var BannerImageLangBackupDTO[]
     */
    private array $langInfo;

    /**
     * @return BannerImageLangBackupDTO[]
     */
    public function getLangInfo(): array
    {
        return $this->langInfo;
    }

    /**
     * @param BannerImageLangBackupDTO $bannerImageLangBackupDTO
     * @param string $langLabel
     */
    public function setLangInfo(BannerImageLangBackupDTO $bannerImageLangBackupDTO, string $langLabel): void
    {
        $this->langInfo[$langLabel] = $bannerImageLangBackupDTO;
    }

    /**
     * @return BannerImageSettingsDTO
     */
    public function getSettings(): BannerImageSettingsDTO
    {
        return $this->settings;
    }

    /**
     * @param BannerImageSettingsDTO $settings
     */
    public function setSettings(BannerImageSettingsDTO $settings): void
    {
        $this->settings = $settings;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}