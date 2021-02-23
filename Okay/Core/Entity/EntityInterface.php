<?php


namespace Okay\Core\Entity;


interface EntityInterface
{
    
    /**
     * @param $id
     * @return object|false
     * Получить одну конкретную сущность, по id или url
     */
    public function get($id);

    /**
     * @param array $filter
     * @return array
     * Поиск сущностей в соответствии с фильтром
     */
    public function find(array $filter = []);
    
    /**
     * @param array $filter
     * @return object|false
     * Поиск одной сущности в соответствии с фильтром.
     * Метод возвращает один кортеж данных
     */
    public function findOne(array $filter = []);

    /**
     * @param string $order
     * @param array $additionalData кастомный массив данных, который может понадобиться в методе
     * @return self
     * 
     */
    public function order($order, array $additionalData = []);

    /**
     * @param array $cols
     * @return self
     * Метод принимает массив колонок, которые нужно достать.
     * Может пригодиться в случае, когда не все колонки нужны
     */
    public function cols(array $cols);

    /**
     * @param array $filter
     * @return int
     * Подсчет сущностей, в соответствии с фильтром
     */
    public function count(array $filter = []);

    /**
     * @param object|array $object
     * @return int|false
     * Добавление сущности
     */
    public function add($object);

    /**
     * @param int|array $ids
     * @param object|array $object
     * @return bool
     * Обновление сущности
     */
    public function update($ids, $object);

    /**
     * @param array $ids
     * @return bool
     * Удаление сущности
     */
    public function delete($ids);

    /**
     * @return void
     * Сброс состояния поиска сущности
     */
    public function flush();

    /**
     * @return self
     * @param $columnName
     * Установки колонки по которой нужно делать маппинг
     */
    public function mappedBy($columnName);
}
