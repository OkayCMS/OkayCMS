<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Image;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Translit;

class BlogCategoriesEntity extends Entity
{
    private $allCategories;
    private $categoriesTree;
    private $filteredCategoryIds = [];

    protected static $fields = [
        'id',
        'parent_id',
        'url',
        'image',
        'position',
        'visible',
        'external_id',
        'level_depth',
        'last_modify',
        'created'
    ];

    protected static $langFields = [
        'name',
        'name_h1',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'annotation',
        'description',
    ];

    protected static $additionalFields = [
        'r.slug_url',
    ];
    
    protected static $searchFields = [];

    protected static $defaultOrderFields = [
        'parent_id',
        'position',
    ];

    protected static $table = 'blog_categories';
    protected static $langObject = 'category';
    protected static $langTable = 'blog_categories';
    protected static $tableAlias = 'c';

    public function flush()
    {
        $this->filteredCategoryIds = [];
        parent::flush();
    }

    public function getCategoriesTree() {
        if (empty($this->categoriesTree)) {
            $this->initCategories();
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->categoriesTree, func_get_args());
    }

    public function get($id)
    {
        if (empty($this->categoriesTree)) {
            $this->initCategories();
        }

        if (is_int($id) && array_key_exists(intval($id), $this->allCategories)) {
            $category = $this->allCategories[intval($id)];
            return ExtenderFacade::execute([static::class, __FUNCTION__], $category, func_get_args());
        }

        if(is_string($id)) {
            foreach ($this->allCategories as $category) {
                if ($category->url == $id) {
                    return ExtenderFacade::execute([static::class, __FUNCTION__], $this->get((int)$category->id), func_get_args());
                }
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
    }
    
    public function add($category)
    {
        $category = (object) $category;
        $category->level_depth = $this->determineLevelDepth($category);

        /** @var Translit $translit */
        $translit = $this->serviceLocator->getService(Translit::class);
        if (empty($category->url)) {
            $category->url = $translit->translit($category->name);
            $category->url = str_replace('.', '', $category->url);
        }

        $category->url = preg_replace("/[\s]+/ui", '', $category->url);

        while ($this->get((string)$category->url)) {
            if(preg_match('/(.+)([0-9]+)$/', $category->url, $parts)) {
                $category->url = $parts[1].''.($parts[2]+1);
            } else {
                $category->url = $category->url.'2';
            }
        }

        $id = parent::add($category);
        unset($this->categoriesTree);
        unset($this->allCategories);
        return $id;
    }
    
    public function update($ids, $category)
    {
        $category = (object) $category;
        
        // При обновлении категории не обновляем уровень вложенности, если его не возможно корректно определить
        if (($levelDepth = $this->determineLevelDepth($category)) !== false) {
            $category->level_depth = $levelDepth;
        }

        parent::update($ids, $category);
        unset($this->categoriesTree);
        unset($this->allCategories);

        /** @var RouterCacheEntity $routerCacheEntity */
        $routerCacheEntity = $this->entity->get(RouterCacheEntity::class);
        $routerCacheEntity->deleteWrongCache();
        
        return true;
    }

    public function find(array $filter = [])
    {
        if (empty($this->categoriesTree)) {
            $this->initCategories();
        }

        if (empty($filter)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], $this->allCategories, func_get_args());
        }
        
        $this->buildFilter($filter);
        $matchedCategories = [];
        foreach ($this->filteredCategoryIds as $id) {
            if (isset($this->allCategories[$id])) {
                $matchedCategories[$id] = $this->allCategories[$id];
            }
        }

        $this->flush();
        return ExtenderFacade::execute([static::class, __FUNCTION__], $matchedCategories, func_get_args());
    }

    public function findOne(array $filter = [])
    {
        if (empty($this->categoriesTree)) {
            $this->initCategories();
        }

        if (empty($filter)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], reset($this->allCategories), func_get_args());
        }
        
        $this->buildFilter($filter);
        foreach ($this->filteredCategoryIds as $id) {
            if (isset($this->allCategories[$id])) {
                $this->flush();
                return ExtenderFacade::execute([static::class, __FUNCTION__], $this->allCategories[$id], func_get_args());
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
    }

    public function delete($ids)
    {
        /** @var Image $imageCore */
        $imageCore = $this->serviceLocator->getService(Image::class);
        
        $ids = (array)$ids;
        foreach ($ids as $id) {
            $category = $this->get((int)$id);
            if ($category === false && empty($category->children)) {
                continue;
            }

            foreach ($category->children as $cId) {
                $imageCore->deleteImage(
                    $cId,
                    'image',
                    self::class,
                    $this->config->get('original_blog_categories_dir'),
                    $this->config->get('resized_blog_categories_dir')
                );
            }

            $select = $this->queryFactory->newSelect();
            $select->from('__blog_categories_relation')
                ->cols(['post_id'])
                ->where('category_id IN (:category_id)')
                ->bindValue('category_id', $category->children);

            $this->db->query($select);
            //Получим товары для которых нужно будет обновить информацию о главных категориях
            $postIds = $this->db->results('post_id');

            $delete = $this->queryFactory->newDelete();
            $delete->from('__blog_categories_relation')
                ->where('category_id IN (:category_id)')
                ->bindValue('category_id', $category->children);

            $this->db->query($delete);
            //Обновим информацию о главной категории
            $this->updateMainPostsCategory($postIds);

            parent::delete($category->children);
        }
        
        unset($this->categoriesTree);
        unset($this->allCategories);

        /** @var RouterCacheEntity $routerCacheEntity */
        $routerCacheEntity = $this->entity->get(RouterCacheEntity::class);
        $routerCacheEntity->deleteWrongCache();
        
        return ExtenderFacade::execute([static::class, __FUNCTION__], true, func_get_args());
    }

    //Обновление информацию о главной категории товара
    public function updateMainPostsCategory($postsIds) {
        $postsIds = (array)$postsIds;
        if (empty($postsIds)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement("UPDATE __blog b
                          LEFT JOIN __blog_categories_relation bc ON b.id = bc.post_id AND bc.position=(SELECT MIN(position) FROM __blog_categories_relation WHERE post_id=b.id LIMIT 1)
                          SET b.main_category_id = bc.category_id
                          WHERE b.id IN (:posts_ids)");
        $sql->bindValue('posts_ids', $postsIds);
        $this->db->query($sql);

        return ExtenderFacade::execute([static::class, __FUNCTION__], true, func_get_args());
    }
    
    public function addPostCategory($postId, $categoryId, $position = 0)
    {
        $this->update($categoryId, ['last_modify' => 'now()']);
        
        $insert = $this->queryFactory->newInsert();
        $insert->into('__blog_categories_relation')
            ->cols([
                'post_id',
                'category_id',
                'position',
            ])
            ->bindValues([
                'post_id' => $postId,
                'category_id' => $categoryId,
                'position' => $position,
            ])
            ->ignore();
        
        $this->db->query($insert);

        return ExtenderFacade::execute([static::class, __FUNCTION__], true, func_get_args());
    }

    public function deletePostCategory($postsIds, $categoriesIds = [])
    {
        if (empty($postsIds)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        $postsIds = (array) $postsIds;
        $categoriesIds = (array) $categoriesIds;

        $delete = $this->queryFactory->newDelete()
            ->from('__blog_categories_relation')
            ->where('post_id IN(:posts_ids)')
            ->bindValue('posts_ids', $postsIds);

        if (empty($categoriesIds)) {
            $this->db->query($delete);
            return ExtenderFacade::execute([static::class, __FUNCTION__], true, func_get_args());
        }

        $delete->where('category_id IN(:categories_ids)')
            ->bindValue('categories_ids', $categoriesIds);

        $this->db->query($delete);
        return ExtenderFacade::execute([static::class, __FUNCTION__], true, func_get_args());
    }

    /*Выбираем категории определенного товара*/
    public function getPostCategories($postsIds = []) {
        $select = $this->queryFactory->newSelect();
        $select->from('__blog_categories_relation')
            ->cols([
                'post_id',
                'category_id',
                'position',
            ])
            ->orderBy(['position']);
        
        if (!empty($postsIds)) {
            $select->where('post_id IN (:post_id)')
                ->bindValue('post_id', (array)$postsIds);
        }
        
        $this->db->query($select);
        $results = $this->db->results();
        return ExtenderFacade::execute([static::class, __FUNCTION__], $results, func_get_args());
    }

    protected function filter__id($ids)
    {
        $ids = (array)$ids;
        $this->filteredCategoryIds = array_merge($this->filteredCategoryIds, $ids);
        $this->filteredCategoryIds = array_unique($this->filteredCategoryIds);
    }
    
    protected function filter__url($url)
    {
        foreach ($this->allCategories as $category) {
            if ($category->url == $url) {
                $this->filteredCategoryIds = [$category->id];
                break;
            }
        }
    }
    
    protected function filter__post_id($ids)
    {
        $ids = (array)$ids;
        $select = $this->queryFactory->newSelect();
        $select->cols(['category_id'])
            ->from('__blog_categories_relation')
            ->where('post_id IN (:posts_ids)');
        $select->bindValue('posts_ids', $ids);
        
        if (!empty($this->filteredCategoryIds)) {
            $select->where('category_id IN (:category_id)')->bindValue('category_id', $this->filteredCategoryIds);
        }
        
        $this->db->query($select);
        
        $categoriesIds = $this->db->results('category_id');
        $this->filteredCategoryIds = array_merge($this->filteredCategoryIds, $categoriesIds);
        $this->filteredCategoryIds = array_unique($this->filteredCategoryIds);
    }

    public function initCategories()
    {
        $categories = $this->getAllCategoriesFromDb();

        $tree = new \stdClass();
        $tree->subcategories = array();

        // Указатели на узлы дерева
        $pointers = array();
        $pointers[0] = &$tree;
        $pointers[0]->path = array();
        $pointers[0]->level = 0;

        $finish = false;
        // Не кончаем, пока не кончатся категории, или пока ниодну из оставшихся некуда приткнуть
        while(!empty($categories)  && !$finish) {
            $flag = false;
            // Проходим все выбранные категории
            foreach($categories as $k=>$category) {
                if(isset($pointers[$category->parent_id])) {
                    // В дерево категорий (через указатель) добавляем текущую категорию
                    $pointers[$category->id] = $pointers[$category->parent_id]->subcategories[$category->id] = $category;

                    // Путь к текущей категории
                    $curr = $pointers[$category->id];
                    $pointers[$category->id]->path = array_merge((array)$pointers[$category->parent_id]->path, array($curr));

                    // Путь к текущей категории в виде строки
                    $pathUrl = '';
                    foreach((array) $pointers[$category->id]->path as $singleCategoryInPath) {
                        $pathUrl .= '/'.$singleCategoryInPath->url;
                    }
                    $pointers[$category->id]->path_url = $pathUrl;

                    // Уровень вложенности категории
                    $pointers[$category->id]->level = 1+$pointers[$category->parent_id]->level;

                    // Убираем использованную категорию из массива категорий
                    unset($categories[$k]);
                    $flag = true;
                }
            }
            if(!$flag) $finish = true;
        }

        // Для каждой категории id всех ее деток узнаем
        $ids = array_reverse(array_keys($pointers));
        foreach($ids as $id) {
            if($id>0) {
                $pointers[$id]->children[] = $id;

                if(isset($pointers[$pointers[$id]->parent_id]->children)) {
                    $pointers[$pointers[$id]->parent_id]->children = array_merge($pointers[$id]->children, $pointers[$pointers[$id]->parent_id]->children);
                } else {
                    $pointers[$pointers[$id]->parent_id]->children = $pointers[$id]->children;
                }
            }
        }
        unset($pointers[0]);
        unset($ids);

        $categoriesIdsWithPosts = [];
        $select = $this->queryFactory->newSelect();
        $select->cols(['category_id'])
            ->from('__blog_categories_relation bc')
            ->innerJoin(BlogCategoriesEntity::getTable() . ' AS c', 'c.id=bc.category_id AND c.visible=1')
            ->groupBy(['bc.category_id']);
        
        foreach ($select->results('category_id') as $result) {
            $categoriesIdsWithPosts[$result] = $result;
        }

        $hasPostsCategoriesIds = [];
        foreach($pointers as &$pointer) {

            if (isset($categoriesIdsWithPosts[$pointer->id])) {
                $hasPostsCategoriesIds[] = $pointer->id;
            }
            $pointer->has_posts = false;
        }
        unset($pointer);

        foreach ($hasPostsCategoriesIds as $id) {
            foreach ($pointers[$id]->path as &$c) {
                $c->has_posts = true;
            }
        }
        unset($c);

        $this->categoriesTree = $tree->subcategories;
        $this->allCategories  = $pointers;
    }

    private function getAllCategoriesFromDb()
    {
        // Подключаем языковую таблицу
        $langQuery = $this->lang->getQuery(
            $this->getTableAlias(),
            $this->getLangTable(),
            $this->getLangObject()
        );

        $this->select->from($this->getTable() . ' AS ' . $this->getTableAlias());
        if (!empty($langQuery['join'])) {
            $this->select->join('LEFT', $langQuery['join'], $langQuery['cond']);
        }

        $this->select->leftJoin(RouterCacheEntity::getTable() . ' AS r', 'r.url=c.url AND r.type="blog_category"');

        $this->select->cols($this->getAllFields());
        $this->db->query($this->select);

        $resultFields = $this->getAllFieldsWithoutAlias();
        $field = null;

        if (count($resultFields) == 1) {
            $field = reset($resultFields);
        }

        $categories = $this->db->results($field);
        $this->flush();
        return $categories;
    }

    private function determineLevelDepth($category)
    {
        if (!property_exists($category, 'parent_id')) {
            return false;
        }
        
        if (empty($this->categoriesTree)) {
            $this->initCategories();
        }
        
        if (empty($category->parent_id)) {
            return 1;
        }

        $parentCategory = $this->allCategories[$category->parent_id];
        return $parentCategory->level + 1;
    }

    public function duplicate($categoryId, $parentId)
    {
        $categoryId = (int)$categoryId;
        $category = $this->get((int)$categoryId);

        //Запоминаем текущую позицию, на нее станет новая запись
        $position = $category->position;

        $newCategory = new \stdClass();

        $fields = array_merge($this->getFields(), $this->getLangFields());

        foreach ($fields as $field) {
            if (property_exists($category, $field)) {
                $newCategory->$field = $category->$field;
            }
        }
        unset($newCategory->id);
        unset($newCategory->url);

        $newCategory->parent_id = $parentId;
        $newCategoryId = $this->add($newCategory);

        // Сдвигаем категории вперед и вставляем копию на соседнюю позицию
        $update = $this->queryFactory->newUpdate();
        $update->table('__blog_categories')
            ->set('position', 'position+1')
            ->where('position>=:position')
            ->bindValue('position', $category->position);
        $this->db->query($update);

        $update = $this->queryFactory->newUpdate();
        $update->table('__blog_categories')
            ->set('position', ':position')
            ->where('id=:id')
            ->bindValues([
                'position' => $position,
                'id' => $newCategoryId,
            ]);
        $this->db->query($update);

        if (!empty($category->subcategories)) {
            foreach ($category->subcategories as $subcategory) {
                $this->duplicate($subcategory->id, $newCategoryId);
            }
        }

        $this->multiDuplicateCategory($categoryId, $newCategoryId);
        return $newCategoryId;
    }

    private function multiDuplicateCategory($categoryId, $newCategoryId)
    {
        $langId = $this->lang->getLangId();
        if (!empty($langId)) {

            /** @var LanguagesEntity $langEntity */
            $langEntity = $this->entity->get(LanguagesEntity::class);

            $languages = $langEntity->find();
            $categoryLangFields = $this->getLangFields();

            foreach ($languages as $language) {
                if ($language->id != $langId) {
                    $this->lang->setLangId($language->id);

                    if (!empty($categoryLangFields)) {
                        $sourceCategory = $this->get((int)$categoryId);
                        $destinationCategory = new \stdClass();
                        foreach($categoryLangFields as $field) {
                            $destinationCategory->{$field} = $sourceCategory->{$field};
                        }
                        $this->update($newCategoryId, $destinationCategory);
                    }

                    $this->lang->setLangId($langId);
                }
            }
        }
    }
}