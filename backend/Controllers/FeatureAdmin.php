<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendFeaturesHelper;
use Okay\Admin\Helpers\BackendFeaturesValuesHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Helpers\BackendCategoriesHelper;
use Okay\Admin\Requests\BackendFeaturesRequest;
use Okay\Admin\Requests\BackendFeaturesValuesRequest;

class FeatureAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendFeaturesRequest       $featuresRequest,
        BackendFeaturesValuesRequest $featuresValuesRequest,
        BackendValidateHelper        $backendValidateHelper,
        BackendFeaturesHelper        $backendFeaturesHelper,
        BackendCategoriesHelper      $backendCategoriesHelper,
        BackendFeaturesValuesHelper  $backendFeaturesValuesHelper
    ) {
        if ($this->request->method('post')) {
            $feature           = $featuresRequest->postFeature();
            $featureCategories = $featuresRequest->postFeatureCategories();

            $neededPostRedirectGet = false;
            if ($error = $backendValidateHelper->getFeatureValidateError($feature)) {
                $this->design->assign('message_error', $error);
            } else {
                /*Добавление/Обновление свойства*/
                if (empty($feature->id)) {
                    $feature   = $backendFeaturesHelper->prepareAdd($feature);
                    $feature->id = $backendFeaturesHelper->add($feature);

                    $this->postRedirectGet->storeMessageSuccess('added');
                    $this->postRedirectGet->storeNewEntityId($feature->id);
                } else {
                    $feature = $backendFeaturesHelper->prepareUpdate($feature);
                    $backendFeaturesHelper->update($feature->id, $feature);

                    $this->postRedirectGet->storeMessageSuccess('updated');
                }

                $feature = $backendFeaturesHelper->getFeature($feature->id);
                $backendFeaturesHelper->updateFeatureCategories($feature->id, $featureCategories);
                $neededPostRedirectGet = true;
            }

            $toIndexAllValues = $featuresValuesRequest->postToIndexAllValues();
            if (isset($toIndexAllValues) && $feature->id) {
                $backendFeaturesValuesHelper->toIndexAllValues($feature);
            }

            $featuresValues = $featuresRequest->postFeaturesValues();

            if ($valuesToDelete = $featuresRequest->postValuesToDelete()) {
                $featuresValues = $backendFeaturesValuesHelper->deleteSelectedValues($valuesToDelete, $featuresValues);
            }

            $backendFeaturesValuesHelper->sortFeatureValuePositions($feature, $featuresValues);

            if ($this->request->post('alphabet_sort_values')) {
                $backendFeaturesValuesHelper->sortFeatureValuePositionsAlphabet($feature);
            }

            $unionMainValueId   = $featuresRequest->postUnionMainValueId();
            $unionSecondValueId = $featuresRequest->postUnionSecondValueId();
            if (!empty($unionMainValueId) && !empty($unionSecondValueId)) {
                $backendFeaturesValuesHelper->unionValues($unionMainValueId, $unionSecondValueId);
            }

            if ($neededPostRedirectGet) {
                $this->postRedirectGet->redirect();
            }
        } else {
            $featureId = $this->request->get('id', 'integer');
            $feature   = $backendFeaturesHelper->getFeature($featureId);
        }

        if (!empty($feature->id)) {
            $featuresValuesFilter = $backendFeaturesValuesHelper->buildValuesFilter($feature);
            $this->design->assign('current_limit', $featuresValuesFilter['limit']);

            $featuresValuesFilter = $backendFeaturesValuesHelper->showAllValuesIfSelected($featuresValuesFilter);
            $featuresValuesFilter = $backendFeaturesValuesHelper->makePagination($featuresValuesFilter);

            $action = $featuresValuesRequest->postAction();
            $ids    = $featuresValuesRequest->postCheck();
            if ($action == 'move_to_page' && !empty($ids)) {
                $targetPage = $featuresValuesRequest->postTargetPage();
                $backendFeaturesValuesHelper->moveToPage($ids, $targetPage, $feature, $featuresValuesFilter);
            }

            $featuresValues = $backendFeaturesValuesHelper->findFeaturesValues($featuresValuesFilter);
            $productsCounts = $backendFeaturesValuesHelper->getProductsCountsByValues($featuresValuesFilter, $featuresValues);

            $this->design->assign('feature_values_count', $backendFeaturesValuesHelper->count($featuresValuesFilter));
            $this->design->assign('pages_count',          $backendFeaturesValuesHelper->countPages($featuresValuesFilter, $feature));
            $this->design->assign('current_page',         $featuresValuesFilter['page']);
            $this->design->assign('products_counts',      $productsCounts);
            $this->design->assign('features_values',      $featuresValues);
        }

        $featureCategories = $backendFeaturesHelper->getFeatureCategories($feature);
        $categories        = $backendCategoriesHelper->getCategoriesTree();

        $this->design->assign('categories', $categories);
        $this->design->assign('feature', $feature);
        $this->design->assign('feature_categories', $featureCategories);

        $this->response->setContent($this->design->fetch('feature.tpl'));
    }

}
