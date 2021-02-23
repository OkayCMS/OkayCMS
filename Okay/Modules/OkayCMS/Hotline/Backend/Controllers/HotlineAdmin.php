<?php


namespace Okay\Modules\OkayCMS\Hotline\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Modules\OkayCMS\Hotline\Entities\HotlineFeedsEntity;
use Okay\Modules\OkayCMS\Hotline\Entities\HotlineRelationsEntity;
use Okay\Modules\OkayCMS\Hotline\Helpers\BackendHotlineHelper;

class HotlineAdmin extends IndexAdmin
{

    public function fetch(
        CategoriesEntity       $categoriesEntity,
        BrandsEntity           $brandsEntity,
        FeaturesEntity         $featuresEntity,
        BackendHotlineHelper   $backendHotlineHelper,
        HotlineRelationsEntity $relationsEntity,
        HotlineFeedsEntity     $feedsEntity
    ) {
        if ($this->request->method('post')) {
            $postFeeds = $this->request->post('feeds');

            if ($errors = $backendHotlineHelper->validateFeeds($postFeeds)) {
                $this->design->assign('errors', $errors);
            } else {
                $this->settings->set('okaycms__hotline__company', $this->request->post('okaycms__hotline__company'));
                $this->settings->set('okaycms__hotline__country_of_origin', $this->request->post('okaycms__hotline__country_of_origin'));
                $this->settings->set('okaycms__hotline__guarantee_manufacturer', $this->request->post('okaycms__hotline__guarantee_manufacturer'));
                $this->settings->set('okaycms__hotline__guarantee_shop', $this->request->post('okaycms__hotline__guarantee_shop'));

                $postRelatedCategories = $this->request->post('related_categories');
                $postRelatedBrands = $this->request->post('related_brands');

                $backendHotlineHelper->updateFeeds($postFeeds);
                $backendHotlineHelper->updateRelatedCategories($postRelatedCategories);
                $backendHotlineHelper->updateRelatedBrands($postRelatedBrands);
                $backendHotlineHelper->updateRelatedProducts();
                $backendHotlineHelper->updateNotRelatedProducts();

                if ($this->request->post('add_feed')) {
                    $backendHotlineHelper->addFeed();
                } else if ($feedId = $this->request->post('remove_feed')) {
                    $backendHotlineHelper->removeFeed($feedId);
                } else if ($feedId = $this->request->post('add_all_categories')) {
                    $backendHotlineHelper->addAllCategories($feedId);
                } else if($feedId = $this->request->post('remove_all_categories')) {
                    $relationsEntity->removeAllCategoriesByFeedId($feedId);
                } else if ($feedId = $this->request->post('add_all_brands')) {
                    $backendHotlineHelper->addAllBrands($feedId);
                } else if($feedId = $this->request->post('remove_all_brands')) {
                    $relationsEntity->removeAllBrandsByFeedId($feedId);
                }

                $this->updateCheckboxes();
            }
        }

        $allFeeds                = $feedsEntity->find();
        $allCategories           = $categoriesEntity->getCategoriesTree();
        $allBrands               = $brandsEntity->find(['limit' => $brandsEntity->count()]);
        $allFeatures             = $featuresEntity->find();
        $allRelatedCategoriesIds = $backendHotlineHelper->getAllRelatedCategoriesIds();
        $allRelatedBrandsIds     = $backendHotlineHelper->getAllRelatedBrandsIds();
        $allRelatedProducts      = $backendHotlineHelper->getAllRelatedProducts();
        $allNotRelatedProducts   = $backendHotlineHelper->getAllNotRelatedProducts();

        $this->design->assign('feeds', $allFeeds);
        $this->design->assign('categories', $allCategories);
        $this->design->assign('brands', $allBrands);
        $this->design->assign('features', $allFeatures);
        $this->design->assign('allRelatedCategoriesIds', $allRelatedCategoriesIds);
        $this->design->assign('allRelatedBrandsIds', $allRelatedBrandsIds);
        $this->design->assign('related_products', $allRelatedProducts);
        $this->design->assign('not_related_products', $allNotRelatedProducts);

        $this->response->setContent($this->design->fetch('hotline_xml.tpl'));
    }

    private function updateCheckboxes()
    {

        $this->settings->set('okaycms__hotline__upload_only_available_to_hotline', $this->request->post('okaycms__hotline__upload_only_available_to_hotline', 'integer'));
        $this->settings->set('okaycms__hotline__use_full_description_to_hotline', $this->request->post('okaycms__hotline__use_full_description_to_hotline', 'integer'));
        $this->settings->set('okaycms__hotline__no_export_without_price', $this->request->post('okaycms__hotline__no_export_without_price', 'integer'));
        $this->settings->set('okaycms__hotline__pickup', $this->request->post('okaycms__hotline__pickup', 'integer'));
        $this->settings->set('okaycms__hotline__store', $this->request->post('okaycms__hotline__store', 'integer'));
        $this->settings->set('okaycms__hotline__upload_without_images', $this->request->post('okaycms__hotline__upload_without_images', 'integer'));
        
    }
}
