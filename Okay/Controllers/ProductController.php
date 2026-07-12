<?php

namespace Okay\Controllers;

use Okay\Core\BrowsedProducts;
use Okay\Core\Router;
use Okay\Entities\ProductsEntity;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\BlogEntity;
use Okay\Helpers\BlogHelper;
use Okay\Helpers\CommentsHelper;
use Okay\Helpers\MetadataHelpers\ProductMetadataHelper;
use Okay\Helpers\ProductsHelper;
use Okay\Helpers\RelatedProductsHelper;
use Okay\Helpers\ValidateHelper;

class ProductController extends AbstractController
{
    /*Отображение товара*/
    /**
     * @param string $url
     * @param string $variantId
     */
    public function render(
        ProductsEntity $productsEntity,
        BrandsEntity $brandsEntity,
        CategoriesEntity $categoriesEntity,
        ProductsHelper $productsHelper,
        BlogEntity $blogEntity,
        BlogHelper $blogHelper,
        CommentsHelper $commentsHelper,
        RelatedProductsHelper $relatedProductsHelper,
        ProductMetadataHelper $productMetadataHelper,
        BrowsedProducts $browsedProducts,
        $url,
        $variantId = ''
    ) {

        if (empty($url)) {
            return false;
        }

        // Выбираем товар из базы
        $product = $productsEntity->get((string)$url);
        //метод можно расширять и отменить либо переопределить дальнейшую логику работы контроллера
        if (($setProduct = $productsHelper->setProduct($product)) !== null) {
            return $setProduct;
        }

        //lastModify
        $this->response->setHeaderLastModify($product->last_modify);

        $product = $productsHelper->attachProductData($product);
        /** @var object{id: int, url: string, brand_id: int|string|null, main_category_id: int|string|null, position: mixed, variants: non-empty-array<int|string, object{id: int|string, product_id: int|string}&\stdClass>, variant?: object{id: int|string, product_id: int|string}&\stdClass}&\stdClass $product */

        // Вариант по умолчанию
        if (!empty($variantId)) {
            if (!isset($product->variants[$variantId])) {
                return false;
            }
            $product->variant = $product->variants[$variantId];
        } elseif (($vId = $this->request->get('variant', 'integer')) > 0 && isset($product->variants[$vId])) {
            $product->variant = $product->variants[$vId];
        } else {
            $product->variant = reset($product->variants);
        }

        // Комментарии к товару
        $commentsHelper->addCommentProcedure('product', $product->id);
        $commentsFilter = $commentsHelper->getCommentsFilter('product', $product->id);
        $commentsSort = $commentsHelper->getCurrentSort();
        $comments = $commentsHelper->getList($commentsFilter, $commentsSort);
        $comments = $commentsHelper->attachAnswers($comments);
        $this->design->assign('comments', $comments);

        // Связанные товары
        $relatedProducts = $relatedProductsHelper->getRelatedProductsList($productsEntity, ['product_id' => $product->id]);
        $this->design->assign('related_products', $relatedProducts);

        //Связянные статьи для товара
        $relatedPosts = $blogEntity->getRelatedProducts(['product_id' => $product->id]);
        if (!empty($relatedPosts)) {
            $filterPost['visible'] = 1;
            foreach ($relatedPosts as $r_post) {
                $filterPost['id'][] = $r_post->post_id;
            }
            $posts = $blogHelper->getList($filterPost);
            $this->design->assign('related_posts', $posts);
        }

        $this->design->assign('product', $product);

        // Категория и бренд товара
        $brand = $brandsEntity->get(intval($product->brand_id));
        if (!empty($brand) && $brand->visible) {
            $this->design->assign('brand', $brand);
        }

        $category = $categoriesEntity->get((int)$product->main_category_id);
        $this->design->assign('category', $category);

        // Соседние товары
        if (!empty($category)) {
            $neighborsProducts = $productsEntity->getNeighborsProducts($category->id, $product->position);
            $this->design->assign('next_product', $neighborsProducts['next']);
            $this->design->assign('prev_product', $neighborsProducts['prev']);
        }

        $browsedProducts->addItem($product->id);

        $this->design->assign('canonical', Router::generateUrl('product', ['url' => $product->url], true));

        $productMetadataHelper->setUp($product, $category, $brand);
        $this->setMetadataHelper($productMetadataHelper);

        $this->response->setContent('product.tpl');
    }

    public function rating(ProductsEntity $productsEntity, ValidateHelper $validateHelper)
    {
        if (!$this->request->isPost()) {
            $this->response->setStatusCode(405);
            $this->response->setContent(json_encode(-1), RESPONSE_JSON);
            return;
        }

        if ($validateHelper->getCustomerCsrfError($this->request->post('customer_csrf_token')) !== null) {
            $this->response->setStatusCode(403);
            $this->response->setContent(json_encode(-1), RESPONSE_JSON);
            return;
        }

        $ratingValue = $this->request->post('rating');
        if (($productInputId = $this->request->post('id')) && is_numeric($ratingValue)) {
            $productId = intval(str_replace('product_', '', (string)$productInputId));
            $rating = floatval($ratingValue);

            if (!isset($_SESSION['rating_ids'])) {
                $_SESSION['rating_ids'] = [];
            }
            if (!in_array($productId, $_SESSION['rating_ids'])) {
                $product = $productsEntity->cols([
                    'rating',
                    'votes',
                ])->get($productId);
                if (!empty($product)) {
                    $rate = ($product->rating * $product->votes + $rating) / ($product->votes + 1);

                    $productsEntity->update($productId, ['rating' => $rate, 'votes' => ($product->votes + 1)]);

                    $_SESSION['rating_ids'][] = $productId;
                    $this->response->setContent(json_encode($rate), RESPONSE_JSON);
                } else {
                    $this->response->setContent(json_encode(-1), RESPONSE_JSON);
                }
            } else {
                $this->response->setContent(json_encode(0), RESPONSE_JSON);
            }
        } else {
            $this->response->setContent(json_encode(-1), RESPONSE_JSON);
        }
    }
}
