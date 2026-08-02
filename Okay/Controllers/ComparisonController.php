<?php

namespace Okay\Controllers;

use Okay\Core\Comparison;
use Okay\Core\Response;
use Okay\Core\Router;
use Okay\Helpers\ComparisonHelper;
use Okay\Helpers\ValidateHelper;

class ComparisonController extends AbstractController
{
    public function render()
    {
        $this->design->assign('noindex_follow', true);
        $this->design->assign('canonical', Router::generateUrl('comparison', [], true));
        $this->response->setContent('comparison.tpl');
    }

    public function ajaxUpdate(
        Comparison $comparison,
        ComparisonHelper $comparisonHelper,
        ValidateHelper $validateHelper
    ) {

        if (!$this->request->isPost()) {
            return $this->rejectComparisonMutation(405, 'method_not_allowed');
        }

        if ($error = $validateHelper->getCustomerCsrfError($this->request->post('customer_csrf_token'))) {
            return $this->rejectComparisonMutation(403, $error);
        }

        $productId = $this->request->post('product', 'integer');
        $action = $this->request->post('action');
        if ($action === 'add') {
            $comparison->addItem($productId);
        } elseif ($action === 'delete') {
            $comparison->deleteItem($productId);
        }

        $this->design->assign('comparison', $comparison->get());

        $result = $comparisonHelper->getInformerTemplate();
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }

    private function rejectComparisonMutation(int $statusCode, string $error): Response
    {
        $this->response->setStatusCode($statusCode);
        return $this->response->setContent(json_encode([
            'result' => 0,
            'error' => $error,
        ]), RESPONSE_JSON);
    }
}
