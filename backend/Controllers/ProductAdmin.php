<?php 


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendCategoriesHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Entities\RouterCacheEntity;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CurrenciesEntity;
use Okay\Admin\Requests\BackendProductsRequest;
use Okay\Admin\Helpers\BackendProductsHelper;
use Okay\Admin\Helpers\BackendVariantsHelper;
use Okay\Admin\Helpers\BackendFeaturesHelper;
use Okay\Admin\Helpers\BackendSpecialImagesHelper;

class ProductAdmin extends IndexAdmin
{

    public function fetch(
        BackendCategoriesHelper    $backendCategoriesHelper,
        BrandsEntity               $brandsEntity,
        CurrenciesEntity           $currenciesEntity,
        BackendProductsRequest     $productRequest,
        BackendProductsHelper      $backendProductsHelper,
        BackendVariantsHelper      $backendVariantsHelper,
        BackendFeaturesHelper      $backendFeaturesHelper,
        BackendSpecialImagesHelper $backendSpecialImagesHelper,
        BackendValidateHelper      $backendValidateHelper,
        RouterCacheEntity          $routerCacheEntity
    ) {

        if ($this->request->method('post') && !empty($_POST)) {
            $product           = $productRequest->postProduct();
            $productVariants   = $productRequest->postVariants();
            $productCategories = $productRequest->postCategories();
            $relatedProducts   = $productRequest->postRelatedProducts();

            if ($error = $backendValidateHelper->getProductValidateError($product, $productCategories)) {
                $this->design->assign('message_error', $error);
            } else {
                // Товар
                if (empty($product->id)) {
                    $preparedProduct = $backendProductsHelper->prepareAdd($product);
                    $addedProductId  = $backendProductsHelper->add($preparedProduct);
                    $product = $backendProductsHelper->getProduct($addedProductId);

                    $this->postRedirectGet->storeMessageSuccess('added');
                    $this->postRedirectGet->storeNewEntityId($product->id);
                } else {
                    $preparedProduct = $backendProductsHelper->prepareUpdate($product);
                    $backendProductsHelper->update($preparedProduct);
                    $product = $backendProductsHelper->getProduct($preparedProduct->id);

                    $routerCacheEntity->deleteByUrl(RouterCacheEntity::TYPE_PRODUCT, $product->url);
                    
                    $this->postRedirectGet->storeMessageSuccess('updated');
                }

                // Категории
                $productCategories = $backendProductsHelper->prepareUpdateProductsCategories($product, $productCategories);
                $backendProductsHelper->updateProductsCategories($product, $productCategories);

                // Варианты
                $productVariants = $backendVariantsHelper->prepareUpdateVariants($productVariants);
                $backendVariantsHelper->updateVariants($product, $productVariants);

                // Картинки
                $images        = $productRequest->postImages();
                $droppedImages = $productRequest->fileDroppedImages();
                $backendProductsHelper->updateImages($product, $images, $droppedImages);
                
                // Промо-изображения
                $specImages        = $productRequest->postSpecialImages();
                $specDroppedImages = $productRequest->fileDroppedSpecialImages();
                $backendProductsHelper->updateSpecialImages($product, $specImages, $specDroppedImages);

                // Характеристики
                $featuresValues     = $productRequest->postFeaturesValues();
                $featuresValuesText = $productRequest->postFeaturesValuesText();
                $newFeaturesNames   = $productRequest->postNewFeaturesNames();
                $newFeaturesValues  = $productRequest->postNewFeaturesValues();
                $backendFeaturesHelper->updateProductFeatures(
                    $product,
                    $featuresValues,
                    $featuresValuesText,
                    $newFeaturesNames,
                    $newFeaturesValues,
                    $productCategories
                );

                // Связанные товары
                $relatedProducts = $backendProductsHelper->prepareUpdateRelatedProducts($product, $relatedProducts);
                $backendProductsHelper->updateRelatedProducts($product, $relatedProducts);

                $this->postRedirectGet->redirect();
            }
        } else {
            $id      = $this->request->get('id', 'integer');
            $product = $backendProductsHelper->getProduct($id);

            $productVariants   = $backendVariantsHelper->findProductVariants($product);
            $relatedProducts   = $backendProductsHelper->findRelatedProducts($product);
        }

        $categoriesTree    = $backendCategoriesHelper->getCategoriesTree();
        $productImages     = $backendProductsHelper->findProductImages($product);
        $productCategories = $backendProductsHelper->findProductCategories($product);
        $features          = $backendFeaturesHelper->findCategoryFeatures($productCategories, $categoriesTree);
        $featuresValues    = $backendFeaturesHelper->findProductFeaturesValues($product);
        $specialImages     = $backendSpecialImagesHelper->findSpecialImages();

        if (empty($product->brand_id) && $brand_id = $this->request->get('brand_id')) {
            $product->brand_id = $brand_id;
        }

        $this->design->assign('product',            $product);
        $this->design->assign('special_images',     $specialImages);
        $this->design->assign('product_categories', $productCategories);
        $this->design->assign('product_variants',   $productVariants);
        $this->design->assign('product_images',     $productImages);
        $this->design->assign('features',           $features);
        $this->design->assign('features_values',    $featuresValues);
        $this->design->assign('related_products',   $relatedProducts);

        $brandsCount = $brandsEntity->count();
        $brands = $brandsEntity->find(['limit' => $brandsCount]);

        $this->design->assign('brands',     $brands);
        $this->design->assign('categories', $categoriesTree);
        $this->design->assign('currencies', $currenciesEntity->find());

        $this->response->setContent($this->design->fetch('product.tpl'));
    }
}
