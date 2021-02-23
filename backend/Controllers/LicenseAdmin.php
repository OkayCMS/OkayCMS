<?php


namespace Okay\Admin\Controllers;


class LicenseAdmin extends IndexAdmin
{

    public function fetch()
    {
        $this->response->setContent($this->design->fetch('license.tpl'));
    }

}
