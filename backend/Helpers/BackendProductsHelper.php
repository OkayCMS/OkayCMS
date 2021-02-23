<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Image;
use Okay\Core\Config;
use Okay\Core\Request;
use Okay\Core\Database;
use Okay\Core\QueryFactory;
use Okay\Core\EntityFactory;
use Okay\Entities\BrandsEntity;
use Okay\Entities\ImagesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\RouterCacheEntity;
use Okay\Entities\VariantsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\SpecialImagesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendProductsHelper
{
    private $db;
    private $config;
    private $request;
    private $imageCore;
    private $queryFactory;

    /** @var BrandsEntity */
    private $brandsEntity;

    /** @var ImagesEntity */
    private $imagesEntity;
    
    /** @var ProductsEntity */
    private $productsEntity;
    
    /** @var VariantsEntity */
    private $variantsEntity;

    /** @var CategoriesEntity */
    private $categoriesEntity;

    /** @var SpecialImagesEntity */
    private $specialImagesEntity;

    /** @var RouterCacheEntity */
    private $routerCacheEntity;


    public function __construct(
        EntityFactory $entityFactory,
        QueryFactory  $queryFactory,
        Database      $db,
        Image         $imageCore,
        Config        $config,
        Request       $request
    ) {
        $this->db                  = $db;
        $this->config              = $config;
        $this->request             = $request;
        $this->imageCore           = $imageCore;
        $this->queryFactory        = $queryFactory;
        $this->brandsEntity        = $entityFactory->get(BrandsEntity::class);
        $this->imagesEntity        = $entityFactory->get(ImagesEntity::class);
        $this->productsEntity      = $entityFactory->get(ProductsEntity::class);
        $this->variantsEntity      = $entityFactory->get(VariantsEntity::class);
        $this->categoriesEntity    = $entityFactory->get(CategoriesEntity::class);
        $this->specialImagesEntity = $entityFactory->get(SpecialImagesEntity::class);
        $this->routerCacheEntity   = $entityFactory->get(RouterCacheEntity::class);
    }

    public function getProduct($id)
    {
        $product = $this->productsEntity->get((int) $id);

        if (empty($product->id)) {
            // Сразу активен
            $product = new \stdClass();
            $product->visible = 1;
        }
        
        return ExtenderFacade::execute(__METHOD__, $product, func_get_args());
    }

    public function prepareAdd($product)
    {
        return ExtenderFacade::execute(__METHOD__, $product, func_get_args());
    }

    public function add($product)
    {
        if (!empty($product->brand_id)) {
            $this->brandsEntity->update($product->brand_id, ['last_modify'=>'now()']);
        }

        $insertId = $this->productsEntity->add($product);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdate($product)
    {
        return ExtenderFacade::execute(__METHOD__, $product, func_get_args());
    }

    public function update($product)
    {
        $oldBrandId = $this->productsEntity->cols(['brand_id'])->get((int)$product->id)->brand_id;

        if (!empty($product->brand_id) && $oldBrandId != $product->brand_id) {
            $this->brandsEntity->update($oldBrandId, ['last_modify'=>'now()']);
            $this->brandsEntity->update($product->brand_id, ['last_modify'=>'now()']);
        }

        $this->productsEntity->update($product->id, $product);

        $select = $this->queryFactory->newSelect();
        $select->cols(['category_id'])
            ->from('__products_categories')
            ->where('product_id=:product_id')
            ->bindValue('product_id', $product->id);

        $this->db->query($select);
        $cIds = $this->db->results('category_id');

        if (!empty($cIds)) {
            $this->categoriesEntity->update($cIds, ['last_modify' => 'now()']);
        }

        ExtenderFacade::execute(__METHOD__, $product, func_get_args());
    }

    public function prepareUpdateProductsCategories($product, $productCategories)
    {
        return ExtenderFacade::execute(__METHOD__, $productCategories, func_get_args());
    }

    public function updateProductsCategories($product, $productCategories)
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from('__products_categories')
            ->where('product_id=:product_id')
            ->bindValue('product_id', $product->id);

        $this->db->query($delete);
        if (is_array($productCategories)) {
            $i = 0;
            foreach($productCategories as $category) {
                $this->categoriesEntity->addProductCategory($product->id, $category->id, $i);
                $i++;
            }
            unset($i);
        }

        $mainCategory = reset($productCategories);
        $this->productsEntity->update($product->id, ['main_category_id'=>$mainCategory->id]);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function prepareUpdateRelatedProducts($product, $relatedProducts)
    {
        return ExtenderFacade::execute(__METHOD__, $relatedProducts, func_get_args());
    }

    public function updateRelatedProducts($product, $relatedProducts)
    {
        $this->productsEntity->deleteRelatedProduct($product->id);
        if (is_array($relatedProducts)) {
            $pos = 0;
            foreach($relatedProducts  as $i=>$relatedProduct) {
                $this->productsEntity->addRelatedProduct($product->id, $relatedProduct->related_id, $pos++);
            }
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function updateImages($product, $images, $droppedImages)
    {
        // Удаление изображений
        $currentImages = $this->imagesEntity->find(['product_id'=>$product->id]);
        foreach ($currentImages as $image) {
            if (!in_array($image->id, $images)) {
                $this->imagesEntity->delete($image->id);
            }
        }

        // Порядок изображений
        if ($images) {
            $i=0;
            foreach ($images as $id) {
                $this->imagesEntity->update($id, ['position'=>$i]);
                $i++;
            }
        }

        // Загрузка изображений drag-n-drop файлов
        if (!empty($droppedImages)) {
            foreach (array_keys($droppedImages['name']) as $key) {
                if ($filename = $this->imageCore->uploadImage($droppedImages['tmp_name'][$key], $droppedImages['name'][$key])) {
                    $image = new \stdClass();
                    $image->product_id = $product->id;
                    $image->filename = $filename;
                    $this->imagesEntity->add($image);
                }
            }
        }

        $productImages = $this->imagesEntity->find(['product_id' => $product->id]);
        $mainImage = reset($productImages);
        $mainImageId = $mainImage ? $mainImage->id : null;
        $this->productsEntity->update($product->id, ['main_image_id' => $mainImageId]);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function updateSpecialImages($product, $specialImages, $specDroppedImages)
    {
        // Удаление изображений
        $currentSpecialImages = $this->specialImagesEntity->find();
        if (!empty($currentSpecialImages)) {
            foreach ($currentSpecialImages as $image) {
                if (!in_array($image->id, $specialImages)) {
                    $this->specialImagesEntity->delete($image->id);
                }
            }
        }
        
        // Загрузка изображений из интернета и drag-n-drop файлов
        if (!empty($specDroppedImages)) {
            foreach (array_keys($specDroppedImages['name']) as $key) {
                $specialImagesFilename = $this->imageCore->uploadImage(
                    $specDroppedImages['tmp_name'][$key],
                    $specDroppedImages['name'][$key],
                    $this->config->get('special_images_dir')
                );

                if (!empty($specialImagesFilename)) {
                    $specialImage = new \stdClass();
                    $specialImage->filename = $specialImagesFilename;
                    $this->specialImagesEntity->add($specialImage);
                }
            }
        }

        // Порядок промо изображений
        if (!empty($specialImages)) {
            $i=0;
            foreach ($specialImages as $id) {
                $this->specialImagesEntity->update($id, ['position'=>$i]);
                $i++;
            }
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function findProductImages($product)
    {
        $productImages     = [];
        if (!empty($product->id)) {
            $productImages = $this->imagesEntity->find(['product_id' => $product->id]);
        }

        return ExtenderFacade::execute(__METHOD__, $productImages, func_get_args());
    }

    public function findProductCategories($product)
    {
        $productCategories = [];
        if (!empty($product->id)) {
            foreach ($this->categoriesEntity->getProductCategories($product->id) as $pc) {
                $productCategories[$pc->category_id] = $pc;
            }

            if (!empty($productCategories)) {
                foreach ($this->categoriesEntity->find(['id' => array_keys($productCategories)]) as $category) {
                    $productCategories[$category->id] = $category;
                }
            }
        }

        if (empty($productCategories)) {
            if ($category_id = $this->request->get('category_id')) {
                $category = $this->categoriesEntity->findOne(['id' => $category_id]);
                $productCategories[$category_id] = $category;
            } else {
                $productCategories = [];
            }
        }

        return ExtenderFacade::execute(__METHOD__, $productCategories, func_get_args());
    }

    public function findRelatedProducts($product)
    {
        $relatedProducts   = [];
        if (!empty($product->id)) {
            $relatedProducts = $this->productsEntity->getRelatedProducts(['product_id' => $product->id]);
            if (!empty($relatedProducts)) {
                $r_products = [];
                foreach ($relatedProducts as &$r_p) {
                    $r_products[$r_p->related_id] = &$r_p;
                }
                $tempProducts = $this->productsEntity->find(['id' => array_keys($r_products), 'limit' => count(array_keys($r_products))]);
                foreach ($tempProducts as $tempProduct) {
                    $r_products[$tempProduct->id] = $tempProduct;
                }

                $relatedProductsImages = $this->imagesEntity->find(['product_id' => array_keys($r_products)]);
                foreach ($relatedProductsImages as $image) {
                    $r_products[$image->product_id]->images[] = $image;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $relatedProducts, func_get_args());
    }

    public function getProductsSortName()
    {
        $sort = $this->request->get('sort', 'string', null);
        return ExtenderFacade::execute(__METHOD__, $sort, func_get_args());
    }
    
    public function buildFilter()
    {
        // Пагинация
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));

        if ($filter['limit'] = $this->request->get('limit', 'integer')) {
            $filter['limit'] = max(5, $filter['limit']);
            $filter['limit'] = min(100, $filter['limit']);
            $_SESSION['products_num_admin'] = $filter['limit'];
        } elseif (!empty($_SESSION['products_num_admin'])) {
            $filter['limit'] = $_SESSION['products_num_admin'];
        } else {
            $filter['limit'] = 25;
        }

        // Категории
        $categoryId = $this->request->get('category_id', 'integer');
        $category = $this->categoriesEntity->get($categoryId);
        if(!empty($categoryId) && !empty($category)) {
            $filter['category_id'] = $category->children;
        } elseif ($categoryId == -1) {
            $filter['without_category'] = 1;
        }

        // Бренды
        $brandId = $this->request->get('brand_id', 'integer');
        if($brandId && $brand = $this->brandsEntity->get($brandId)) {
            $filter['brand_id'] = $brand->id;
        } elseif ($brandId == -1) {
            $filter['brand_id'] = array(0);
        }

        if ($features = $this->request->get('features')) {
            $filter['features'] = $features;
        }

        // Фильтр по товарам
        if($f = $this->request->get('filter', 'string')) {
            if($f == 'featured') {
                $filter['featured'] = 1;
            } elseif($f == 'discounted') {
                $filter['discounted'] = 1;
            } elseif($f == 'visible') {
                $filter['visible'] = 1;
            } elseif($f == 'hidden') {
                $filter['visible'] = 0;
            } elseif($f == 'outofstock') {
                $filter['not_in_stock'] = 1;
            } elseif($f == 'instock') {
                $filter['in_stock'] = 1;
            } elseif($f == 'without_images') {
                $filter['has_no_images'] = 1;
            }

            $filter['filter'] = $f;
        } else {
            $filter['filter'] = null;
        }

        // Поиск
        $keyword = $this->request->get('keyword');
        if (!empty($keyword)) {
            $filter['keyword'] = $keyword;
        }

        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $this->productsEntity->count($filter);
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function moveToPage($ids, $filter)
    {
        /*Переместить на страницу*/
        $targetPage = $this->request->post('target_page', 'integer');

        // Сразу потом откроем эту страницу
        $filter['page'] = $targetPage;

        // До какого товара перемещать
        $limit = $filter['limit']*($targetPage-1);
        if($targetPage > $this->request->get('page', 'integer')) {
            $limit += count($ids)-1;
        } else {
            $ids = array_reverse($ids, true);
        }

        $tempFilter = $filter;
        $tempFilter['page'] = $limit+1;
        $tempFilter['limit'] = 1;
        $tmp = $this->productsEntity->find($tempFilter);
        $targetProduct = array_pop($tmp);
        $targetPosition = $targetProduct->position;

        // Если вылезли за последний товар - берем позицию последнего товара в качестве цели перемещения
        if ($targetPage > $this->request->get('page', 'integer') && !$targetPosition) {

            $select = $this->queryFactory->newSelect();
            $select->cols(['distinct p.position AS target'])
                ->from('__products AS p')
                ->join('left', '__products_categories AS pc', 'pc.product_id = p.id')
                ->orderBy(['p.position DESC'])
                ->limit(1);

            $this->db->query($select);
            $targetPosition = $this->db->result('target');
        }

        foreach ($ids as $id) {
            $initialPosition = $this->productsEntity->cols(['position'])->get((int)$id)->position;

            $update = $this->queryFactory->newUpdate();
            if ($targetPosition > $initialPosition) {
                $update->table('__products')
                    ->set('position', 'position-1')
                    ->where('position > :initial_position')
                    ->where('position <= :target_position')
                    ->bindValues([
                        'initial_position' => $initialPosition,
                        'target_position' => $targetPosition,
                    ]);
            } else {
                $update->table('__products')
                    ->set('position', 'position+1')
                    ->where('position < :initial_position')
                    ->where('position >= :target_position')
                    ->bindValues([
                        'initial_position' => $initialPosition,
                        'target_position' => $targetPosition,
                    ]);
            }
            $this->db->query($update);

            $this->productsEntity->update($id, ['position' => $targetPosition]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function actionAddSecondCategories($ids)
    {
        /* Добавить категорию */
        $categoryId = $this->request->post('target_second_category');
        
        // Подсчитываем кол-во категорий каждого товара, чтобы знать какой position ставить новым категориям
        $productsCategoriesNum = [];
        foreach ($this->categoriesEntity->getProductCategories($ids) as $pc) {
            if (!isset($productsCategoriesNum[$pc->product_id])) {
                $productsCategoriesNum[$pc->product_id] = 0;
            }
            $productsCategoriesNum[$pc->product_id]++;
        }
        foreach ($ids as $id) {
            if (!isset($productsCategoriesNum[$id])) {
                $productsCategoriesNum[$id] = 0;
            }
            $this->categoriesEntity->addProductCategory($id, $categoryId, $productsCategoriesNum[$id]++);
        }
        
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function actionMoveToCategory($ids)
    {
        /*Переместить в категорию*/
        $categoryId = $this->request->post('target_category', 'integer');
        $filter['page'] = 1;
        $category = $this->categoriesEntity->get($categoryId);
        $filter['category_id'] = $category->children;

        foreach($ids as $id) {
            $delete = $this->queryFactory->newDelete();
            $delete->from('__products_categories')
                ->where('category_id=:category_id')
                ->where('product_id=:product_id')
                ->bindValues([
                    'category_id' => $categoryId,
                    'product_id' => $id,
                ]);

            $update = $this->queryFactory->newUpdate();
            $update->table('__products_categories')
                ->cols(['category_id'])
                ->where('product_id = :product_id')
                ->orderBy(['position DESC'])
                ->limit(1)
                ->bindValues([
                    'category_id' => $categoryId,
                    'product_id' => $id
                ])
                ->ignore();

            $this->db->query($update);
            if ($this->db->affectedRows() == 0) {
                $insert = $this->queryFactory->newInsert();
                $insert->into('__products_categories')
                    ->ignore()
                    ->set('category_id', $categoryId)
                    ->set('product_id', $id);
                $this->db->query($insert);
            }
            
        }

        if (!empty($ids)) {
            $this->productsEntity->update($ids, ['main_category_id' => $categoryId]);
            $productsUrls = $this->productsEntity->col('url')->find(['id' => $ids]);
            $this->routerCacheEntity->deleteByUrl(RouterCacheEntity::TYPE_PRODUCT, $productsUrls);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function duplicateProducts($ids)
    {
        foreach($ids as $id) {
            $this->productsEntity->duplicate((int)$id);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function actionMoveToBrand($ids)
    {
        $brandId = $this->request->post('target_brand', 'integer');
        $filter['page'] = 1;
        $filter['brand_id'] = $brandId;
        $this->productsEntity->update($ids, ['brand_id' => $brandId]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function sortPositions($positions)
    {
        $ids = array_keys($positions);
        sort($positions);
        $positions = array_reverse($positions);

        return ExtenderFacade::execute(__METHOD__, [$ids, $positions], func_get_args());
    }

    public function updatePositions($ids, $positions)
    {
        foreach($positions as $i=>$position) {
            $this->productsEntity->update($ids[$i], array('position' => (int) $position));
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function findProductsForProductsAdmin($filter, $sortName)
    {
        
        $this->productsEntity->order($sortName);
        
        $products  = $this->productsEntity->mappedBy('id')->find($filter);

        $imagesIds = [];
        foreach ($products as $p) {
            $imagesIds[] = $p->main_image_id;
        }

        if (!empty($products)) {
            // Товары
            $productsIds = array_keys($products);
            foreach($products as $product) {
                $product->variants   = array();
                $product->properties = array();
            }

            $variants = $this->variantsEntity->find(['product_id' => $productsIds]);
            foreach ($variants as $variant) {
                $products[$variant->product_id]->variants[] = $variant;
            }

            if (!empty($imagesIds)) {
                foreach ($this->imagesEntity->find(['id' => $imagesIds]) as $image) {
                    if (isset($products[$image->product_id])) {
                        $products[$image->product_id]->image = $image;
                    }
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }

    public function disable($ids)
    {
        $this->productsEntity->update($ids, ['visible'=>0]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function enable($ids)
    {
        $this->productsEntity->update($ids, ['visible'=>1]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function setFeatured($ids)
    {
        $this->productsEntity->update($ids, ['featured' => 1]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function unsetFeatured($ids)
    {
        $this->productsEntity->update($ids, ['featured'=>0]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete($ids)
    {
        $this->productsEntity->delete($ids);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

}