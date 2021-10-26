<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Controllers;

use Okay\Admin\Controllers\IndexAdmin;

class ModuleAdmin extends IndexAdmin
{
    public function fetch()
    {
        $this->response->setContent($this->design->fetch('module.tpl'));
    }
}