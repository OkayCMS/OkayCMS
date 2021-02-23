<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Image;
use Okay\Core\Config;
use Okay\Core\EntityFactory;
use Okay\Entities\CategoriesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendCategoriesHelper
{

    private $config;
    private $imageCore;
    /** @var CategoriesEntity */
    private $categoriesEntity;

    public function __construct(
        EntityFactory $entityFactory,
        Image         $imageCore,
        Config        $config
    ) {
        $this->config           = $config;
        $this->imageCore        = $imageCore;
        $this->categoriesEntity = $entityFactory->get(CategoriesEntity::class);
    }

    public function getCategoriesTree()
    {
        $categories = $this->categoriesEntity->getCategoriesTree();
        return ExtenderFacade::execute(__METHOD__, $categories, func_get_args());
    }

    public function getCategory($id)
    {
        $category = $this->categoriesEntity->findOne(['id' => $id]);
        return ExtenderFacade::execute(__METHOD__, $category, func_get_args());
    }

    public function sortPositions($positions)
    {
        $ids = array_keys($positions);
        sort($positions);

        return ExtenderFacade::execute(__METHOD__, [$ids, $positions], func_get_args());
    }

    public function updatePositions($ids, $positions)
    {
        foreach($positions as $i=>$position) {
            $this->categoriesEntity->update($ids[$i], array('position' => (int) $position));
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function disable($ids)
    {
        $this->categoriesEntity->update($ids, ['visible' => 0]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function enable($ids)
    {
        $this->categoriesEntity->update($ids, ['visible' => 1]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete($ids)
    {
        $this->categoriesEntity->delete($ids);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function countAllCategories()
    {
        $count = $this->categoriesEntity->count();
        return ExtenderFacade::execute(__METHOD__, $count, func_get_args());
    }

    public function prepareAdd($category)
    {
        return ExtenderFacade::execute(__METHOD__, $category, func_get_args());
    }

    public function add($category)
    {
        $insertId = $this->categoriesEntity->add($category);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdate($id, $category)
    {
        return ExtenderFacade::execute(__METHOD__, $category, func_get_args());
    }

    public function update($id, $category)
    {
        $this->categoriesEntity->update($id, $category);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function deleteCategoryImage($category)
    {
        $this->imageCore->deleteImage(
            $category->id,
            'image',
            CategoriesEntity::class,
            $this->config->original_categories_dir,
            $this->config->resized_categories_dir
        );

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function prepareUploadCategoryImage($category, $image)
    {
        return ExtenderFacade::execute(__METHOD__, $image, func_get_args());
    }

    public function uploadCategoryImage($category, $image)
    {
        if (!empty($image['name']) && ($filename = $this->imageCore->uploadImage($image['tmp_name'], $image['name'], $this->config->original_categories_dir))) {

            $this->imageCore->deleteImage(
                $category->id,
                'image',
                CategoriesEntity::class,
                $this->config->original_categories_dir,
                $this->config->resized_categories_dir
            );

            $this->categoriesEntity->update($category->id, ['image'=>$filename]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function findCategories($filter = [])
    {
        $categories = $this->categoriesEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $categories, func_get_args());
    }

    public function duplicateCategories($ids)
    {
        foreach($ids as $id) {
            $category = $this->categoriesEntity->get((int)$id);
            $this->categoriesEntity->duplicate((int)$id, $category->parent_id);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}