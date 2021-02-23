<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Config;
use Okay\Core\Database;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Entities\BrandsEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendBrandsHelper
{
    /**
     * @var BrandsEntity
     */
    private $brandsEntity;

    /**
     * @var Image
     */
    private $imageCore;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var Request
     */
    private $request;

    public function __construct(
        EntityFactory $entityFactory,
        Config        $config,
        Image         $imageCore,
        QueryFactory  $queryFactory,
        Database      $db,
        Request       $request
    ){
        $this->brandsEntity = $entityFactory->get(BrandsEntity::class);
        $this->config       = $config;
        $this->imageCore    = $imageCore;
        $this->queryFactory = $queryFactory;
        $this->db           = $db;
        $this->request      = $request;
    }

    public function findBrands($filter)
    {
        $brands = $this->brandsEntity->mappedBy('id')->find($filter);
        return ExtenderFacade::execute(__METHOD__, $brands, func_get_args());
    }

    public function findAllBrands()
    {
        $brandsCount = $this->brandsEntity->count();
        $allBrands = $this->brandsEntity->mappedBy('id')->find(['limit' => $brandsCount]);
        return ExtenderFacade::execute(__METHOD__, $allBrands, func_get_args());
    }

    public function prepareFilterForProductsAdmin($categoryId)
    {
        $brandsFilter = [];
        if (!empty($categoryId)) {
            $brandsFilter['category_id'] = ['category_id' => $categoryId];
        }

        $brandsCount = $this->brandsEntity->count($brandsFilter);
        $brandsFilter['limit'] = $brandsCount;

        return ExtenderFacade::execute(__METHOD__, $brandsFilter, func_get_args());
    }

    public function prepareAdd($brand)
    {
        return ExtenderFacade::execute(__METHOD__, $brand, func_get_args());
    }

    public function add($brand)
    {
        $insertId = $this->brandsEntity->add($brand);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdate($brand)
    {
        return ExtenderFacade::execute(__METHOD__, $brand, func_get_args());
    }

    public function update($id, $brand)
    {
        $this->brandsEntity->update($id, $brand);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getBrand($id)
    {
        $brand = $this->brandsEntity->get($id);
        return ExtenderFacade::execute(__METHOD__, $brand, func_get_args());
    }

    public function deleteImage($brand)
    {
        $this->imageCore->deleteImage(
            $brand->id,
            'image',
            BrandsEntity::class,
            $this->config->original_brands_dir,
            $this->config->resized_brands_dir
        );

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function uploadImage($image, $brand)
    {
        if (!empty($image['name']) && ($filename = $this->imageCore->uploadImage($image['tmp_name'], $image['name'], $this->config->original_brands_dir))) {
            $this->imageCore->deleteImage(
                $brand->id,
                'image',
                BrandsEntity::class,
                $this->config->original_brands_dir,
                $this->config->resized_brands_dir
            );

            $this->brandsEntity->update($brand->id, ['image'=>$filename]);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function enable($ids)
    {
        $this->brandsEntity->update($ids, ['visible' => 1]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function disable($ids)
    {
        $this->brandsEntity->update($ids, ['visible' => 0]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete($ids)
    {
        $this->brandsEntity->delete($ids);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function moveToPage($ids, $targetPage, $filter)
    {
        // Сразу потом откроем эту страницу
        $filter['page'] = $targetPage;

        // До какого бренда перемещать
        $limit = $filter['limit']*($targetPage-1);
        if ($targetPage > $this->request->get('page', 'integer')) {
            $limit += count($ids)-1;
        } else {
            $ids = array_reverse($ids, true);
        }

        $tempFilter = $filter;
        $tempFilter['page'] = $limit+1;
        $tempFilter['limit'] = 1;
        $tmp = $this->brandsEntity->find($tempFilter);
        $targetBrand = array_pop($tmp);
        $targetPosition = $targetBrand->position;

        // Если вылезли за последний бренд - берем позицию последнего бренда в качестве цели перемещения
        if ($targetPage > $this->request->get('page', 'integer') && !$targetPosition) {

            $select = $this->queryFactory->newSelect();
            $select->from('__brands')
                ->cols(['distinct position AS target'])
                ->orderBy(['position DESC'])
                ->limit(1);

            $this->db->query($select);
            $targetPosition = $this->db->result('target');
        }

        foreach ($ids as $id) {
            $initialPosition = $this->brandsEntity->cols(['position'])->get((int)$id)->position;

            $update = $this->queryFactory->newUpdate();
            if ($targetPosition > $initialPosition) {
                $update->table('__brands')
                    ->set('position', 'position-1')
                    ->where('position > :initial_position')
                    ->where('position <= :target_position')
                    ->bindValues([
                        'initial_position' => $initialPosition,
                        'target_position' => $targetPosition,
                    ]);
            } else {
                $update->table('__brands')
                    ->set('position', 'position+1')
                    ->where('position < :initial_position')
                    ->where('position >= :target_position')
                    ->bindValues([
                        'initial_position' => $initialPosition,
                        'target_position' => $targetPosition,
                    ]);
            }
            $this->db->query($update);

            $this->brandsEntity->update($id, ['position' => $targetPosition]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function sortPositions($positions)
    {
        $ids       = array_keys($positions);
        sort($positions);

        foreach ($positions as $i=>$position) {
            $this->brandsEntity->update($ids[$i], ['position'=>$position]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function buildFilter()
    {
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));

        if ($filter['limit'] = $this->request->get('limit', 'integer')) {
            $filter['limit'] = max(5, $filter['limit']);
            $filter['limit'] = min(100, $filter['limit']);
            $_SESSION['brands_num_admin'] = $filter['limit'];
        } elseif (!empty($_SESSION['brands_num_admin'])) {
            $filter['limit'] = $_SESSION['brands_num_admin'];
        } else {
            $filter['limit'] = 25;
        }

        $keyword = $this->request->get('keyword', 'string');
        if (!empty($keyword)) {
            $filter['keyword'] = $keyword;
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function countBrands($filter)
    {
        $brandsCount = $this->brandsEntity->count($filter);
        return ExtenderFacade::execute(__METHOD__, $brandsCount, func_get_args());
    }

    public function makePagination($brandsCount, $filter)
    {
        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $brandsCount;
        }

        if ($filter['limit']>0) {
            $pagesCount = ceil($brandsCount/$filter['limit']);
        } else {
            $pagesCount = 0;
        }

        $filter['page'] = min($filter['page'], $pagesCount);

        return [$filter, $pagesCount];
    }

    public function findBrandsByCategory($category)
    {
        $brands = $this->brandsEntity->find(['category_id' => $category->children]);
        return ExtenderFacade::execute(__METHOD__, $brands, func_get_args());
    }

    public function duplicate($ids)
    {
        foreach($ids as $id) {
            $this->brandsEntity->duplicate((int)$id);
        }
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}