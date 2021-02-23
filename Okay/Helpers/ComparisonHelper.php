<?php


namespace Okay\Helpers;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;

class ComparisonHelper
{
    private $entityFactory;
    private $design;

    public function __construct(EntityFactory $entityFactory, Design $design)
    {
        $this->entityFactory = $entityFactory;
        $this->design = $design;
    }

    public function getInformerTemplate()
    {
        $result['success'] = true;
        $result['template'] = $this->design->fetch('comparison_informer.tpl');

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }
}