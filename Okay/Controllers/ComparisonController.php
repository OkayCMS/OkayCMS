<?php


namespace Okay\Controllers;


use Okay\Core\Comparison;
use Okay\Core\Router;
use Okay\Helpers\ComparisonHelper;

class ComparisonController extends AbstractController
{
    
    public function render()
    {
        $this->design->assign('canonical', Router::generateUrl('comparison', [], true));
        $this->response->setContent('comparison.tpl');
    }
    
    public function ajaxUpdate(Comparison $comparison, ComparisonHelper $comparisonHelper)
    {

        $productId = $this->request->get('product', 'integer');
        $action = $this->request->get('action');
        if ($action == 'add') {
            $comparison->addItem($productId);
        } elseif($action == 'delete') {
            $comparison->deleteItem($productId);
        }

        $this->design->assign('comparison', $comparison->get());
        
        $result = $comparisonHelper->getInformerTemplate();
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
    
}
