<?php


namespace Okay\Modules\OkayCMS\AdminGuide\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;

class AdminGuideAdmin extends IndexAdmin
{
    public function fetch()
    {
        $this->response->setContent($this->design->fetch('description.tpl'));
    }
}