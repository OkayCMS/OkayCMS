<?php


namespace Okay\Modules\OkayCMS\Integration1C\Integration\Export\ExportFactory;


use Okay\Modules\OkayCMS\Integration1C\Integration\AbstractFactory;
use Okay\Modules\OkayCMS\Integration1C\Integration\Export;

class ExportFactory extends AbstractFactory
{
    
    public function create($exportType)
    {
        $exportType = strtolower($exportType);
        switch ($exportType) {
            case 'orders':
                if (class_exists(\Okay\Modules\OkayCMS\Integration1C\Integration\Export\Overrides\ExportOrders::class)) {
                    return new \Okay\Modules\OkayCMS\Integration1C\Integration\Export\Overrides\ExportOrders($this->integration1C);
                }
                return new Export\ExportOrders($this->integration1C);
                break;
            default:
                throw new \Exception('Unknown export type: "' . $exportType . '"');
        }
    }
}