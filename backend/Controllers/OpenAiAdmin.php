<?php


namespace Okay\Admin\Controllers;


use Okay\Helpers\OpenAiEntityHelper;
use Okay\Helpers\OpenAiHelper;

class OpenAiAdmin extends IndexAdmin
{
    
    public function fetch(
        OpenAiHelper $openAiHelper,
        OpenAiEntityHelper $openAiEntityHelper
    ) {
        $field = $this->request->get('field');
        $entity = $this->request->get('entity');
        $name = $this->request->get('name');
        $entityId = $this->request->get('entityId', 'int');
        $aiRequest = $openAiEntityHelper->getRequest($entity, $entityId, $name);
        $openAiHelper->streamMetadata(
            $aiRequest->getRequestText($field),
            $aiRequest->getAdditionalInfo()
        );
//        var_dump($aiRequest->getRequestText($field));
    }
}
