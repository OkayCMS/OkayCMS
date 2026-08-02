<?php

namespace Okay\Core\DebugBar\DataFormatters;

use DebugBar\DataFormatter\QueryFormatter as LibQueryFormatter;

class QueryFormatter extends LibQueryFormatter
{
    /**
     * @param array<int|string, mixed> $bindings
     */
    public function formatSqlWithBindings(string $sql, array $bindings, ?\PDO $pdo = null): string
    {
        foreach ($bindings as $key => $binding) {
            if (is_bool($binding)) {
                $bindings[$key] = (int) $binding;
            }
        }

        return parent::formatSqlWithBindings($sql, $bindings, $pdo);
    }
}
