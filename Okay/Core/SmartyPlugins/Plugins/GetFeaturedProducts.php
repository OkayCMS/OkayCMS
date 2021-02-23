<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\EntityFactory;
use Okay\Entities\ProductsEntity;
use Okay\Helpers\ProductsHelper;
use Okay\Core\SmartyPlugins\Func;

class GetFeaturedProducts extends Func
{

    protected $tag = 'get_featured_products';
    
    /**
     * @var ProductsEntity
     */
    private $productsEntity;
    
    /**
     * @var Products
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
        $params['featured'] = 1;
        if (!empty($params['var'])) {
            $sort = isset($params['sort']) ? $params['sort'] : null;
            $products = $this->productsHelper->getList($params, $sort);
            $smarty->assign($params['var'], $products);
        }
    }
}