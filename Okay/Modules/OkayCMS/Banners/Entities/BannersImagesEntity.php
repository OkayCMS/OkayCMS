<?php


namespace Okay\Modules\OkayCMS\Banners\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Image;

class BannersImagesEntity extends Entity
{
    
    const SHOW_DEFAULT = 'default';
    const SHOW_DARK = 'dark';
    const SHOW_IMAGE_LEFT = 'image_left';
    const SHOW_IMAGE_RIGHT = 'image_right';
    const DEFAULT_DESKTOP_W = 1200;
    const DEFAULT_DESKTOP_H = 700;
    const DEFAULT_MOBILE_W = 500;
    const DEFAULT_MOBILE_H = 320;
    
    protected static $fields = [
        'id',
        'banner_id',
        'position',
        'visible',
        'settings',
        'is_lang_banner',
    ];

    protected static $langFields = [
        'image',
        'name',
        'alt',
        'title',
        'description',
        'url',
    ];

    protected static $defaultOrderFields = [
        'position DESC',
    ];

    protected static $table = 'okaycms__banners_images';
    protected static $langObject = 'banner_image';
    protected static $langTable = 'okaycms__banners_images';
    protected static $tableAlias = 'bi';

    public function delete($ids)
    {
        if (empty($ids)) {
            return parent::delete($ids);
        }

        /** @var Image $imageCore */
        $imageCore = $this->serviceLocator->getService(Image::class);

        $ids = (array)$ids;
        foreach ($ids as $id) {
            $imageCore->deleteImage(
                (int)$id,
                'image',
                self::class,
                $this->config->banners_images_dir,
                $this->config->resized_banners_images_dir
            );
        }

        return parent::delete($ids);
    }
    
}
