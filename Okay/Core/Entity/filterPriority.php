<?php


namespace Okay\Core\Entity;


trait filterPriority
{
    public function addHighPriority($filterName)
    {
        $this->highPriorityFilters[$filterName] = $filterName;
        $this->removeLowPriority($filterName);
        return $this;
    }

    public function addLowPriority($filterName)
    {
        $this->lowPriorityFilters[$filterName] = $filterName;
        $this->removeHighPriority($filterName);
        return $this;
    }

    public function removeHighPriority($filterName)
    {
        unset($this->highPriorityFilters[$filterName]);
        return $this;
    }

    public function removeLowPriority($filterName)
    {
        unset($this->lowPriorityFilters[$filterName]);
        return $this;
    }

    public function resetPriority()
    {
        $this->highPriorityFilters = [];
        $this->lowPriorityFilters = [];
        return $this;
    }
    
    public function orderFilterByPriority(array $filter = [])
    {
        $resultHigh = [];
        $resultLow = [];
        
        foreach($this->getHighPriority() as $highPriorityName)
        {
            if (isset($filter[$highPriorityName])) {
                $resultHigh[$highPriorityName] = $filter[$highPriorityName];
                unset($filter[$highPriorityName]);
            }
        }
        
        foreach($this->getLowPriority() as $lowPriorityName)
        {
            if (isset($filter[$lowPriorityName])) {
                $resultLow[$lowPriorityName] = $filter[$lowPriorityName];
                unset($filter[$lowPriorityName]);
            }
        }
        
        return array_merge($resultHigh, $filter, $resultLow);
    }
    
    private function getHighPriority()
    {
        return (array)$this->highPriorityFilters;
    }

    private function getLowPriority()
    {
        return (array)$this->lowPriorityFilters;
    }
}