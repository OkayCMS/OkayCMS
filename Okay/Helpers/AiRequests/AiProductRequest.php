<?php

namespace Okay\Helpers\AiRequests;

use Okay\Core\EntityFactory;
use Okay\Core\ServiceLocator;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;

class AiProductRequest extends AbstractAiRequest
{
    private string $additionalInfo = '';

    public function __construct(?int $entityId, ?string $name)
    {
        parent::__construct($entityId, $name);

        if (!$this->entityId) {
            return;
        }
        $SL = ServiceLocator::getInstance();
        $entityFactory = $SL->getService(EntityFactory::class);
        $featuresValuesEntity = $entityFactory->get(FeaturesValuesEntity::class);
        $featuresEntity = $entityFactory->get(FeaturesEntity::class);

        $featuresValues = [];
        foreach ($featuresValuesEntity->find(['product_id' => $this->entityId]) as $fv) {
            $featuresValues[$fv->feature_id][$fv->id] = $fv;
        }
        $featuresIds = array_keys($featuresValues);
        if (!empty($featuresIds)) {
            $additionalInfo = [];
            foreach ($featuresEntity->find(['id' => $featuresIds]) as $f) {
                if (!empty($featuresValues[$f->id])) {
                    $values = [];
                    foreach ($featuresValues[$f->id] as $fv) {
                        $values[] = $fv->value;
                    }
                    $additionalInfo[] = sprintf(
                        '%s: %s',
                        $f->name,
                        implode(', ', $values)
                    );
                }
            }
            $this->additionalInfo = implode("\n", $additionalInfo);
        }
    }

    public function getRequestText(string $field): string
    {
        return "Згенеруй мені унікальний текст для товару на 800 символів\n '{$this->name}' з такими ключовими словами.\nКупити у Дніпрі, найкраща ціна, безкоштовна доставка";
    }

    public function getAdditionalInfo(): string
    {
        return $this->additionalInfo;
    }
}