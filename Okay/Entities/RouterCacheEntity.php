<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;

class RouterCacheEntity extends Entity
{
    
    const TYPE_PRODUCT = 'product';
    const TYPE_CATEGORY = 'category';
    const TYPE_POST = 'post';
    const TYPE_BLOG_CATEGORY = 'blog_category';
    
    protected static $fields = [
        'url',
        'slug_url',
        'type',
    ];

    protected static $table = 'router_cache';
    
    public function deleteByUrl($objectType, $url)
    {
        $delete = $this->queryFactory->newDelete();
        
        $delete->from(self::getTable())
            ->where('type=:type')
            ->where('url in (:url)')
            ->bindValue('type', $objectType)
            ->bindValue('url', (array)$url)
            ->execute();
        
        return true;
    }
    
    public function deleteProductsCache()
    {
        $delete = $this->queryFactory->newDelete();
        
        $delete->from(self::getTable())
            ->where('type="product"')
            ->execute();
        
        return true;
    }
    
    public function deleteCategoriesCache()
    {
        $delete = $this->queryFactory->newDelete();
        
        $delete->from(self::getTable())
            ->where('type="category"')
            ->execute();
        
        return true;
    }
    
    public function deleteBlogCategoriesCache()
    {
        $delete = $this->queryFactory->newDelete();
        
        $delete->from(self::getTable())
            ->where('type="blog_category"')
            ->execute();
        
        return true;
    }
    
    public function deleteBlogCache()
    {
        $delete = $this->queryFactory->newDelete();
        
        $delete->from(self::getTable())
            ->where('type="post"')
            ->execute();
        
        return true;
    }

    /**
     * Метод удаляет неактуальный кеш, нужно вызывать при удалении или обновлении категорий или товаров
     * 
     * @return bool
     */
    public function deleteWrongCache()
    {
        // Удаляем ненужный кеш товаров
        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement("DELETE r FROM " . self::getTable() . " AS r 
            LEFT JOIN " . ProductsEntity::getTable() . " AS p ON p.url=r.url AND r.type='product'
            WHERE r.type='product' AND p.id IS NULL")
            ->execute();

        // Удаляем ненужный кеш категорий
        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement("DELETE r FROM " . self::getTable() . " AS r 
            LEFT JOIN " . CategoriesEntity::getTable() . " AS c ON c.url=r.url AND r.type='category'
            WHERE r.type='category' AND c.id IS NULL")
            ->execute();

        // Удаляем ненужный кеш категорий блога
        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement("DELETE r FROM " . self::getTable() . " AS r 
            LEFT JOIN " . BlogCategoriesEntity::getTable() . " AS c ON c.url=r.url AND r.type='blog_category'
            WHERE r.type='blog_category' AND c.id IS NULL")
            ->execute();

        // Удаляем ненужный кеш блога
        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement("DELETE r FROM " . self::getTable() . " AS r 
            LEFT JOIN " . BlogEntity::getTable() . " AS c ON c.url=r.url AND r.type='post'
            WHERE r.type='post' AND c.id IS NULL")
            ->execute();
        
        return true;
    }
    
    public function getCategoriesUrlsWithoutCache()
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['c.url'])
            ->from(CategoriesEntity::getTable() . ' AS c')
            ->leftJoin(self::getTable() . ' AS r', 'c.url=r.url AND r.type = "category"')
            ->where('r.url IS NULL');
        
        return $select->results('url');
    }
    
    public function getProductsUrlsWithoutCache()
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['p.url'])
            ->from(ProductsEntity::getTable() . ' AS p')
            ->leftJoin(self::getTable() . ' AS r', 'p.url=r.url AND r.type = "product"')
            ->where('r.url IS NULL');
        
        return $select->results('url');
    }

    public function getBlogCategoriesUrlsWithoutCache()
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['c.url'])
            ->from(BlogCategoriesEntity::getTable() . ' AS c')
            ->leftJoin(self::getTable() . ' AS r', 'c.url=r.url AND r.type = "blog_category"')
            ->where('r.url IS NULL');

        return $select->results('url');
    }

    public function getBlogUrlsWithoutCache()
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['c.url'])
            ->from(BlogEntity::getTable() . ' AS c')
            ->leftJoin(self::getTable() . ' AS r', 'c.url=r.url AND r.type = "blog"')
            ->where('r.url IS NULL');

        return $select->results('url');
    }
    
}
