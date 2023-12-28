<?php


namespace Okay\Modules\OkayCMS\Banners\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Image;

class BannersImagesEntity extends Entity
{
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
        'image_mobile',
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
            $imageCore->deleteImage(
                (int)$id,
                'image_mobile',
                self::class,
                $this->config->banners_images_dir,
                $this->config->resized_banners_images_dir
            );
        }

        return parent::delete($ids);
    }
    
}
