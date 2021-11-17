<?php

namespace Okay\Modules\OkayCMS\Feeds\Init;

use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Modules\OkayCMS\Feeds\Entities\ConditionsEntity;
use Okay\Modules\OkayCMS\Feeds\Entities\FeedsEntity;

class Init extends AbstractInit
{
    const PERMISSION = 'okay_cms__feeds';
    const CONDITIONS_ENTITIES_RELATION_TABLE = '__okay_cms__feeds__conditions_entities';

    public function install()
    {
        $this->setModuleType(MODULE_TYPE_XML);

        $this->setBackendMainController('FeedsAdmin');

        $this->migrateEntityTable(FeedsEntity::class, [
            (new EntityField('id'))->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('name'))->setTypeVarchar(100, false),
            (new EntityField('url'))->setTypeVarchar(100, false)->setIndexUnique(),
            (new EntityField('enabled'))->setTypeTinyInt(1, false)->setDefault(0),
            (new EntityField('preset'))->setTypeVarchar(255, false),
            (new EntityField('settings'))->setTypeMediumText(),
            (new EntityField('features_settings'))->setTypeMediumText(),
            (new EntityField('categories_settings'))->setTypeMediumText(),
            (new EntityField('position'))->setTypeInt(11)->setDefault(0)->setIndex()
        ]);

        $this->migrateEntityTable(ConditionsEntity::class, [
            (new EntityField('id'))->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('feed_id'))->setTypeInt(11, false),
            (new EntityField('entity'))->setTypeVarchar(255, false),
            (new EntityField('type'))->setTypeEnum(['inclusion', 'exclusion']),
            (new EntityField('all_entities'))->setTypeTinyInt(1, false)->setDefault(0),
        ]);

        $entityIdField = (new EntityField('entity_id'))->setTypeInt(11, false);
        $this->migrateCustomTable(self::CONDITIONS_ENTITIES_RELATION_TABLE, [
            (new EntityField('condition_id'))->setTypeInt(11, false)->setIndexUnique(null, $entityIdField),
            $entityIdField,
        ]);
    }

    public function init()
    {
        $this->addPermission(self::PERMISSION);
        $this->registerBackendController('FeedsAdmin');
        $this->addBackendControllerPermission('FeedsAdmin', self::PERMISSION);
        $this->registerBackendController('FeedAdmin');
        $this->addBackendControllerPermission('FeedAdmin', self::PERMISSION);

        $this->extendBackendMenu('okay_cms__feeds__menu', [
            'okay_cms__feeds__menu' => ['FeedsAdmin', 'FeedAdmin'],
        ], '<svg width="20" height="20" viewBox="0 -256 1792 1792" xmlns="http://www.w3.org/2000/svg">
            <g transform="matrix(1,0,0,-1,212.61017,1346.1695)"><path d="M 384,192 Q 384,112 328,56 272,0 192,0 112,0 56,56 0,112 0,192 q 0,80 56,136 56,56 136,56 80,0 136,-56 56,-56 56,-136 z M 896,69 Q 898,41 879,21 861,0 832,0 H 697 Q 672,0 654,16.5 636,33 634,58 612,287 449.5,449.5 287,612 58,634 33,636 16.5,654 0,672 0,697 v 135 q 0,29 21,47 17,17 43,17 h 5 Q 229,883 375,815.5 521,748 634,634 748,521 815.5,375 883,229 896,69 z m 512,-2 Q 1410,40 1390,20 1372,0 1344,0 H 1201 Q 1175,0 1156.5,17.5 1138,35 1137,60 1125,275 1036,468.5 947,662 804.5,804.5 662,947 468.5,1036 275,1125 60,1138 35,1139 17.5,1157.5 0,1176 0,1201 v 143 q 0,28 20,46 18,18 44,18 h 3 Q 329,1395 568.5,1288 808,1181 994,994 1181,808 1288,568.5 1395,329 1408,67 z" fill="currentColor"/></g>
        </svg>');

        $this->extendUpdateObject('okay_cms__feed', self::PERMISSION, FeedsEntity::class);

    }
}