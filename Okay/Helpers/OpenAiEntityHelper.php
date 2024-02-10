<?php

namespace Okay\Helpers;

use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Helpers\AiRequests\AbstractAiRequest;
use Okay\Helpers\AiRequests\AiProductRequest;

class OpenAiEntityHelper
{
    public const ENTITY_TYPE_PRODUCT = 'product';

    public function getRequest(string $entity, ?int $entityId, ?string $name): ?AbstractAiRequest
    {
        if ($entity == self::ENTITY_TYPE_PRODUCT) {
            return new AiProductRequest($entityId, $name);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}