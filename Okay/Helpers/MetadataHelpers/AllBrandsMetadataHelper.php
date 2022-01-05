<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\PagesEntity;

class AllBrandsMetadataHelper extends CommonMetadataHelper
{
    /** @var string */
    private $keyword;

    public function setUp($keyword = null): void
    {
        $this->keyword = $keyword;
    }

    public function __construct()
    {
        parent::__construct();
        
        if (!$this->keyword) {
            $entityFactory = $this->SL->getService(EntityFactory::class);
            /** @var PagesEntity $pagesEntity */
            $pagesEntity = $entityFactory->get(PagesEntity::class);
            $this->page = $pagesEntity->get('brands');
        }
    }

    public function getH1Template(): string
    {
        $h1 = parent::getH1Template();

        if ($this->keyword !== null) {
            $h1 .= " «{$this->keyword}»";
        }
        
        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }
}