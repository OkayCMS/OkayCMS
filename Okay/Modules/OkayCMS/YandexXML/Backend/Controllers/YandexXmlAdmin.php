<?php


namespace Okay\Modules\OkayCMS\YandexXML\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Modules\OkayCMS\YandexXML\Entities\YandexXMLFeedsEntity;
use Okay\Modules\OkayCMS\YandexXML\Entities\YandexXMLRelationsEntity;
use Okay\Modules\OkayCMS\YandexXML\Helpers\BackendYandexXMLHelper;

class YandexXmlAdmin extends IndexAdmin
{

    public function fetch(
        CategoriesEntity         $categoriesEntity,
        BrandsEntity             $brandsEntity,
        FeaturesEntity           $featuresEntity,
        YandexXMLFeedsEntity     $feedsEntity,
        YandexXMLRelationsEntity $relationsEntity,
        BackendYandexXMLHelper   $backendYandexXMLHelper
    ) {
        if ($this->request->method('post')) {
            $postFeeds = $this->request->post('feeds');

            if ($errors = $backendYandexXMLHelper->validateFeeds($postFeeds)) {
                $this->design->assign('errors', $errors);
            } else {
                $this->settings->set('okaycms__yandex_xml__company', $this->request->post('okaycms__yandex_xml__company'));
                $this->settings->set('okaycms__yandex_xml__country_of_origin', $this->request->post('okaycms__yandex_xml__country_of_origin'));
                $salesNotes = $this->request->post('okaycms__yandex_xml__sales_notes');
                $this->settings->set('okaycms__yandex_xml__sales_notes', mb_substr($salesNotes, 0, 50));

                $postRelatedCategories = $this->request->post('related_categories');
                $postRelatedBrands = $this->request->post('related_brands');

                $backendYandexXMLHelper->updateFeeds($postFeeds);
                $backendYandexXMLHelper->updateRelatedCategories($postRelatedCategories);
                $backendYandexXMLHelper->updateRelatedBrands($postRelatedBrands);
                $backendYandexXMLHelper->updateRelatedProducts();
                $backendYandexXMLHelper->updateNotRelatedProducts();

                if ($this->request->post('add_feed')) {
                    $backendYandexXMLHelper->addFeed();
                } else if ($feedId = $this->request->post('remove_feed')) {
                    $backendYandexXMLHelper->removeFeed($feedId);
                } else if ($feedId = $this->request->post('add_all_categories')) {
                    $backendYandexXMLHelper->addAllCategories($feedId);
                } else if($feedId = $this->request->post('remove_all_categories')) {
                    $relationsEntity->removeAllCategoriesByFeedId($feedId);
                } else if ($feedId = $this->request->post('add_all_brands')) {
                    $backendYandexXMLHelper->addAllBrands($feedId);
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
        $allRelatedCategoriesIds = $backendYandexXMLHelper->getAllRelatedCategoriesIds();
        $allRelatedBrandsIds     = $backendYandexXMLHelper->getAllRelatedBrandsIds();
        $allRelatedProducts      = $backendYandexXMLHelper->getAllRelatedProducts();
        $allNotRelatedProducts   = $backendYandexXMLHelper->getAllNotRelatedProducts();

        $this->design->assign('feeds', $allFeeds);
        $this->design->assign('categories', $allCategories);
        $this->design->assign('brands', $allBrands);
        $this->design->assign('features', $allFeatures);
        $this->design->assign('allRelatedCategoriesIds', $allRelatedCategoriesIds);
        $this->design->assign('allRelatedBrandsIds', $allRelatedBrandsIds);
        $this->design->assign('related_products', $allRelatedProducts);
        $this->design->assign('not_related_products', $allNotRelatedProducts);

        $this->response->setContent($this->design->fetch('yandex_xml.tpl'));
    }

    private function updateCheckboxes()
    {
        $this->settings->set('okaycms__yandex_xml__upload_only_available_to_yandex', $this->request->post('okaycms__yandex_xml__upload_only_available_to_yandex', 'integer'));
        $this->settings->set('okaycms__yandex_xml__use_full_description_to_yandex', $this->request->post('okaycms__yandex_xml__use_full_description_to_yandex', 'integer'));
        $this->settings->set('okaycms__yandex_xml__has_manufacturer_warranty', $this->request->post('okaycms__yandex_xml__has_manufacturer_warranty', 'integer'));
        $this->settings->set('okaycms__yandex_xml__no_export_without_price', $this->request->post('okaycms__yandex_xml__no_export_without_price', 'integer'));
        $this->settings->set('okaycms__yandex_xml__pickup', $this->request->post('okaycms__yandex_xml__pickup', 'integer'));
        $this->settings->set('okaycms__yandex_xml__store', $this->request->post('okaycms__yandex_xml__store', 'integer'));
        $this->settings->set('okaycms__yandex_xml__delivery_disallow', $this->request->post('okaycms__yandex_xml__delivery_disallow', 'integer'));
        $this->settings->set('okaycms__yandex_xml__adult', $this->request->post('okaycms__yandex_xml__adult', 'integer'));
        $this->settings->set('okaycms__yandex_xml__upload_without_images', $this->request->post('okaycms__yandex_xml__upload_without_images', 'integer'));
        
    }
}
