<?php


namespace Okay\Helpers;


interface GetListInterface
{
    /**
     * @param array $filter фильтр, который передастся в Entity
     * @param null $sortName название сотрировки
     * @param null|false|array $excludedFields поля, которые стоит исключить из выборки. null - использовать стандартный
     * набор, false - не исключать (доставать вообще все поля), array - свой набор полей, которые стоит исключить.
     * @return array
     * @throws \Exception
     */
    
    public function getList($filter = [], $sortName = null, $excludedFields = null);

    public function getExcludeFields();
    
}