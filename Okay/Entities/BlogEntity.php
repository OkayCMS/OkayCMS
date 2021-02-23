<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Entity\RelatedProductsInterface;
use Okay\Core\Image;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BlogEntity extends Entity implements RelatedProductsInterface
{
    
    protected static $fields = [
        'id',
        'url',
        'visible',
        'show_table_content',
        'date',
        'image',
        'last_modify',
        'main_category_id',
        'author_id',
        'read_time',
        'updated_date',
        'rating',
        'votes',
    ];
    
    protected static $langFields = [
        'name',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'annotation',
        'description',
    ];
    
    protected static $searchFields = [
        'name',
        'meta_keywords',
    ];
    
    protected static $defaultOrderFields = [
        'date DESC',
        'visible DESC',
        'name DESC',
        'id DESC',
    ];

    protected static $table = '__blog';
    protected static $langObject = 'blog';
    protected static $langTable = 'blog';
    protected static $tableAlias = 'b';
    protected static $alternativeIdField = 'url';

    public function update($ids, $object)
    {
        $res = parent::update($ids, $object);

        /** @var RouterCacheEntity $routerCacheEntity */
        $routerCacheEntity = $this->entity->get(RouterCacheEntity::class);
        $routerCacheEntity->deleteWrongCache();
        return $res;
    }
    
    public function delete($ids)
    {
        if (empty($ids)) {
            parent::delete($ids);
        }
        
        $ids = (array)$ids;

        $comments = $this->entity->get(CommentsEntity::class);
        $commentsIds = $comments->cols(['id'])->find([
            'type' => 'post',
            'object_id' => $ids,
        ]);

        $comments->delete($commentsIds);

        /** @var Image $imageCore */
        $imageCore = $this->serviceLocator->getService(Image::class);

        $ids = (array)$ids;
        foreach ($ids as $id) {
            $imageCore->deleteImage(
                $id,
                'image',
                self::class,
                $this->config->original_blog_dir,
                $this->config->resized_blog_dir
            );
        }
        
        parent::delete($ids);

        /** @var RouterCacheEntity $routerCacheEntity */
        $routerCacheEntity = $this->entity->get(RouterCacheEntity::class);
        $routerCacheEntity->deleteWrongCache();

        return ExtenderFacade::execute([static::class, __FUNCTION__], true, func_get_args());
    }

    public function getNeighborsPosts($categoryId, $date)
    {
        $pIds = [];
        // предыдущий товар
        $select = $this->queryFactory->newSelect();
        $select->from(self::getTable() . ' b')
            ->cols(['id'])
            ->join('left', '__blog_categories_relation pc', 'pc.post_id=b.id')
            ->where('b.date>:date')
            ->where('pc.position=(SELECT MIN(pc2.position) FROM __blog_categories_relation pc2 WHERE pc.post_id=pc2.post_id)')
            ->where('pc.category_id=:category_id')
            ->where('b.visible')
            ->orderBy(['b.date ASC'])
            ->limit(1)
            ->bindValues([
                'date' => $date,
                'category_id' => $categoryId,
            ]);

        $this->db->query($select);
        $pid = $this->db->result('id');
        if ($pid) {
            $pIds[$pid] = 'prev';
        }

        // следующий товар
        $select = $this->queryFactory->newSelect();
        $select->from(self::getTable() . ' b')
            ->cols(['id'])
            ->join('left', '__blog_categories_relation pc', 'pc.post_id=b.id')
            ->where('b.date<:date')
            ->where('pc.position=(SELECT MIN(pc2.position) FROM __blog_categories_relation pc2 WHERE pc.post_id=pc2.post_id)')
            ->where('pc.category_id=:category_id')
            ->where('b.visible')
            ->orderBy(['b.date DESC'])
            ->limit(1)
            ->bindValues([
                'date' => $date,
                'category_id' => $categoryId,
            ]);

        $this->db->query($select);
        $pid = $this->db->result('id');
        
        if ($pid) {
            $pIds[$pid] = 'next';
        }
        
        $result = ['next'=>'', 'prev'=>''];
        if (!empty($pIds)) {
            foreach ($this->find(array('id'=>array_keys($pIds))) as $p) {
                $result[$pIds[$p->id]] = $p;
            }
        }
        
        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }
    
    public function getRelatedProducts(array $filter = [])
    {
        $select = $this->queryFactory->newSelect();
        $select->from('__related_blogs')
            ->cols([
                'post_id',
                'related_id',
                'position',
            ])
            ->orderBy(['position']);
        
        
        if (!empty($filter['post_id'])) {
            $select->where('post_id IN (:post_id)')
                ->bindValue('post_id', (array)$filter['post_id']);
        }
        if (!empty($filter['product_id'])) {
            $select->where('related_id IN (:related_id)')
                ->bindValue('related_id', (array)$filter['product_id']);
        }
        
        $this->db->query($select);

        $results = $this->db->results();
        return ExtenderFacade::execute([static::class, __FUNCTION__], $results, func_get_args());
    }

    public function addRelatedProduct($postId, $relatedId, $position = 0)
    {
        $insert = $this->queryFactory->newInsert();
        $insert->into('__related_blogs')
            ->cols([
                'post_id',
                'related_id',
                'position',
            ])
            ->bindValues([
                'post_id' => $postId,
                'related_id' => $relatedId,
                'position' => $position,
            ])
            ->ignore();

        $this->db->query($insert);
        return $relatedId;
    }

    public function deleteRelatedProduct($postId, $relatedId = null)
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from('__related_blogs')
            ->where('post_id=:post_id')
            ->bindValue('post_id', (int)$postId);

        if ($relatedId !== null) {
            $delete->where('related_id=:related_id')
                ->bindValue('related_id', (int)$relatedId);
        }
        $this->db->query($delete);
    }

    protected function filter__category_id($categoriesIds)
    {
        $this->select->join(
            'INNER',
            '__blog_categories_relation AS bc',
            'b.id = bc.post_id AND bc.category_id IN(:category_ids)'
        );

        $this->select->bindValue('category_ids', $categoriesIds);

        $this->select->groupBy(['b.id']);
    }

    protected function filter__without_category($categoriesIds)
    {
        $this->select->where("(SELECT count(*)=0 FROM __blog_categories_relation bc WHERE bc.post_id=b.id)=:without_category");
        $this->select->bindValue('without_category', $categoriesIds);
    }
    
}
