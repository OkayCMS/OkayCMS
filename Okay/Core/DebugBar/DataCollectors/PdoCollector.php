<?php

namespace Okay\Core\DebugBar\DataCollectors;

use DebugBar\DataCollector\PDO\PDOCollector as LibPdoCollector;
use DebugBar\DataFormatter\QueryFormatter as LibQueryFormatter;
use Okay\Core\DebugBar\DataFormatters\QueryFormatter;

class PdoCollector extends LibPdoCollector
{
    public function getQueryFormatter(): LibQueryFormatter
    {
        if ($this->queryFormatter === null) {
            $this->queryFormatter = new QueryFormatter();
        }

        return $this->queryFormatter;
    }
}
