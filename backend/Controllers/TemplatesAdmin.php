<?php


namespace Okay\Admin\Controllers;


use Okay\Core\TemplateConfig\FrontTemplateConfig;

class TemplatesAdmin extends IndexAdmin
{

    /*Чтение файлов шаблона*/
    public function fetch(FrontTemplateConfig $frontTemplateConfig)
    {
        $currentTheme = $frontTemplateConfig->getTheme();

        if ($this->request->get("email")){
            $templatesDir = 'design/'.$currentTheme.'/html/email/';
            $this->design->assign('current_dir', 'email');
        } else {
            $templatesDir = 'design/'.$currentTheme.'/html/';
            $this->design->assign('current_dir', 'html');
        }

        $templates = [];
        // Читаем все tpl-файлы
        if($handle = opendir($templatesDir)) {
            while(false !== ($file = readdir($handle))) {
                if(is_file($templatesDir.$file) && $file[0] != '.'  && pathinfo($file, PATHINFO_EXTENSION) == 'tpl') {
                    $templates[] = $file;
                }
            }
            closedir($handle);
            asort($templates);
        }

        // Текущий шаблон
        $templateFile = $this->request->get('file');
        
        if (!empty($templateFile) && pathinfo($templateFile, PATHINFO_EXTENSION) != 'tpl') {
            exit();
        }
        
        if (!isset($templateFile)){
            $templateFile = reset($templates);
        }

        // Передаем имя шаблона в дизайн
        $this->design->assign('template_file', $templateFile);
        
        // Если можем прочитать файл - передаем содержимое в дизайн
        if (is_readable($templatesDir.$templateFile)) {
            $template_content = file_get_contents($templatesDir.$templateFile);
            $this->design->assign('template_content', $template_content);
        }
        
        // Если нет прав на запись - передаем в дизайн предупреждение
        if (!empty($templateFile) && !is_writable($templatesDir.$templateFile) && !is_file($templatesDir.'../locked')) {
            $this->design->assign('message_error', 'permissions');
        } elseif (is_file($templatesDir.'../locked')) {
            $this->design->assign('message_error', 'theme_locked');
        } else {
            // Запоминаем в сессии имя редактируемого шаблона
            $_SESSION['last_edited_template'] = $templateFile;
        }

        $this->design->assign('theme', $currentTheme);
        $this->design->assign('templates', $templates);
        
        $this->response->setContent($this->design->fetch('templates.tpl'));
    }
    
}
