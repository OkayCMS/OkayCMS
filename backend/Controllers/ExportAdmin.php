<?php


namespace Okay\Admin\Controllers;


use Okay\Entities\BrandsEntity;
use Okay\Admin\Helpers\BackendExportHelper;

class ExportAdmin extends IndexAdmin
{
    
    private $exportFilesDir = 'backend/files/export/';

    /*Экспорт товаров*/
    public function fetch(BrandsEntity $brandsEntity, BackendExportHelper $backendExportHelper){
        $this->design->assign('export_files_dir', $this->exportFilesDir);
        if (!is_writable($this->exportFilesDir)) {
            $this->design->assign('message_error', 'no_permission');
        }

        $brands = [];
        $categories = [];
        $brandsCount = $brandsEntity->count();
        $brands = $backendExportHelper->getBrandsForExportFilter($brandsCount);
        $categories = $backendExportHelper->getCategoriesForExportFilter();
        $this->design->assign('brands', $brands);
        $this->design->assign('categories', $categories);

        $this->response->setContent($this->design->fetch('export.tpl'));
    }
    
}
