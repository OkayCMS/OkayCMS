<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendBrandsHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendBrandsRequest;

class BrandAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendBrandsRequest  $brandsRequest,
        BackendValidateHelper $backendValidateHelper,
        BackendBrandsHelper   $backendBrandsHelper
    ) {
        if ($this->request->method('post')) {
            $brand = $brandsRequest->postBrand();

            if ($error = $backendValidateHelper->getBrandsValidateError($brand)) {
                $this->design->assign('message_error', $error);
            } else {
                // Бренд
                if (empty($brand->id)) {
                    $preparedBrand = $backendBrandsHelper->prepareAdd($brand);
                    $brand->id     = $backendBrandsHelper->add($preparedBrand);

                    $this->postRedirectGet->storeMessageSuccess('added');
                    $this->postRedirectGet->storeNewEntityId($brand->id);
                } else {
                    $preparedBrand = $backendBrandsHelper->prepareUpdate($brand);
                    $backendBrandsHelper->update($preparedBrand->id, $preparedBrand);

                    $this->postRedirectGet->storeMessageSuccess('updated');
                }

                // Картинка
                if ($brandsRequest->postDeleteImage()) {
                    $backendBrandsHelper->deleteImage($brand);
                }

                if ($image = $brandsRequest->fileImage()) {
                    $backendBrandsHelper->uploadImage($image, $brand);
                }

                $this->postRedirectGet->redirect();
            }
        } else {
            $brandId = $this->request->get('id', 'integer');
            $brand   = $backendBrandsHelper->getBrand($brandId);
        }

        $this->design->assign('brand', $brand);
        $this->response->setContent($this->design->fetch('brand.tpl'));
    }
    
}
