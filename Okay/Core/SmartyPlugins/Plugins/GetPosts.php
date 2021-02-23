<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\EntityFactory;
use Okay\Entities\BlogEntity;
use Okay\Core\SmartyPlugins\Func;
use Okay\Helpers\BlogHelper;

class GetPosts extends Func
{

    protected $tag = 'get_posts';
    
    /** @var BlogEntity */
    private $blogEntity;
    
    /** @var BlogHelper */
    private $blogHelper;

    
    public function __construct(EntityFactory $entityFactory, BlogHelper $blogHelper)
    {
        $this->blogEntity = $entityFactory->get(BlogEntity::class);
        $this->blogHelper = $blogHelper;
    }

    public function run($params, \Smarty_Internal_Template $smarty)
    {
        if (!isset($params['visible'])) {
            $params['visible'] = 1;
        }

        $sort = isset($params['sort']) ? $params['sort'] : null;
        
        if (!empty($params['var'])) {
            $smarty->assign($params['var'], $this->blogHelper->getList($params, $sort));
        }
    }
}