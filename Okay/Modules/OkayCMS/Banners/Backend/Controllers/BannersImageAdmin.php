<?php


namespace Okay\Modules\OkayCMS\Banners\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Modules\OkayCMS\Banners\Entities\BannersEntity;
use Okay\Modules\OkayCMS\Banners\Helpers\BannersImagesHelper;
use Okay\Modules\OkayCMS\Banners\Requests\BannersImagesRequest;

class BannersImageAdmin extends IndexAdmin
{
    
    public function fetch(
        BannersEntity $bannersEntity,
        BannersImagesRequest $bannersImagesRequest,
        BannersImagesHelper $bannersImagesHelper
    ) {
        /*Принимаем данные о слайде*/
        if ($this->request->method('post')) {
            $bannersImage = $bannersImagesRequest->postBannerImage();
            
            /*Добавляем/удаляем слайд*/
            if (empty($bannersImage->id)) {
                $preparedBannersImage = $bannersImagesHelper->prepareAdd($bannersImage);
                $bannersImage->id     = $bannersImagesHelper->add($preparedBannersImage);
                $this->design->assign('message_success', 'added');
                $isNewBannersImage = true;
            } else {
                
                // если сняли галочку "Мультиязычный баннер", проставим изображение баннера с основного языка для всех
                if (!$bannersImage->is_lang_banner) {
                    $currentLangId = $this->languages->getLangId();
                    $mainLang = $this->languages->getMainLanguage();
                    $this->languages->setLangId($mainLang->id);
                    $currentBannersImage = $bannersImagesHelper->getBannerImage((int)$bannersImage->id);
                    if ($currentBannersImage->is_lang_banner != $bannersImage->is_lang_banner) {
                        
                        foreach ($this->languages->getAllLanguages() as $lang) {
                            $this->languages->setLangId($lang->id);
                            $bannersImagesHelper->update($bannersImage->id, ['image' => $currentBannersImage->image]);
                        }
                        
                    }
                    $this->languages->setLangId($currentLangId);
                }
                
                $preparedBannersImage = $bannersImagesHelper->prepareUpdate($bannersImage);
                $bannersImagesHelper->update($preparedBannersImage->id, $preparedBannersImage);
                $this->design->assign('message_success', 'updated');
                $isNewBannersImage = false;
            }

            // Картинка
            if ($bannersImagesRequest->postDeleteImage()) {
                $bannersImagesHelper->deleteImage($bannersImage);
            }

            if ($image = $bannersImagesRequest->fileImage()) {
                $bannersImagesHelper->uploadImage($image, $bannersImage, $isNewBannersImage);
            }

            $bannersImage = $bannersImagesHelper->getBannerImage((int)$bannersImage->id);
        } else {
            $bannersImageId = $this->request->get('id', 'integer');

            // Если пришли с меню быстрого редактирования
            if ($bannerSlideId = $this->request->get('banner_slide_id')) {
                list($bannerId, $bannersImageId) = explode(':', $bannerSlideId);
            } elseif ($bannerSlideId = $this->request->get('banner_slide_id_add')) {
                list($bannerId) = explode(':', $bannerSlideId);
                $this->design->assign('banner_id', $bannerId);
            }
            
            $bannersImage = $bannersImagesHelper->getBannerImage($bannersImageId);
        }
        
        $banners = $bannersEntity->find();//todo

        $this->design->assign('banners_image', $bannersImage);
        $this->design->assign('banners', $banners);

        $this->response->setContent($this->design->fetch('banners_image.tpl'));
    }
    
}
