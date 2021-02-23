<?php


namespace Okay\Modules\OkayCMS\Integration1C\Integration\Import;


use Okay\Modules\OkayCMS\Integration1C\Integration\Integration1C;

abstract class AbstractImport
{

    /** @var Integration1C */
    protected $integration1C;

    public function __construct(Integration1C $integration1C)
    {
        $this->integration1C = $integration1C;
    }
    
    /**
     * @param string $xmlFile Full path to xml file
     * @return string
     */
    abstract public function import($xmlFile);
    
}
