<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class LessonsEntity extends Entity
{
    
    protected static $fields = [
        'id',
        'preview',
        'video',
        'title',
        'description',
        'button',
        'target_module',
        'done',
    ];

    protected static $langFields = [
        'title',
        'description',
        'button',
    ];

    protected static $defaultOrderFields = [];

    protected static $table = '__lessons';
    protected static $langObject = 'lesson';
    protected static $langTable = 'lessons';
    protected static $tableAlias = 'les';

    public function doneAll()
    {
        $update = $this->queryFactory->newUpdate();
        $update->table(self::getTable())->set('done', 1);
        $this->db->query($update);

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    protected function filter__not_done($value = null)
    {
        $this->select->where('done IS NULL OR done=0');
    }
}
