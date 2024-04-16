<?php

namespace Okay\Modules\OkayCMS\Banners\DTO;

class BannerBackupDTO implements \JsonSerializable
{
    private string $name;
    private string $groupName;
    private bool $asIndividualShortcode = false;
    private ?array $pages = null;
    private BannerSettingsDTO $settings;

    /**
     * @var BannerImageBackupDTO[]
     */
    private array $bannerImages = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * @param string $groupName
     */
    public function setGroupName(string $groupName): void
    {
        $this->groupName = $groupName;
    }

    /**
     * @return bool
     */
    public function isAsIndividualShortcode(): bool
    {
        return $this->asIndividualShortcode;
    }

    /**
     * @param bool $asIndividualShortcode
     */
    public function setAsIndividualShortcode(bool $asIndividualShortcode): void
    {
        $this->asIndividualShortcode = $asIndividualShortcode;
    }

    /**
     * @return BannerSettingsDTO
     */
    public function getSettings(): BannerSettingsDTO
    {
        return $this->settings;
    }

    /**
     * @param BannerSettingsDTO $settings
     */
    public function setSettings(BannerSettingsDTO $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @return array
     */
    public function getBannerImageBackupDTO(): array
    {
        return $this->bannerImages;
    }

    /**
     * @return null|array
     */
    public function getPages(): ?array
    {
        return $this->pages;
    }

    /**
     * @param string $page
     */
    public function setPage(string $page): void
    {
        $this->pages[] = $page;
    }

    /**
     * @param BannerImageBackupDTO $bannerImageBackupDTO
     */
    public function setBannerImageBackupDTO(BannerImageBackupDTO $bannerImageBackupDTO): void
    {
        $this->bannerImages[] = $bannerImageBackupDTO;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function fromArray(array $array)
    {
        $this->setName($array['name'] ?? '');
        $this->setGroupName($array['groupName'] ?? '');
        foreach ($array['pages'] ?? [] as $page) {
            $this->setPage($page);
        }
        $this->setAsIndividualShortcode((bool)($array['asIndividualShortcode'] ?? false));
    }
}