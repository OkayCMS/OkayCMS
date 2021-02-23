<?php


namespace Okay\Modules\OkayCMS\Integration1C\Integration\Import\ImportFactory;


use Okay\Modules\OkayCMS\Integration1C\Integration\AbstractFactory;
use Okay\Modules\OkayCMS\Integration1C\Integration\Import;

class ImportFactory extends AbstractFactory
{
    
    public function create($importType)
    {
        $importType = strtolower($importType);
        switch ($importType) {
            case 'orders':
                if (class_exists(\Okay\Modules\OkayCMS\Integration1C\Integration\Import\Overrides\ImportOrders::class)) {
                    return new \Okay\Modules\OkayCMS\Integration1C\Integration\Import\Overrides\ImportOrders($this->integration1C);
                }
                return new Import\ImportOrders($this->integration1C);
                break;
            case 'products':
                if (class_exists(\Okay\Modules\OkayCMS\Integration1C\Integration\Import\Overrides\ImportProducts::class)) {
                    return new \Okay\Modules\OkayCMS\Integration1C\Integration\Import\Overrides\ImportProducts($this->integration1C);
                }
                return new Import\ImportProducts($this->integration1C);
                break;
            case 'offers':
                if (class_exists(\Okay\Modules\OkayCMS\Integration1C\Integration\Import\Overrides\ImportOffers::class)) {
                    return new \Okay\Modules\OkayCMS\Integration1C\Integration\Import\Overrides\ImportOffers($this->integration1C);
                }
                return new Import\ImportOffers($this->integration1C);
                break;
            default:
                throw new \Exception('Unknown import type: "' . $importType . '"');
        }
    }
}