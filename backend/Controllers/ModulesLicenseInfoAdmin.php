<?php


namespace Okay\Admin\Controllers;


class ModulesLicenseInfoAdmin extends IndexAdmin
{
    public function fetch()
    {
        $this->response->setContent($this->design->fetch('modules_license_info_admin.tpl'));
    }

}