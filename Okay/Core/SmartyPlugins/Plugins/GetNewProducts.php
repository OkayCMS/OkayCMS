<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\EntityFactory;
use Okay\Entities\ProductsEntity;
use Okay\Helpers\ProductsHelper;
use Okay\Core\SmartyPlugins\Func;

class GetNewProducts extends Func
{

    protected $tag = 'get_new_products';
    
    /**
     * @var ProductsEntity
     */
    private $productsEntity;
    
    /**
     * @var ProductsHelper
     */
    private $productsHelper;

    
    public function __construct(EntityFactory $entityFactory, ProductsHelper $productsHelper)
    {
        $this->productsEntity = $entityFactory->get(ProductsEntity::class);
        $this->productsHelper = $productsHelper;
    }

    public function run($params, \Smarty_Internal_Template $smarty)
    {
        if (!isset($params['visible'])) {
            $params['visible'] = 1;
        }
        
        if (!empty($params['var'])) {
            $sort = isset($params['sort']) ? $params['sort'] : 'created_desc';
            $products = $this->productsHelper->getList($params, $sort);
            $smarty->assign($params['var'], $products);
        }
    }
}