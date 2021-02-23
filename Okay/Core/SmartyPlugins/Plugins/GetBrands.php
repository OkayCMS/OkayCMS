<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\EntityFactory;
use Okay\Entities\BrandsEntity;
use Okay\Core\SmartyPlugins\Func;
use Okay\Helpers\BrandsHelper;

class GetBrands extends Func
{

    protected $tag = 'get_brands';
    
    /** @var BrandsEntity */
    private $brands;
    
    /** @var BrandsHelper */
    private $brandsHelper;

    
    public function __construct(EntityFactory $entityFactory, BrandsHelper $brandsHelper)
    {
        $this->brands = $entityFactory->get(BrandsEntity::class);
        $this->brandsHelper = $brandsHelper;
    }

    public function run($params, \Smarty_Internal_Template $smarty)
    {
        if (!isset($params['visible'])) {
            $params['visible'] = 1;
        }

        $sort = isset($params['sort']) ? $params['sort'] : null;
        
        if (!empty($params['var'])) {
            $smarty->assign($params['var'], $this->brandsHelper->getList($params, $sort));
        }
    }
}