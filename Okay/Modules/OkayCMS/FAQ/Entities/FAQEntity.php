<?php


namespace Okay\Modules\OkayCMS\FAQ\Entities;


use Okay\Core\Entity\Entity;

class FAQEntity extends Entity
{
    protected static $fields = [
        'id',
        'question',
        'answer',
        'visible',
        'position',
    ];

    protected static $langFields = [
        'question',
        'answer',
    ];

    protected static $defaultOrderFields = [
        'position',
    ];

    protected static $table = '__okaycms__faq__faq';
    protected static $langTable = 'okaycms__faq__faq';
    protected static $langObject = 'faq';
    protected static $tableAlias = 'of_f';
}