<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\EntityFactory;
use Okay\Entities\PagesEntity;

class BestsellersMetadataHelper extends CommonMetadataHelper
{
    
    public function __construct()
    {
        parent::__construct();
        
        $entityFactory = $this->SL->getService(EntityFactory::class);
        /** @var PagesEntity $pagesEntity */
        $pagesEntity = $entityFactory->get(PagesEntity::class);
        $this->page = $pagesEntity->get('bestsellers');
    }
}