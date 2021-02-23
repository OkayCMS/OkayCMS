<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Entities\SpecialImagesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendSpecialImagesHelper
{
    private $specialImagesEntity;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->specialImagesEntity = $entityFactory->get(SpecialImagesEntity::class);
    }

    public function findSpecialImages()
    {
        $specialImages = $this->specialImagesEntity->find();
        return ExtenderFacade::execute(__METHOD__, $specialImages, func_get_args());
    }
}