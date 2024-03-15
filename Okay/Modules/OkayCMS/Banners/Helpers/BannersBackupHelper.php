<?php

namespace Okay\Modules\OkayCMS\Banners\Helpers;

use Okay\Core\EntityFactory;
use Okay\Core\Languages;
use Okay\Entities\PagesEntity;
use Okay\Modules\OkayCMS\Banners\DTO\BannerBackupDTO;
use Okay\Modules\OkayCMS\Banners\DTO\BannerImageBackupDTO;
use Okay\Modules\OkayCMS\Banners\DTO\BannerImageLangBackupDTO;
use Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO;
use Okay\Modules\OkayCMS\Banners\DTO\BannerSettingsDTO;
use Okay\Modules\OkayCMS\Banners\Entities\BannersEntity;
use Okay\Modules\OkayCMS\Banners\Entities\BannersImagesEntity;
use Okay\Modules\OkayCMS\Banners\VO\RestoreBackupErrorVO;

class BannersBackupHelper
{
    private const BACKUP_CONFIG_FILE = 'config.json';

    private BannersEntity $bannersEntity;
    private BannersImagesEntity $bannersImagesEntity;
    private PagesEntity $pagesEntity;
    private Languages $languages;
    private string $bannersImagesDir;

    public function __construct(
        EntityFactory $entityFactory,
        Languages $languages,
        string $bannersImagesDir
    ) {
        $this->languages = $languages;
        $this->bannersImagesDir = $bannersImagesDir;
        $this->bannersEntity = $entityFactory->get(BannersEntity::class);
        $this->bannersImagesEntity = $entityFactory->get(BannersImagesEntity::class);
        $this->pagesEntity = $entityFactory->get(PagesEntity::class);
    }

    /**
     * @param array $bannersIds
     * @return string|null
     * @throws \Exception
     *
     * Метод створює zip архів бекапу обраних груп банерів і повертає шлях до цього архіву.
     */
    public function backup(array $bannersIds): ?string
    {
        if ($backup = $this->makeBackupConfigFile($bannersIds)) {
            $backupImages = [];
            foreach ($backup as $bannerBackupDTO) {
                foreach ($bannerBackupDTO->getBannerImageBackupDTO() as $bannerImageBackupDTO) {
                    foreach ($bannerImageBackupDTO->getLangInfo() as $bannerImageLangBackupDTO) {
                        if ($image = $bannerImageLangBackupDTO->getImage()) {
                            $backupImages[] = $image;
                        }
                        if ($imageMobile = $bannerImageLangBackupDTO->getImageMobile()) {
                            $backupImages[] = $imageMobile;
                        }
                    }
                }
            }

            $backupConfigFile = $this->serializeBackupConfigFile($backup);
            return $this->zipBackup($backupConfigFile, array_unique($backupImages));
        }
        return null;
    }

    /**
     * @param string $zipFilename
     * @return RestoreBackupErrorVO[]
     *
     * Метод для розгортання бекапу банерів.
     */
    public function restoreBackup(string $zipFilename): array
    {
        $errors = [];
        if ($backupFilesDir = $this->unzipBackup($zipFilename)) {
            $configFile = $backupFilesDir . DIRECTORY_SEPARATOR . self::BACKUP_CONFIG_FILE;
            if (!is_file($configFile)) {
                return [new RestoreBackupErrorVO(
                    RestoreBackupErrorVO::WRONG_CONFIG_FILE,
                    [
                        self::BACKUP_CONFIG_FILE,
                    ]
                )];
            }

            if (($bannerBackupDTOs = $this->unserializeBackup(file_get_contents($configFile))) === null) {
                return [new RestoreBackupErrorVO(
                    RestoreBackupErrorVO::WRONG_CONFIG_FILE,
                    [
                        self::BACKUP_CONFIG_FILE,
                    ]
                )];
            }

            $mainLanguage = $this->languages->getMainLanguage();
            $currentLanguageId = $this->languages->getLangId();
            foreach ($this->languages->getAllLanguages() as $language) {
                $allLanguages[$language->label] = $language;
            }

            $pages = [];
            foreach ($this->pagesEntity->cols(['id', 'url'])->noLimit()->find() as $p) {
                $pages[(string)$p->url] = $p;
            }

            foreach ($bannerBackupDTOs as $bannerBackupDTO) {
                if (empty($bannerBackupDTO->getGroupName())
                    || $this->bannersEntity->findOne(['group_name' => $bannerBackupDTO->getGroupName()]))
                {
                    $errors[] = new RestoreBackupErrorVO(
                        RestoreBackupErrorVO::GROUP_ALREADY_EXISTS,
                        [
                            $bannerBackupDTO->getGroupName(),
                        ]
                    );
                    continue;
                }

                $bannerPages = [];
                if ($backupBannerPages = $bannerBackupDTO->getPages()) {
                    foreach ($backupBannerPages as $pageUrl) {
                        if (isset($pages[$pageUrl])) {
                            $bannerPages[] = $pages[$pageUrl]->id;
                        }
                    }
                }

                $bannerId = $this->bannersEntity->add([
                    'name' => $bannerBackupDTO->getName(),
                    'group_name' => $bannerBackupDTO->getGroupName(),
                    'visible' => 1,
                    'pages' => implode(',', $bannerPages),
                    'show_all_pages' => $backupBannerPages === null,
                    'as_individual_shortcode' => $bannerBackupDTO->isAsIndividualShortcode(),
                    'settings' => serialize($bannerBackupDTO->getSettings()),
                ]);

                foreach ($bannerBackupDTO->getBannerImageBackupDTO() as $bannerImageBackupDTO) {
                    $isLangBanner = false;
                    $images = [];
                    $imagesMobile = [];
                    foreach ($bannerImageBackupDTO->getLangInfo() as $bannerImageLangBackupDTO) {
                        $images[] = $bannerImageLangBackupDTO->getImage();
                        $imagesMobile[] = $bannerImageLangBackupDTO->getImageMobile();
                    }
                    if (count(array_unique($images)) > 1 || count(array_unique($imagesMobile)) > 1) {
                        $isLangBanner = true;
                    }
                    $bannerImageId = null;
                    $image = null;
                    $imageMobile = null;
                    foreach ($bannerImageBackupDTO->getLangInfo() as $langLabel => $bannerImageLangBackupDTO) {
                        $this->languages->setLangId($allLanguages[$langLabel]->id ?? $mainLanguage->id);
                        $bannerImage = [
                            'name' => $bannerImageLangBackupDTO->getName(),
                            'alt' => $bannerImageLangBackupDTO->getAlt(),
                            'title' => $bannerImageLangBackupDTO->getTitle(),
                            'description' => $bannerImageLangBackupDTO->getDescription(),
                            'url' => $bannerImageLangBackupDTO->getUrl(),
                        ];

                        if ($isLangBanner || is_null($image)) {
                            $image = $bannerImage['image'] = $this->copyImage(
                                $backupFilesDir,
                                $bannerImageLangBackupDTO->getImage()
                            );
                        } else {
                            $bannerImage['image'] = $image;
                        }

                        if ($isLangBanner || is_null($imageMobile)) {
                            $imageMobile = $bannerImage['image_mobile'] = $this->copyImage(
                                $backupFilesDir,
                                $bannerImageLangBackupDTO->getImageMobile()
                            );
                        } else {
                            $bannerImage['image_mobile'] = $imageMobile;
                        }

                        if (empty($bannerImageId)) {
                            $bannerImage['banner_id'] = $bannerId;
                            $bannerImage['visible'] = 1;
                            $bannerImage['settings'] = serialize($bannerImageBackupDTO->getSettings());
                            $bannerImage['is_lang_banner'] = $isLangBanner;
                            $bannerImageId = $this->bannersImagesEntity->add($bannerImage);
                        } else {
                            $this->bannersImagesEntity->update($bannerImageId, $bannerImage);
                        }
                    }
                }
            }
            $this->languages->setLangId($currentLanguageId);

            return $errors;
        }

        return [new RestoreBackupErrorVO(RestoreBackupErrorVO::UNZIP_ERROR)];
    }

    private function zipBackup(string $backupConfigFile, array $backupImages): string
    {
        $zip = new \ZipArchive();
        $tempConfigFile = tempnam(sys_get_temp_dir(), 'banner_config') . '.json';
        file_put_contents($tempConfigFile, $backupConfigFile);
        $tempArchive = tempnam(sys_get_temp_dir(), 'banner_backup') . '.zip';

        $zip->open($tempArchive, \ZipArchive::CREATE);
        $zip->addFile($tempConfigFile, self::BACKUP_CONFIG_FILE);
        foreach ($backupImages as $backupImage) {
            $zip->addFile($this->bannersImagesDir . $backupImage, $backupImage);
        }
        $zip->close();
        return $tempArchive;
    }

    private function unzipBackup(string $zipFilename): ?string
    {
        $zip = new \ZipArchive();
        $tempDir = sys_get_temp_dir() . '/tmp_' . microtime(true);
        if ($zip->open($zipFilename) === true) {
            $zip->extractTo($tempDir);
            $zip->close();
            unlink($zipFilename);

            return $tempDir;
        }
        return null;
    }

    /**
     * @param string $jsonBackupInfo
     * @return BannerBackupDTO[]|null
     */
    private function unserializeBackup(string $jsonBackupInfo): ?array
    {
        $backupInfoList = json_decode($jsonBackupInfo, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($backupInfoList)) {
            return null;
        }
        $bannerBackupDTOs = [];
        foreach ($backupInfoList as $backupInfo) {
            $bannerBackupDTO = new BannerBackupDTO();
            $bannerBackupDTO->fromArray($backupInfo);

            $bannerSettingsDTO = new BannerSettingsDTO();
            if (!empty($backupInfo['settings'])) {
                $bannerSettingsDTO->fromArray($backupInfo['settings']);
            }
            $bannerBackupDTO->setSettings($bannerSettingsDTO);

            foreach ($backupInfo['bannerImages'] ?? [] as $bannerImageBackup) {
                $bannerImageBackupDTO = new BannerImageBackupDTO();

                $bannerImageSettingsDTO = new BannerImageSettingsDTO();
                if (!empty($bannerImageBackup['settings'])) {
                    $bannerImageSettingsDTO->fromArray((array)$bannerImageBackup['settings']);
                }
                $bannerImageBackupDTO->setSettings($bannerImageSettingsDTO);

                foreach ($bannerImageBackup['langInfo'] ?? [] as $langLabel => $langInfo) {
                    $bannerImageLangBackupDTO = new BannerImageLangBackupDTO();
                    $bannerImageLangBackupDTO->fromArray($langInfo);
                    $bannerImageBackupDTO->setLangInfo($bannerImageLangBackupDTO, $langLabel);
                }
                $bannerBackupDTO->setBannerImageBackupDTO($bannerImageBackupDTO);
            }
            $bannerBackupDTOs[] = $bannerBackupDTO;
        }

        return $bannerBackupDTOs;
    }

    /**
     * @param array $bannersIds
     * @return BannerBackupDTO[]
     * @throws \Exception
     */
    private function makeBackupConfigFile(array $bannersIds): array
    {
        $backup = [];
        $banners = $this->bannersEntity->mappedBy('id')->find(['id' => $bannersIds]);
        $pages = $this->pagesEntity->mappedBy('id')->noLimit()->find();
        if (empty($banners)) {
            return [];
        }
        $currentLangId = $this->languages->getLangId();
        $langList = $this->languages->getAllLanguages();

        foreach ($banners as $banner) {
            $bannerBackupDTO = new BannerBackupDTO();
            $bannerBackupDTO->setName($banner->name);
            $bannerBackupDTO->setGroupName($banner->group_name);
            $bannerBackupDTO->setAsIndividualShortcode((bool)$banner->as_individual_shortcode);
            foreach (explode(',', $banner->pages) as $pageId) {
                if (isset($pages[$pageId])) {
                    $bannerBackupDTO->setPage($pages[$pageId]->url);
                }
            }
            $settings = unserialize($banner->settings);
            if ($settings instanceof BannerSettingsDTO) {
                $bannerBackupDTO->setSettings($settings);
            }
            $backup[$banner->id] = $bannerBackupDTO;
        }

        $bannerImageBackupDTOList = [];
        foreach ($this->bannersImagesEntity->find(['banner_id' => array_keys($banners)]) as $bi) {
            $bannerImageBackupDTO = new BannerImageBackupDTO();
            $settings = unserialize($bi->settings);
            if ($settings instanceof BannerImageSettingsDTO) {
                $bannerImageBackupDTO->setSettings($settings);
            }

            $langInfo = $this->createBannerImageLangDTO($bi);
            $bannerImageBackupDTO->setLangInfo($langInfo, $langList[$currentLangId]->label);
            $bannerImageBackupDTOList[$bi->banner_id][$bi->id] = $bannerImageBackupDTO;
        }

        foreach ($langList as $lang) {
            if ($lang->id == $currentLangId) {
                continue;
            }
            $this->languages->setLangId($lang->id);
            foreach ($this->bannersImagesEntity->find(['banner_id' => array_keys($banners)]) as $bi) {
                $langInfo = $this->createBannerImageLangDTO($bi);
                $bannerImageBackupDTOList[$bi->banner_id][$bi->id]->setLangInfo($langInfo, $lang->label);
            }
        }

        foreach ($bannerImageBackupDTOList as $bannerId => $bannerImageBackupDTOs) {
            foreach ($bannerImageBackupDTOs as $bannerImageBackupDTO) {
                $backup[$bannerId]->setBannerImageBackupDTO($bannerImageBackupDTO);
            }
        }

        $this->languages->setLangId($currentLangId);

        return array_values($backup);
    }

    private function serializeBackupConfigFile(array $backup): ?string
    {
        $serialized = json_encode($backup, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        return preg_replace('/^(  +?)\\1(?=[^ ])/m', '$1', $serialized);
    }

    private function createBannerImageLangDTO(object $bannerImageInfo): BannerImageLangBackupDTO
    {
        $langInfo = new BannerImageLangBackupDTO();
        $langInfo->setName($bannerImageInfo->name);
        $langInfo->setAlt($bannerImageInfo->alt);
        $langInfo->setTitle($bannerImageInfo->title);
        $langInfo->setDescription($bannerImageInfo->description);
        $langInfo->setUrl($bannerImageInfo->url);
        $langInfo->setImage($bannerImageInfo->image);
        $langInfo->setImageMobile($bannerImageInfo->image_mobile);

        return $langInfo;
    }

    private function copyImage(string $backupFilesDir, string $filename): string
    {
        if (empty($filename)) {
            return $filename;
        }
        $newFilename = $filename;
        $ext = pathinfo($newFilename, PATHINFO_EXTENSION);
        $base = pathinfo($newFilename, PATHINFO_FILENAME);

        while (file_exists($this->bannersImagesDir . $newFilename)) {
            $newBase = pathinfo($newFilename, PATHINFO_FILENAME);
            $parts = [];
            preg_match('~_([0-9]+)$~', $newBase, $parts);
            $newFilename = $base . '_' . (($parts[1] ?? 0) + 1) . '.' . $ext;
        }

        // Копіюємо зображення
        copy($backupFilesDir . DIRECTORY_SEPARATOR . $filename, $this->bannersImagesDir . $newFilename);
        return $newFilename;
    }
}