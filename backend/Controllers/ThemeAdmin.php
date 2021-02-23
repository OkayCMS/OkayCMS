<?php


namespace Okay\Admin\Controllers;


use Okay\Entities\ManagersEntity;

class ThemeAdmin extends IndexAdmin
{

    private $themes_dir = 'design/';
    private $compiled_dir = 'compiled/';

    /*Работа с шаблонами сайта*/
    public function fetch(ManagersEntity $managersEntity)
    {
        if ($this->request->method('post')) {

            if (isset($_POST['admin_theme']) && $_POST['admin_theme'] != $this->settings->get('theme')) {
                $this->settings->set('admin_theme', $this->request->post('admin_theme'));
            }
            $admin_theme_managers = $this->request->post('admin_theme_managers');
            $this->settings->set('admin_theme_managers', $admin_theme_managers == 'all' ? '' : $admin_theme_managers);

            $this->dirDelete($this->compiled_dir, false, ['.htaccess']);
            $old_names = $this->request->post('old_name');
            $new_names = $this->request->post('new_name');
            if (is_array($old_names)) {
                foreach ($old_names as $i=>$old_name) {
                    $new_name = preg_replace("/[^a-zA-Z0-9\-_]/", "", $new_names[$i]);

                    if (is_writable($this->themes_dir) && is_dir($this->themes_dir.$old_name) && !is_file($this->themes_dir.$new_name)&& !is_dir($this->themes_dir.$new_name)) {
                        rename($this->themes_dir.$old_name, $this->themes_dir.$new_name);
                        if($this->settings->get('admin_theme') == $old_name) {
                            $this->settings->set('admin_theme', $new_name);
                        }
                        if($this->settings->get('theme') == $old_name) {
                            $this->settings->set('theme', $new_name);
                        }
                    }
                }
            }

            $action = $this->request->post('action');
            $action_theme  = $this->request->post('theme');

            switch ($action) {
                case 'set_main_theme': {
                    /*Установить тему*/
                    if ($action_theme == $this->settings->get('admin_theme')) {
                        $this->settings->set('admin_theme', '');
                    }
                    $this->settings->set('theme', $action_theme);
                    break;
                }
                case 'clone_theme': {
                    /*Сдлать копию темы*/
                    $new_name = $this->settings->get('theme');
                    while (is_dir($this->themes_dir.$new_name) || is_file($this->themes_dir.$new_name)) {
                        if (preg_match('/(.+)_([0-9]+)$/', $new_name, $parts)) {
                            $new_name = $parts[1].'_'.($parts[2]+1);
                        } else {
                            $new_name = $new_name.'_1';
                        }
                    }
                    $this->dirCopy($this->themes_dir.$this->settings->get('theme'), $this->themes_dir.$new_name);
                    @unlink($this->themes_dir.$new_name.'/locked');
                    $this->settings->set('theme', $new_name);
                    break;
                }
                case 'delete_theme': {
                    /*Удалить тему*/
                    $this->dirDelete($this->themes_dir.$action_theme);
                    if ($action_theme == $this->settings->get('admin_theme')) {
                        $this->settings->set('admin_theme', '');
                    }
                    if ($action_theme == $this->settings->get('theme')) {
                        $t = current($this->getThemes());
                        $this->settings->set('theme', $t->name);
                    }
                    break;
                }
            }
        }

        $themes = $this->getThemes();

        // Если нет прав на запись - передаем в дизайн предупреждение
        if (!is_writable($this->themes_dir)) {
            $this->design->assign('message_error', 'permissions');
        }

        $current_theme = new \stdClass;
        $current_theme->name = $this->settings->get('theme');
        $current_theme->locked = is_file($this->themes_dir.$current_theme->name.'/locked');
        $managers = $managersEntity->find();
        $this->design->assign('managers', $managers);
        $admin_theme_managers = $this->settings->get('admin_theme_managers');
        $this->design->assign('admin_theme_managers', $admin_theme_managers);
        $this->design->assign('theme', $current_theme);
        $this->design->assign('themes', $themes);
        $this->design->assign('themes_dir', $this->themes_dir);
        $this->response->setContent($this->design->fetch('theme.tpl'));
    }

    private function dirCopy($src, $dst) {
        if(is_dir($src)) {
            mkdir($dst, 0755);
            $files = scandir($src);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $this->dirCopy("$src/$file", "$dst/$file");
                }
            }
            @chmod($dst, 0755);
        } elseif(file_exists($src)) {
            copy($src, $dst);
            @chmod($dst, 0664);
        }
    }

    private function dirDelete($path, $delete_self = true, $ignore = []) {
        if(!$dh = @opendir($path)) {
            return;
        }
        while (false !== ($obj = readdir($dh))) {
            if($obj == '.' || $obj == '..' || in_array($obj, $ignore)) {
                continue;
            }

            if (!@unlink($path . '/' . $obj)) {
                $this->dirDelete($path.'/'.$obj, true);
            }
        }
        closedir($dh);
        if($delete_self) {
            @rmdir($path);
        }
        return;
    }

    private function getThemes() {
        $themes = [];
        if($handle = opendir($this->themes_dir)) {
            while(false !== ($file = readdir($handle))) {
                if(is_dir($this->themes_dir.'/'.$file) && $file[0] != '.') {
                    $theme = new \stdClass;
                    $theme->name = $file;
                    $theme->locked = is_file($this->themes_dir.$file.'/locked');
                    $themes[] = $theme;
                }
            }
            closedir($handle);
            sort($themes);
        }
        return $themes;
    }

}
