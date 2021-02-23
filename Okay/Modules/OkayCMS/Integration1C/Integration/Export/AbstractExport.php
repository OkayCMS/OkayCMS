<?php

namespace Okay\Modules\OkayCMS\Integration1C\Integration\Export;


use Okay\Modules\OkayCMS\Integration1C\Integration\Integration1C;

abstract class AbstractExport
{

    /** @var Integration1C */
    protected $integration1C;
    
    public function __construct(Integration1C $integration1C)
    {
        $this->integration1C = $integration1C;
    }
    
    /**
     * @return string
     */
    abstract public function export();
    
}
