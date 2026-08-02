<?php

namespace Okay\Core\DebugBar\DataCollectors;

class ConfigCollector extends \DebugBar\DataCollector\ConfigCollector
{
    public function set(string $name, mixed $value, string $source = ''): void
    {
        if (!isset($this->data[$name])) {
            $this->data[$name] = [];
        }

        array_unshift($this->data[$name], [
            'value' => $value,
            'source' => $source
        ]);
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function collect(): array
    {
        $data = array();
        foreach ($this->data as $name => $changes) {
            foreach ($changes as $i => $params) {
                if (!is_string($params['value'])) {
                    $params['value'] = $this->getDataFormatter()->formatVar($params['value']);
                }
                $data[$name][$i] = $params;
            }
        }
        ksort($data);

        return $data;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getWidgets(): array
    {
        $name = $this->getName();
        return array(
            "$name" => array(
                "icon" => "adjustments",
                "widget" => "PhpDebugBar.Widgets.OkayVariableListWidget",
                "map" => "$name",
                "default" => "{}"
            )
        );
    }
}
