<?php

namespace Okay\Helpers;

use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Helpers\AiRequests\AbstractAiRequest;
use Okay\Helpers\AiRequests\AiBrandRequest;
use Okay\Helpers\AiRequests\AiCategoryRequest;
use Okay\Helpers\AiRequests\AiProductRequest;

class OpenAiEntityHelper
{
    public function getRequest(string $entity, ?int $entityId, ?string $name): ?AbstractAiRequest
    {
        if ($entity == AiProductRequest::ENTITY_TYPE) {
            return new AiProductRequest($entityId, $name);
        }
        if ($entity == AiCategoryRequest::ENTITY_TYPE) {
            return new AiCategoryRequest($entityId, $name);
        }
        if ($entity == AiBrandRequest::ENTITY_TYPE) {
            return new AiBrandRequest($entityId, $name);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}