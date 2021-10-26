<?php


namespace Okay\Modules\OkayCMS\AutoDeploy\Init;


use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Entities\TranslationsEntity;
use Okay\Modules\OkayCMS\AutoDeploy\Entities\MigrationsEntity;
use Okay\Modules\OkayCMS\AutoDeploy\Extenders\BackendExtender;

class Init extends AbstractInit
{

    const PERMISSION = 'auto_deploy';
    
    /**
     * @inheritDoc
     */
    public function install()
    {
        $this->setBackendMainController('AutoDeployAdmin');
        $this->migrateEntityTable(MigrationsEntity::class, [
            (new EntityField('id'))->setTypeInt(11)->setAutoIncrement(),
            (new EntityField('name'))->setTypeVarchar(1024),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->registerBackendController('AutoDeployAdmin');
        
        $this->addBackendControllerPermission('AutoDeployAdmin', self::PERMISSION);
        
        $this->registerChainExtension(
            [TranslationsEntity::class, 'getWriteLangFile'],
            [BackendExtender::class, 'getWriteLangFile']
        );

        $this->registerChainExtension(
            [TranslationsEntity::class, 'getWriteModuleLangFile'],
            [BackendExtender::class, 'getWriteLangFile']
        );

        $this->registerQueueExtension(
            [TranslationsEntity::class, 'writeThemeTranslations'],
            [BackendExtender::class, 'writeThemeTranslations']
        );

        $this->registerQueueExtension(
            [TranslationsEntity::class, 'writeModuleTranslation'],
            [BackendExtender::class, 'writeModuleTranslation']
        );
        
        $this->registerChainExtension(
            [TranslationsEntity::class, 'initOneTranslation'],
            [BackendExtender::class, 'initOneTranslation']
        );

        $this->registerChainExtension(
            [TranslationsEntity::class, 'get'],
            [BackendExtender::class, 'get']
        );
        
        $this->addBackendBlock('translation_custom_block', 'translation_custom_block.tpl');
        $this->addBackendBlock('translations_custom_block', 'translations_custom_block.tpl');
    }
}