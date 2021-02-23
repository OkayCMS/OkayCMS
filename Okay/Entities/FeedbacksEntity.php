<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class FeedbacksEntity extends Entity
{

    protected static $fields = [
        'id',
        'name',
        'email',
        'ip',
        'message',
        'is_admin',
        'parent_id',
        'processed',
        'date',
        'lang_id',
    ];

    protected static $defaultOrderFields = [
        'id DESC',
    ];

    protected static $searchFields = [
        'name',
        'message',
        'email',
    ];

    protected static $table = '__feedbacks';
    protected static $tableAlias = 'f';
    protected static $langTable;
    protected static $langObject;

    public function add($feedback)
    {
        $feedback = (object)$feedback;
        $feedback->date = 'now()';
        return parent::add($feedback);
    }

    public function delete($ids)
    {
        $ids = (array)$ids;
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $children = $this->cols(['id'])->find(['parent_id' => $id]);
                foreach ($children as $child_id) {
                    $this->delete($child_id);
                }
            }
        }
        return parent::delete($ids);
    }

    protected function filter__has_parent($hasParent)
    {
        $this->select->where('parent_id' . ($hasParent ? '>0' : '=0'));
    }

    protected function customOrder($order = null, array $orderFields = [], array $additionalData = [])
    {
        if (!empty($order)) {
            switch ($order) {
                case 'new_first':
                    $orderFields = ['f.id DESC'];
                    break;
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $orderFields, func_get_args());
    }
    
}
