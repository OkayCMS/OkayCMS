<?php


namespace Okay\Core\Entity;


interface RelatedProductsInterface
{
    /**
     * Метод возвращает связанные товары для сущностей
     * Структура объекта:
        'post_id'|'product_id',
        'related_id',
        'position'
     * 
     * @param array $filter
     * @return \stdClass[] 
     */
    public function getRelatedProducts(array $filter = []);
    
    public function addRelatedProduct($objectId, $relatedId, $position = 0);
    
    public function deleteRelatedProduct($objectId, $relatedId = null);
}