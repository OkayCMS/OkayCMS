<?php


namespace Okay\Modules\OkayCMS\Rozetka\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Modules\OkayCMS\Rozetka\Entities\RozetkaFeedsEntity;
use Okay\Modules\OkayCMS\Rozetka\Entities\RozetkaRelationsEntity;
use Okay\Modules\OkayCMS\Rozetka\Helpers\BackendRozetkaHelper;

class RozetkaXmlAdmin extends IndexAdmin
{

    public function fetch(
        CategoriesEntity       $categoriesEntity,
        BrandsEntity           $brandsEntity,
        BackendRozetkaHelper   $backendRozetkaHelper,
        RozetkaRelationsEntity $relationsEntity,
        RozetkaFeedsEntity     $feedsEntity
    ) {
        if ($this->request->method('post')) {
            $postFeeds = $this->request->post('feeds');

            if ($errors = $backendRozetkaHelper->validateFeeds($postFeeds)) {
                $this->design->assign('errors', $errors);
            } else {

                $this->settings->update('okaycms__rozetka_xml__company', $this->request->post('okaycms__rozetka_xml__company'));
                
                $postRelatedCategories = $this->request->post('related_categories');
                $postRelatedBrands = $this->request->post('related_brands');

                $backendRozetkaHelper->updateFeeds($postFeeds);
                $backendRozetkaHelper->updateRelatedCategories($postRelatedCategories);
                $backendRozetkaHelper->updateRelatedBrands($postRelatedBrands);
                $backendRozetkaHelper->updateRelatedProducts();
                $backendRozetkaHelper->updateNotRelatedProducts();

                if ($this->request->post('add_feed')) {
                    $backendRozetkaHelper->addFeed();
                } else if ($feedId = $this->request->post('remove_feed')) {
                    $backendRozetkaHelper->removeFeed($feedId);
                } else if ($feedId = $this->request->post('add_all_categories')) {
                    $backendRozetkaHelper->addAllCategories($feedId);
                } else if($feedId = $this->request->post('remove_all_categories')) {
                    $relationsEntity->removeAllCategoriesByFeedId($feedId);
                } else if ($feedId = $this->request->post('add_all_brands')) {
                    $backendRozetkaHelper->addAllBrands($feedId);
                } else if($feedId = $this->request->post('remove_all_brands')) {
                    $relationsEntity->removeAllBrandsByFeedId($feedId);
                }

                $this->updateCheckboxes();
            }
        }

        $allFeeds                = $feedsEntity->find();
        $allCategories           = $categoriesEntity->getCategoriesTree();
        $allBrands               = $brandsEntity->find(['limit' => $brandsEntity->count()]);
        $allRelatedCategoriesIds = $backendRozetkaHelper->getAllRelatedCategoriesIds();
        $allRelatedBrandsIds     = $backendRozetkaHelper->getAllRelatedBrandsIds();
        $allRelatedProducts      = $backendRozetkaHelper->getAllRelatedProducts();
        $allNotRelatedProducts   = $backendRozetkaHelper->getAllNotRelatedProducts();

        $this->design->assign('feeds', $allFeeds);
        $this->design->assign('categories', $allCategories);
        $this->design->assign('brands', $allBrands);
        $this->design->assign('allRelatedCategoriesIds', $allRelatedCategoriesIds);
        $this->design->assign('allRelatedBrandsIds', $allRelatedBrandsIds);
        $this->design->assign('related_products', $allRelatedProducts);
        $this->design->assign('not_related_products', $allNotRelatedProducts);

        $this->response->setContent($this->design->fetch('rozetka_xml.tpl'));
    }

    private function updateCheckboxes()
    {

        $this->settings->set('upload_only_available_to_rozetka', $this->request->post('upload_non_available', 'integer'));
        $this->settings->set('use_full_description_in_upload_rozetka', $this->request->post('full_description', 'integer'));
        $this->settings->set('okaycms__rozetka_xml__upload_without_images', $this->request->post('okaycms__rozetka_xml__upload_without_images', 'integer'));
        
    }
    
}
