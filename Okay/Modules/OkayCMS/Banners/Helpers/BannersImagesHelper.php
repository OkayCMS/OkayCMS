<?php


namespace Okay\Modules\OkayCMS\Banners\Helpers;


use Okay\Core\Config;
use Okay\Core\Database;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\Languages;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Modules\OkayCMS\Banners\Entities\BannersEntity;
use Okay\Modules\OkayCMS\Banners\Entities\BannersImagesEntity;

class BannersImagesHelper
{
    /**
     * @var BannersImagesEntity
     */
    private $bannersImagesEntity;
    
    /**
     * @var BannersEntity
     */
    private $bannersEntity;

    /**
     * @var Image
     */
    private $imageCore;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Languages
     */
    private $languages;

    public function __construct(
        EntityFactory $entityFactory,
        Config        $config,
        Image         $imageCore,
        QueryFactory  $queryFactory,
        Database      $db,
        Request       $request,
        Languages     $languages
    ) {
        $this->bannersEntity = $entityFactory->get(BannersEntity::class);
        $this->bannersImagesEntity = $entityFactory->get(BannersImagesEntity::class);
        $this->config       = $config;
        $this->imageCore    = $imageCore;
        $this->queryFactory = $queryFactory;
        $this->db           = $db;
        $this->request      = $request;
        $this->languages    = $languages;
    }

    public function findBannersImages($filter)
    {
        $bannersImages = $this->bannersImagesEntity->mappedBy('id')->find($filter);
        return ExtenderFacade::execute(__METHOD__, $bannersImages, func_get_args());
    }

    public function findAllBannersImages()
    {
        $bannersImagesCount = $this->bannersImagesEntity->count();
        $allBannersImages = $this->bannersImagesEntity->mappedBy('id')->find(['limit' => $bannersImagesCount]);
        return ExtenderFacade::execute(__METHOD__, $allBannersImages, func_get_args());
    }

    public function prepareAdd($bannerImage)
    {
        return ExtenderFacade::execute(__METHOD__, $bannerImage, func_get_args());
    }

    public function add($bannerImage)
    {
        $insertId = $this->bannersImagesEntity->add($bannerImage);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdate($bannerImage)
    {
        return ExtenderFacade::execute(__METHOD__, $bannerImage, func_get_args());
    }

    public function update($id, $bannerImage)
    {
        $this->bannersImagesEntity->update($id, $bannerImage);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getBannerImage($id)
    {
        $bannerImage = $this->bannersImagesEntity->get($id);

        if (!empty($bannerImage->settings)) {
            $bannerImage->settings = unserialize($bannerImage->settings);
        }
        
        return ExtenderFacade::execute(__METHOD__, $bannerImage, func_get_args());
    }

    public function deleteImage($bannerImage)
    {
        $this->imageCore->deleteImage(
            $bannerImage->id,
            'image',
            BannersImagesEntity::class,
            $this->config->get('banners_images_dir'),
            $this->config->get('resized_banners_images_dir'),
            $this->languages->getLangId(),
            BannersImagesEntity::getLangObject().'_id'
        );

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function uploadImage($image, $bannerImage, $isNewBannersImage = false)
    {
        if (!empty($image['name']) && ($filename = $this->imageCore->uploadImage($image['tmp_name'], $image['name'], $this->config->get('banners_images_dir')))) {
            $this->imageCore->deleteImage(
                $bannerImage->id,
                'image',
                BannersImagesEntity::class,
                $this->config->get('banners_images_dir'),
                $this->config->get('resized_banners_images_dir'),
                $this->languages->getLangId(),
                BannersImagesEntity::getLangObject().'_id'
            );
            
            if ($isNewBannersImage || !$bannerImage->is_lang_banner) {
                $currentLangId = $this->languages->getLangId();
                foreach ($this->languages->getAllLanguages() as $lang) {
                    $this->languages->setLangId($lang->id);
                    $this->bannersImagesEntity->update($bannerImage->id, ['image'=>$filename]);
                }
                $this->languages->setLangId($currentLangId);
            } else {
                $this->bannersImagesEntity->update($bannerImage->id, ['image' => $filename]);
            }
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function enable($ids)
    {
        $this->bannersImagesEntity->update($ids, ['visible' => 1]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function disable($ids)
    {
        $this->bannersImagesEntity->update($ids, ['visible' => 0]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete($ids)
    {
        $this->bannersImagesEntity->delete($ids);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function sortPositions($positions)
    {

        $ids = array_keys($positions);
        sort($positions);
        $positions = array_reverse($positions);
        foreach ($positions as $i=>$position) {
            $this->bannersImagesEntity->update($ids[$i], ['position'=>$position]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function buildFilter()
    {
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));
        $filter['limit'] = 20;

        // Текущий баннер
        $bannerId = $this->request->get('banner_id', 'integer');
        if ($bannerId && ($banner = $this->bannersEntity->get($bannerId))) {
            $filter['banner_id'] = $banner->id;
        }

        // Текущий фильтр
        if ($f = $this->request->get('filter', 'string'))
        {
            if ($f == 'visible') {
                $filter['visible'] = 1;
            } elseif ($f == 'hidden') {
                $filter['visible'] = 0;
            }
            $filter['filter'] = $f;
        } else {
            $filter['filter'] = null;
        }
        
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function moveToPage($ids, $targetPage, $filter)
    {
        $bannerId = $this->request->post('target_banner', 'integer');
        $filter['page'] = 1;
        $banner = $this->bannersEntity->get($bannerId);
        $filter['banner_id'] = $banner->id;

        $this->bannersImagesEntity->update($ids, ['banner_id'=>$banner->id]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    public function countBannersImages($filter)
    {
        $bannerImagesCount = $this->bannersImagesEntity->count($filter);
        return ExtenderFacade::execute(__METHOD__, $bannerImagesCount, func_get_args());
    }

    public function makePagination($bannersImagesCount, $filter)
    {
        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $bannersImagesCount;
        }

        if ($filter['limit'] > 0) {
            $pagesCount = ceil($bannersImagesCount/$filter['limit']);
        } else {
            $pagesCount = 0;
        }

        $filter['page'] = min($filter['page'], $pagesCount);

        return [$filter, $pagesCount];
    }
    
}