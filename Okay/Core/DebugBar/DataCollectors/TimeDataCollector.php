<?php

namespace Okay\Core\DebugBar\DataCollectors;

use \DebugBar\DataCollector\TimeDataCollector as LibTimeDataCollector;
use DebugBar\DebugBarException;

class TimeDataCollector extends LibTimeDataCollector
{
    public function startMeasure($name, $label = null, $collector = null, $aggregate = false)
    {
        $start = microtime(true);
        $this->startedMeasures[$name] = array(
            'name' => $name,
            'label' => $label ?: $name,
            'start' => $start,
            'collector' => $collector,
            'aggregate' => $aggregate
        );
    }

    public function stopMeasure($name, $params = array())
    {
        $end = microtime(true);
        if (!$this->hasStartedMeasure($name)) {
            throw new DebugBarException("Failed stopping measure '$name' because it hasn't been started");
        }
        $this->addMeasure(
            $this->startedMeasures[$name]['label'],
            $this->startedMeasures[$name]['start'],
            $end,
            $params,
            $this->startedMeasures[$name]['collector'],
            $this->startedMeasures[$name]['aggregate'],
            $this->startedMeasures[$name]['name']
        );
        unset($this->startedMeasures[$name]);
    }

    public function addMeasure($label, $start, $end, $params = array(), $collector = null, $aggregate = false, $name = null)
    {
        $this->measures[] = array(
            'name' => $name ?? $label,
            'label' => $label,
            'start' => $start,
            'relative_start' => $start - $this->requestStartTime,
            'end' => $end,
            'relative_end' => $end - $this->requestEndTime,
            'duration' => $end - $start,
            'duration_str' => $this->getDataFormatter()->formatDuration($end - $start),
            'params' => $params,
            'collector' => $collector,
            'aggregate' => $aggregate
        );
    }

    public function getWidgets()
    {
        return array(
            "time" => array(
                "icon" => "clock-o",
                "tooltip" => "Request Duration",
                "map" => "time.duration_str",
                "default" => "'0ms'"
            ),
            "timeline" => array(
                "icon" => "tasks",
                "widget" => "PhpDebugBar.Widgets.OkayTimelineWidget",
                "map" => "time",
                "default" => "{}"
            )
        );
    }
}