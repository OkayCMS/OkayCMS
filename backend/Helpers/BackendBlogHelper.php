<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Config;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Entities\BlogCategoriesEntity;
use Okay\Entities\BlogEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Helpers\ProductsHelper;

class BackendBlogHelper
{
    /**
     * @var BlogEntity
     */
    private $blogEntity;
    
    /**
     * @var BlogCategoriesEntity
     */
    private $categoriesEntity;

    /**
     * @var Request
     */
    private $request;
    
    /**
     * @var Config
     */
    private $config;
    
    /**
     * @var Image
     */
    private $imageCore;
    
    /**
     * @var Settings
     */
    private $settings;
    
    /**
     * @var QueryFactory
     */
    private $queryFactory;
    
    private $productsHelper;

    public function __construct(
        EntityFactory $entityFactory,
        Request $request,
        Config $config,
        Image $imageCore,
        Settings $settings,
        QueryFactory $queryFactory,
        ProductsHelper $productsHelper
    ) {
        $this->blogEntity = $entityFactory->get(BlogEntity::class);
        $this->categoriesEntity = $entityFactory->get(BlogCategoriesEntity::class);
        $this->request    = $request;
        $this->config     = $config;
        $this->imageCore  = $imageCore;
        $this->settings   = $settings;
        $this->queryFactory = $queryFactory;
        $this->productsHelper = $productsHelper;
    }

    public function disable($ids)
    {
        if (is_array($ids)) {
            $this->blogEntity->update($ids, ['visible'=>0]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function enable($ids)
    {
        if (is_array($ids)) {
            $this->blogEntity->update($ids, ['visible' => 1]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete($ids)
    {
        if (is_array($ids)) {
            $this->blogEntity->delete($ids);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function buildPostsFilter()
    {
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));
        $filter['limit'] = 20;

        $keyword = $this->request->get('keyword', 'string');
        if (!empty($keyword)) {
            $filter['keyword'] = $keyword;
        }

        $postsCount = $this->blogEntity->count($filter);
        if($this->request->get('page') == 'all') {
            $filter['limit'] = $postsCount;
        }

        // Категории
        $categoryId = $this->request->get('category_id', 'integer');
        $category = $this->categoriesEntity->findOne(['id' => $categoryId]);
        if(!empty($categoryId) && !empty($category)) {
            $filter['category_id'] = $category->children;
        } elseif ($categoryId == -1) {
            $filter['without_category'] = 1;
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function getPostsCount($filter)
    {
        $obj = new \ArrayObject();
        $countFilter = $obj->getArrayCopy();
        unset($countFilter['limit']);
        $count = $this->blogEntity->count($filter);
        return ExtenderFacade::execute(__METHOD__, $count, func_get_args());
    }

    public function findPosts($filter)
    {
        $posts = $this->blogEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $posts, func_get_args());
    }

    public function prepareAdd($post)
    {
        return ExtenderFacade::execute(__METHOD__, $post, func_get_args());
    }

    public function add($post)
    {
        $insertId = $this->blogEntity->add($post);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdate($post)
    {
        return ExtenderFacade::execute(__METHOD__, $post, func_get_args());
    }

    public function update($id, $post)
    {
        $this->blogEntity->update($id, $post);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getPost($id)
    {
        $post = $this->blogEntity->get($id);

        if (empty($post)) {
            $post = new \stdClass;
            $post->date = date($this->settings->get('date_format'), time());
            $post->visible = 1;
            $post->show_table_content = 1;
        }
        
        return ExtenderFacade::execute(__METHOD__, $post, func_get_args());
    }

    public function deleteImage($post)
    {
        $this->imageCore->deleteImage(
            $post->id,
            'image',
            BlogEntity::class,
            $this->config->get('original_blog_dir'),
            $this->config->get('resized_blog_dir')
        );

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function uploadImage($image, $post)
    {
        if (!empty($image['name']) && ($filename = $this->imageCore->uploadImage($image['tmp_name'], $image['name'], $this->config->get('original_blog_dir')))) {
            $this->imageCore->deleteImage(
                $post->id,
                'image',
                BlogEntity::class,
                $this->config->get('original_blog_dir'),
                $this->config->get('resized_blog_dir')
            );

            $this->blogEntity->update($post->id, ['image'=>$filename]);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function prepareUpdateRelatedProducts($post, $relatedProducts)
    {
        return ExtenderFacade::execute(__METHOD__, $relatedProducts, func_get_args());
    }

    public function updateRelatedProducts($post, $relatedProducts)
    {
        $this->blogEntity->deleteRelatedProduct($post->id);
        if (is_array($relatedProducts)) {
            $pos = 0;
            foreach($relatedProducts  as $i=>$relatedProduct) {
                $this->blogEntity->addRelatedProduct($post->id, $relatedProduct->related_id, $pos++);
            }
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @param array $filter аргумент метода getRelatedProducts()
     * @return mixed|void|null
     * @throws \Exception
     */
    public function getRelatedProductsList(array $filter)
    {

        $relatedProducts = [];
        foreach ($this->blogEntity->getRelatedProducts($filter) as $p) {
            $relatedProducts[$p->related_id] = null;
        }

        if (!empty($relatedProducts)) {
            $relatedIds = array_keys($relatedProducts);
            $relatedFilter = [
                'id' => $relatedIds,
                'limit' => count($relatedIds),
            ];
            foreach ($this->productsHelper->getList($relatedFilter) as $p) {
                $relatedProducts[$p->id] = $p;
            }
            foreach ($relatedProducts as $id=>$r) {
                if ($r === null) {
                    unset($relatedProducts[$id]);
                }
            }
        }
        return ExtenderFacade::execute(__METHOD__, $relatedProducts, func_get_args());
    }
    
    public function findPostCategories($post)
    {
        $postCategories = [];
        if (!empty($post->id)) {
            foreach ($this->categoriesEntity->getPostCategories($post->id) as $pc) {
                $postCategories[$pc->category_id] = $pc;
            }

            if (!empty($postCategories)) {
                foreach ($this->categoriesEntity->find(['id' => array_keys($postCategories)]) as $category) {
                    $postCategories[$category->id] = $category;
                }
            }
        }

        if (empty($postCategories)) {
            if ($category_id = $this->request->get('category_id')) {
                $category = $this->categoriesEntity->findOne(['id' => $category_id]);
                $postCategories[$category_id] = $category;
            } else {
                $postCategories = [];
            }
        }

        return ExtenderFacade::execute(__METHOD__, $postCategories, func_get_args());
    }

    public function prepareUpdatePostCategories($post, $postCategories)
    {
        return ExtenderFacade::execute(__METHOD__, $postCategories, func_get_args());
    }

    public function updatePostCategories($post, $postCategories)
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from('__blog_categories_relation')
            ->where('post_id=:post_id')
            ->bindValue('post_id', $post->id)->execute();

        if (is_array($postCategories)) {
            $i = 0;
            foreach($postCategories as $category) {
                $this->categoriesEntity->addPostCategory($post->id, $category->id, $i);
                $i++;
            }
            unset($i);
        }

        if (!empty($postCategories)) {
            $mainCategory = reset($postCategories);
            $this->blogEntity->update($post->id, ['main_category_id' => $mainCategory->id]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
}