<?php


namespace Okay\Core\DebugBar\DataCollectors;


class ConfigCollector extends \DebugBar\DataCollector\ConfigCollector
{
    protected $useHtmlVarDumper = false;

    public function set($name, $value, $source = '')
    {
        if (!isset($this->data[$name])) {
            $this->data[$name] = [];
        }

        array_unshift($this->data[$name], [
            'value' => $value,
            'source' => $source
        ]);
    }

    public function collect()
    {
        $data = array();
        foreach ($this->data as $name => $changes) {
            foreach ($changes as $i => $params) {
                if ($this->isHtmlVarDumperUsed()) {
                    $params['value'] = $this->getVarDumper()->renderVar($params['value']);
                } else if (!is_string($params['value'])) {
                    $params['value'] = $this->getDataFormatter()->formatVar($params['value']);
                }
                $data[$name][$i] = $params;
            }
        }
        ksort($data);

        return $data;
    }

    public function getWidgets()
    {
        $name = $this->getName();
        $widget = $this->isHtmlVarDumperUsed()
            ? "PhpDebugBar.Widgets.HtmlVariableListWidget"
            : "PhpDebugBar.Widgets.OkayVariableListWidget";
        return array(
            "$name" => array(
                "icon" => "gear",
                "widget" => $widget,
                "map" => "$name",
                "default" => "{}"
            )
        );
    }
}