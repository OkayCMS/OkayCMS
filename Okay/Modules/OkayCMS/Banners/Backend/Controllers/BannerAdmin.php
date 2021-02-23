<?php


namespace Okay\Modules\OkayCMS\Banners\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Modules\OkayCMS\Banners\Entities\BannersEntity;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\PagesEntity;
use Okay\Modules\OkayCMS\Banners\Helpers\BannersHelper;
use Okay\Modules\OkayCMS\Banners\Requests\BannersRequest;

class BannerAdmin extends IndexAdmin
{
    public function fetch(
        BannersEntity $bannersEntity,
        CategoriesEntity $categoriesEntity,
        BrandsEntity $brandsEntity,
        PagesEntity $pagesEntity,
        BannersRequest $bannersRequest,
        BannersHelper $bannersHelper
    ) {
        $categories = $categoriesEntity->getCategoriesTree();
        $brands     = $brandsEntity->find();
        $pages      = $pagesEntity->find();

        /*Принимаем данные о группе баннеров*/
        if ($this->request->method('POST')) {

            $banner = $bannersRequest->postBanner();
            if (!empty($banner->as_individual_shortcode) && ($b = $bannersEntity->findOne(['group_name' => $banner->group_name])) && $b->id!=$banner->id) {
                $this->design->assign('message_error', 'shortcode_exists');
            } else {
                if (empty($banner->id)) {
                    /*Добавляем/обновляем группу баннеров*/
                    $preparedBanner = $bannersHelper->prepareAdd($banner);
                    $banner->id = $bannersHelper->add($preparedBanner);
                    $this->design->assign('message_success', 'added');
                } else {
                    $preparedBanner = $bannersHelper->prepareUpdate($banner);
                    $bannersHelper->update($preparedBanner->id, $preparedBanner);
                    $this->design->assign('message_success', 'updated');
                }
            }
            $banner = $bannersHelper->addSelectedEntities($banner);
        } else {
            $bannerId = $this->request->get('id', 'integer');

            // Если пришли с меню быстрого редактирования
            if ($bannerSlideId = $this->request->get('banner_slide_id')) {
                list($bannerId, $slideId) = explode(':', $bannerSlideId);
            }
            
            $banner   = $bannersHelper->getBanner((int)$bannerId);
            $banner   = $bannersHelper->getSelectedEntities($banner);
        }

        $this->design->assign('banner',     $banner);
        $this->design->assign('categories', $categories);
        $this->design->assign('brands',     $brands);
        $this->design->assign('pages',      $pages);

        if (!empty($banner->settings)) {
            $banner->settings = unserialize($banner->settings);
        }
        
        $this->response->setContent($this->design->fetch('banner.tpl'));
    }
}
