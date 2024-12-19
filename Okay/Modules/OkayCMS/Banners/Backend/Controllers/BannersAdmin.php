<?php


namespace Okay\Modules\OkayCMS\Banners\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Modules\OkayCMS\Banners\Entities\BannersEntity;
use Okay\Modules\OkayCMS\Banners\Helpers\BannersBackupHelper;
use Okay\Modules\OkayCMS\Banners\Helpers\BannersHelper;
use Okay\Modules\OkayCMS\Banners\VO\RestoreBackupErrorVO;

class BannersAdmin extends IndexAdmin
{
    public function fetch(
        BannersEntity $bannersEntity,
        BannersHelper $bannersHelper,
        BannersBackupHelper $bannersBackupHelper
    ) {

        $filter = $bannersHelper->buildFilter();

        if (($backupZipFile = $this->request->files('banners'))) {
            if ($backupZipFile['error'] == UPLOAD_ERR_OK
                && pathinfo($backupZipFile['name'], PATHINFO_EXTENSION) == 'zip'
            ) {
                $destination = sys_get_temp_dir() . '/backup_' . uniqid() . '.zip';
                if (move_uploaded_file($backupZipFile['tmp_name'], $destination)) {
                    $errors = $bannersBackupHelper->restoreBackup($destination);
                     if (file_exists($destination)) {
                         unlink($destination);
                     }
                     $this->design->assign('restore_backup_errors', $errors);
                } else {
                    $this->design->assign('restore_backup_errors', [new RestoreBackupErrorVO(RestoreBackupErrorVO::UNZIP_ERROR)]);
                }
            }

        } elseif ($this->request->method('post')) {
            $ids = $this->request->post('check');
            if (is_array($ids)) {
                switch ($this->request->post('action')) {
                    case 'disable': {
                        /*Выключаем группы баннеров*/
                        $bannersEntity->update($ids, ['visible' => 0]);
                        break;
                    }
                    case 'enable': {
                        /*Включаем группы баннеров*/
                        $bannersEntity->update($ids, ['visible' => 1]);
                        break;
                    }
                    case 'delete': {
                        /*Удаляем группы баннеров*/
                        $bannersEntity->delete($ids);
                        break;
                    }
                    case 'backup': {
                        /*Створюємо бекап обраних груп баннерів*/
                        $backupFilename = $bannersBackupHelper->backup($ids);
                        $archiveName = "backup.zip";
                        $this->response->addHeader("Content-type: application/zip");
                        $this->response->addHeader("Content-Disposition: attachment; filename=\"{$archiveName}\"");
                        $this->response->addHeader("Content-Length: " . filesize($backupFilename));
                        $this->response->addHeader("Pragma: public");
                        $this->response->addHeader("Cache-Control: must-revalidate");
                        $this->response->addHeader("Expires: 0");

                        $this->response->sendHeaders();
                        readfile($backupFilename);
                        unlink($backupFilename);
                        exit();
                    }
                }
            }
            
            // Сортировка
            $positions = $this->request->post('positions');
            $ids = array_keys($positions);
            sort($positions);
            foreach($positions as $i=>$position) {
                $bannersEntity->update($ids[$i], ['position'=>$position]);
            }
        }

        $bannersCount              = $bannersHelper->countBannersImages($filter);
        list($filter, $pagesCount) = $bannersHelper->makePagination($bannersCount, $filter);
        $banners = $bannersHelper->getBannersListForAdmin($filter);

        $this->design->assign('banners_count', $bannersCount);
        $this->design->assign('pages_count', $pagesCount);
        $this->design->assign('current_page', $filter['page']);
        
        $this->design->assign('banners', $banners);

        $this->response->setContent($this->design->fetch('banners.tpl'));
    }
    
}
