<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;

class SEOFilterPatternsEntity extends Entity
{

    protected static $fields = [
        'id',
        'category_id',
        'type',
        'feature_id',
        'second_feature_id',
    ];

    protected static $langFields = [
        'h1',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'description',
    ];

    protected static $table = '__seo_filter_patterns';
    protected static $langObject = 'seo_filter_pattern';
    protected static $langTable = 'seo_filter_patterns';
    protected static $tableAlias = 's';
    
}
