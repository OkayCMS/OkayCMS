<?php


namespace Okay\Modules\OkayCMS\Banners\Init;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Core\QueryFactory;
use Okay\Core\ServiceLocator;
use Okay\Helpers\MainHelper;
use Okay\Helpers\MetadataHelpers\BrandMetadataHelper;
use Okay\Helpers\MetadataHelpers\CategoryMetadataHelper;
use Okay\Helpers\MetadataHelpers\CommonMetadataHelper;
use Okay\Helpers\MetadataHelpers\PostMetadataHelper;
use Okay\Helpers\MetadataHelpers\ProductMetadataHelper;
use Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO;
use Okay\Modules\OkayCMS\Banners\DTO\BannerSettingsDTO;
use Okay\Modules\OkayCMS\Banners\Entities\BannersEntity;
use Okay\Modules\OkayCMS\Banners\Entities\BannersImagesEntity;
use Okay\Modules\OkayCMS\Banners\Extenders\FrontExtender;

class Init extends AbstractInit
{

    const PERMISSION = 'okaycms_banners';
    
    public function install()
    {
        
        if (!is_dir('files/originals/slides')) {
            mkdir('files/originals/slides');
        }
        
        if (!is_dir('files/resized/slides')) {
            mkdir('files/resized/slides');
        }
        
        $this->setBackendMainController('BannersAdmin');
        $this->migrateEntityTable(BannersEntity::class, [
            (new EntityField('id'))->setTypeInt(11)->setAutoIncrement(),
            (new EntityField('name'))->setTypeVarchar(255),
            (new EntityField('group_name'))->setTypeVarchar(255, true),
            (new EntityField('position'))->setTypeInt(11)->setDefault(0)->setIndex(),
            (new EntityField('visible'))->setTypeTinyInt(1, true)->setDefault(1)->setIndex(),
            (new EntityField('show_all_pages'))->setTypeTinyInt(1, true)->setDefault(1)->setIndex(),
            (new EntityField('show_all_products'))->setTypeTinyInt(1, true)->setDefault(0)->setIndex(),
            (new EntityField('categories'))->setTypeVarchar(255)->setDefault(''),
            (new EntityField('pages'))->setTypeVarchar(255)->setDefault(''),
            (new EntityField('brands'))->setTypeVarchar(255)->setDefault(''),
            (new EntityField('as_individual_shortcode'))->setTypeTinyInt(1, true)->setDefault(0),
            (new EntityField('settings'))->setTypeText(),
        ]);
        
        $this->migrateEntityTable(BannersImagesEntity::class, [
            (new EntityField('id'))->setTypeInt(11)->setAutoIncrement(),
            (new EntityField('banner_id'))->setTypeInt(11)->setIndex(),
            (new EntityField('name'))->setTypeVarchar(255)->setDefault('')->setIsLang(),
            (new EntityField('alt'))->setTypeVarchar(255)->setDefault('')->setIsLang(),
            (new EntityField('title'))->setTypeVarchar(255)->setDefault('')->setIsLang(),
            (new EntityField('url'))->setTypeVarchar(255)->setDefault('')->setIsLang(),
            (new EntityField('description'))->setTypeText()->setIsLang(),
            (new EntityField('image'))->setTypeVarchar(255)->setDefault(''),
            (new EntityField('position'))->setTypeInt(11)->setDefault(0)->setIndex(),
            (new EntityField('visible'))->setTypeTinyInt(1, true)->setDefault(1)->setIndex(),
            (new EntityField('settings'))->setTypeText(),
        ]);
    }
    
    public function init()
    {
        $this->registerBackendController('BannersAdmin');
        $this->registerBackendController('BannerAdmin');
        $this->registerBackendController('BannersImageAdmin');
        $this->registerBackendController('BannersImagesAdmin');
        
        $this->addBackendControllerPermission('BannersAdmin', self::PERMISSION);
        $this->addBackendControllerPermission('BannerAdmin', self::PERMISSION);
        $this->addBackendControllerPermission('BannersImageAdmin', self::PERMISSION);
        $this->addBackendControllerPermission('BannersImagesAdmin', self::PERMISSION);
        
        $this->registerQueueExtension(
            [MainHelper::class, 'commonAfterControllerProcedure'],
            [FrontExtender::class, 'assignCurrentBanners']
        );
        
        $this->registerChainExtension(
            [CategoryMetadataHelper::class, 'getParts'],
            [FrontExtender::class, 'metadataGetParts']
        );
        
        $this->registerChainExtension(
            [BrandMetadataHelper::class, 'getParts'],
            [FrontExtender::class, 'metadataGetParts']
        );
        
        $this->registerChainExtension(
            [ProductMetadataHelper::class, 'getParts'],
            [FrontExtender::class, 'metadataGetParts']
        );
        
        $this->registerChainExtension(
            [PostMetadataHelper::class, 'getParts'],
            [FrontExtender::class, 'metadataGetParts']
        );
        
        $this->registerChainExtension(
            [CommonMetadataHelper::class, 'getParts'],
            [FrontExtender::class, 'metadataGetParts']
        );
        
        $this->extendBackendMenu('left_banners', [
            'left_banners_title' => ['BannersAdmin', 'BannerAdmin'],
            'left_banners_images_title' => ['BannersImagesAdmin', 'BannersImageAdmin'],
        ], '<svg width="20" height="20" viewBox="0 0 24 15" xmlns="http://www.w3.org/2000/svg">
            <path d="M.918.04h22.164v14.92H.918V.04zM2.2 1.349v12.344h19.614V1.348H2.2zm1.616 5.939l3.968-3.608v7.216L3.816 7.287zm16.475 0l-4 3.636V3.651l4 3.636z" fill="currentColor" />
        </svg>');
        
        
        $this->addFastMenuItem('slide', [
            'controller' => 'OkayCMS.Banners.BannerAdmin',
            'translation' => 'admintooltip_edit_banner',
            'params' => [
                'banner_slide_id' => 'id',
            ],
            'action' => 'edit',
        ], [
            'controller' => 'OkayCMS.Banners.BannerAdmin',
            'translation' => 'admintooltip_add_banner',
        ], [
            'controller' => 'OkayCMS.Banners.BannersImageAdmin',
            'translation' => 'admintooltip_edit_slide',
            'params' => [
                'banner_slide_id' => 'id',
            ],
            'action' => 'edit',
        ], [
            'controller' => 'OkayCMS.Banners.BannersImageAdmin',
            'translation' => 'admintooltip_add_slide',
            'params' => [
                'banner_slide_id_add' => 'id',
            ],
        ]);
        
        $this->addResizeObject('banners_images_dir', 'resized_banners_images_dir');

        $this->extendUpdateObject('okay_cms__banners', self::PERMISSION, BannersEntity::class);
        $this->extendUpdateObject('okay_cms__banners_images', self::PERMISSION, BannersImagesEntity::class);
    }
    
    public function update_1_0_1()
    {
        $SL = ServiceLocator::getInstance();

        /** @var QueryFactory $queryFactory */
        $queryFactory = $SL->getService(QueryFactory::class);
        
        $sql = $queryFactory->newSqlQuery();
        $sql->setStatement("ALTER TABLE " . BannersImagesEntity::getLangTable() . " ADD `image` varchar(255) NULL DEFAULT ''")->execute();

        $sql = $queryFactory->newSqlQuery();
        $sql->setStatement("UPDATE " . BannersImagesEntity::getLangTable() . " AS l LEFT JOIN " . BannersImagesEntity::getTable() . " AS b ON l." . BannersImagesEntity::getLangObject() . "_id = b.id SET l.image=b.image")->execute();
        
        $this->migrateEntityField(BannersImagesEntity::class, (new EntityField('is_lang_banner'))->setTypeTinyInt(1, true)->setDefault(1));
        
    }

    public function update_1_1_0()
    {
        $this->migrateEntityField(
            BannersImagesEntity::class,
            (new EntityField('image_mobile'))
                ->setTypeVarchar(255)
                ->setIsLang()
                ->setDefault('')
        );
        $SL = ServiceLocator::getInstance();

        $entityFactory = $SL->getService(EntityFactory::class);
        $bannersEntity = $entityFactory->get(BannersEntity::class);
        $bannersImagesEntity = $entityFactory->get(BannersImagesEntity::class);

        $banners = $bannersEntity->noLimit()->find();
        $bannersImages = $bannersImagesEntity->noLimit()->find();

        foreach ($banners as $banner) {
            $settings = unserialize($banner->settings);
            if (is_array($settings)) {
                $bannerSettingsDTO = new BannerSettingsDTO();
                $bannerSettingsDTO->setAsSlider((bool)($settings['as_slider'] ?? true));
                $bannerSettingsDTO->setAutoplay((bool)($settings['autoplay'] ?? true));
                $bannerSettingsDTO->setLoop((bool)($settings['loop'] ?? false));
                $bannerSettingsDTO->setNav((bool)($settings['nav'] ?? false));
                $bannerSettingsDTO->setDots((bool)($settings['dots'] ?? false));
                $bannerSettingsDTO->setRotationSpeed((int)($settings['rotation_speed'] ?? BannerSettingsDTO::DEFAULT_ROTATION_SPEED));

                $bannersEntity->update($banner->id, [
                    'settings' => serialize($bannerSettingsDTO),
                ]);
            }
        }

        foreach ($bannersImages as $bannerImage) {
            $settings = unserialize($bannerImage->settings);
            if (is_array($settings)) {
                $bannerImageSettingsDTO = new BannerImageSettingsDTO();
                $bannerImageSettingsDTO->setDesktopWidth((int)($settings['desktop']['w'] ?? BannerImageSettingsDTO::DEFAULT_DESKTOP_W));
                $bannerImageSettingsDTO->setDesktopHeight((int)($settings['desktop']['h'] ?? BannerImageSettingsDTO::DEFAULT_DESKTOP_H));
                $bannerImageSettingsDTO->setMobileWidth((int)($settings['mobile']['w'] ?? BannerImageSettingsDTO::DEFAULT_MOBILE_W));
                $bannerImageSettingsDTO->setMobileHeight((int)($settings['mobile']['h'] ?? BannerImageSettingsDTO::DEFAULT_MOBILE_H));
                $bannerImageSettingsDTO->setVariantShow($settings['variant_show'] ?? BannerImageSettingsDTO::SHOW_DEFAULT);

                $bannersImagesEntity->update($bannerImage->id, [
                    'settings' => serialize($bannerImageSettingsDTO),
                ]);
            }
        }
    }
}