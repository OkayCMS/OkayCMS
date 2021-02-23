<?php


namespace Okay\Modules\OkayCMS\GoogleMerchant\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Modules\OkayCMS\GoogleMerchant\Entities\GoogleMerchantFeedsEntity;
use Okay\Modules\OkayCMS\GoogleMerchant\Entities\GoogleMerchantRelationsEntity;
use Okay\Modules\OkayCMS\GoogleMerchant\Helpers\BackendGoogleMerchantHelper;

class GoogleMerchantAdmin extends IndexAdmin
{

    public function fetch(
        CategoriesEntity              $categoriesEntity,
        BrandsEntity                  $brandsEntity,
        FeaturesEntity                $featuresEntity,
        GoogleMerchantFeedsEntity     $feedsEntity,
        GoogleMerchantRelationsEntity $relationsEntity,
        BackendGoogleMerchantHelper   $backendGoogleMerchantHelper
    ) {
        if ($this->request->method('post')) {
            $postFeeds = $this->request->post('feeds');

            if ($errors = $backendGoogleMerchantHelper->validateFeeds($postFeeds)) {
                $this->design->assign('errors', $errors);
            } else {
                $this->settings->set('okaycms__google_merchant__company', $this->request->post('okaycms__google_merchant__company'));
                $this->settings->set('okaycms__google_merchant__color', $this->request->post('okaycms__google_merchant__color'));

                $postRelatedCategories = $this->request->post('related_categories');
                $postRelatedBrands = $this->request->post('related_brands');

                $backendGoogleMerchantHelper->updateFeeds($postFeeds);
                $backendGoogleMerchantHelper->updateRelatedCategories($postRelatedCategories);
                $backendGoogleMerchantHelper->updateRelatedBrands($postRelatedBrands);
                $backendGoogleMerchantHelper->updateRelatedProducts();
                $backendGoogleMerchantHelper->updateNotRelatedProducts();

                if ($this->request->post('add_feed')) {
                    $backendGoogleMerchantHelper->addFeed();
                } else if ($feedId = $this->request->post('remove_feed')) {
                    $backendGoogleMerchantHelper->removeFeed($feedId);
                } else if ($feedId = $this->request->post('add_all_categories')) {
                    $backendGoogleMerchantHelper->addAllCategories($feedId);
                } else if($feedId = $this->request->post('remove_all_categories')) {
                    $relationsEntity->removeAllCategoriesByFeedId($feedId);
                } else if ($feedId = $this->request->post('add_all_brands')) {
                    $backendGoogleMerchantHelper->addAllBrands($feedId);
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
        $allRelatedCategoriesIds = $backendGoogleMerchantHelper->getAllRelatedCategoriesIds();
        $allRelatedBrandsIds     = $backendGoogleMerchantHelper->getAllRelatedBrandsIds();
        $allRelatedProducts      = $backendGoogleMerchantHelper->getAllRelatedProducts();
        $allNotRelatedProducts   = $backendGoogleMerchantHelper->getAllNotRelatedProducts();

        $this->design->assign('feeds', $allFeeds);
        $this->design->assign('categories', $allCategories);
        $this->design->assign('brands', $allBrands);
        $this->design->assign('features', $allFeatures);
        $this->design->assign('allRelatedCategoriesIds', $allRelatedCategoriesIds);
        $this->design->assign('allRelatedBrandsIds', $allRelatedBrandsIds);
        $this->design->assign('related_products', $allRelatedProducts);
        $this->design->assign('not_related_products', $allNotRelatedProducts);

        $this->response->setContent($this->design->fetch('google_merchant.tpl'));
    }

    private function updateCheckboxes()
    {
        $this->updateSingleCheckbox('okaycms__google_merchant__upload_non_exists_products_to_google');
        $this->updateSingleCheckbox('okaycms__google_merchant__use_full_description_to_google');
        $this->updateSingleCheckbox('okaycms__google_merchant__has_manufacturer_warranty');
        $this->updateSingleCheckbox('okaycms__google_merchant__no_export_without_price');
        $this->updateSingleCheckbox('okaycms__google_merchant__pickup');
        $this->updateSingleCheckbox('okaycms__google_merchant__store');
        $this->updateSingleCheckbox('okaycms__google_merchant__delivery_disallow');
        $this->updateSingleCheckbox('okaycms__google_merchant__adult');
        $this->updateSingleCheckbox('okaycms__google_merchant__use_variant_name_like_size');
        $this->updateSingleCheckbox('okaycms__google_merchant__upload_without_images');
    }

    private function updateSingleCheckbox($name)
    {
        if ($this->request->post($name, 'integer')) {
            $this->settings->set($name, 1);
        } else {
            $this->settings->set($name, 0);
        }
    }
}
