<?php


namespace Okay\Modules\OkayCMS\Banners\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Modules\OkayCMS\Banners\Entities\BannersEntity;
use Okay\Modules\OkayCMS\Banners\Entities\BannersImagesEntity;
use Okay\Modules\OkayCMS\Banners\Helpers\BannersImagesHelper;
use Okay\Modules\OkayCMS\Banners\Requests\BannersImagesRequest;

class BannersImagesAdmin extends IndexAdmin
{
    
    public function fetch(
        BannersImagesEntity $bannersImagesEntity,
        BannersEntity $bannersEntity,
        BannersImagesRequest $bannersImagesRequest,
        BannersImagesHelper $bannersImagesHelper
    ) {

        $filter = $bannersImagesHelper->buildFilter();
        
        $this->design->assign('filter',         $filter['filter']);
        // Баннера
        $banners = $bannersEntity->find(); // todo
        $this->design->assign('banners', $banners);

        // Обработка действий
        if ($this->request->method('post')) {
            // Сортировка
            $positions = $bannersImagesRequest->postPositions();
            $bannersImagesHelper->sortPositions($positions);
            
            // Смена группы
            $imageBanners = $this->request->post('image_banners');
            foreach($imageBanners as $i=>$imageBanner) {
                $bannersImagesEntity->update($i, array('banner_id'=>$imageBanner));
            }

            // Действия с выбранными
            $ids = $bannersImagesRequest->postCheck();
            switch ($bannersImagesRequest->postAction()) {
                case 'enable': {
                    $bannersImagesHelper->enable($ids);
                    break;
                }
                case 'disable': {
                    $bannersImagesHelper->disable($ids);
                    break;
                }
                case 'delete': {
                    $bannersImagesHelper->delete($ids);
                    break;
                }
                case 'move_to_page': {
                    $targetPage = $this->request->post('target_page', 'integer');
                    $bannersImagesHelper->moveToPage($ids, $targetPage, $filter);
                    break;
                }
            }
        }
        
        // Отображение
        if (!empty($filter['banner_id'])) {
            $banner = $bannersEntity->get((int)$filter['banner_id']);
            $this->design->assign('banner', $banner);
        }
        
        $bannersImagesCount        = $bannersImagesHelper->countBannersImages($filter);
        list($filter, $pagesCount) = $bannersImagesHelper->makePagination($bannersImagesCount, $filter);
        $bannersImages             = $bannersImagesHelper->findBannersImages($filter);

        $this->design->assign('banners_images_count', $bannersImagesCount);
        $this->design->assign('pages_count', $pagesCount);
        $this->design->assign('current_page', $filter['page']);
        $this->design->assign('banners_images', $bannersImages);
        
        $this->response->setContent($this->design->fetch('banners_images.tpl'));
    }
    
}
