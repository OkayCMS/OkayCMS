<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;

class AdvantagesEntity extends Entity
{
    protected static $fields = [
        'id',
        'filename',
        'position',
    ];

    protected static $langFields = [
        'text',
    ];

    protected static $defaultOrderFields = [
        'position ASC',
    ];

    protected static $table = '__advantages';
    protected static $langObject = 'advantage';
    protected static $langTable = 'advantages';
    protected static $tableAlias = 'a';

    public function add($advantage)
    {
        $advantageId = parent::add($advantage);
        $this->update($advantageId, [
            'position' => $advantageId
        ]);
        return $advantageId;
    }
}