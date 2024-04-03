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
        $format = $this->request->get('format', 'string') == 'true';
        $aiRequest = $openAiEntityHelper->getRequest($entity, $entityId, $name);
        if (!$aiRequest) {
            return $this->response->setContent("event: stop\ndata: stopped\n\n", RESPONSE_GPT_STREAM);
        }

        $openAiHelper->streamMetadata(
            $aiRequest->getRequestText($field),
            $aiRequest->getAdditionalInfo(),
            $format
        );
    }
}
