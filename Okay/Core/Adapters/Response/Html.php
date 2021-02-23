<?php


namespace Okay\Core\Adapters\Response;


use Okay\Core\Design;
use Okay\Core\ServiceLocator;

class Html extends AbstractResponse
{

    /** @var Design */
    private $design;
    
    public function __construct()
    {
        $serviceLocator = ServiceLocator::getInstance();
        $this->design = $serviceLocator->getService(Design::class);
    }

    public function getSpecialHeaders()
    {
        return [
            'Content-type: text/html; charset=utf-8',
        ];
    }
    
    public function send($contents)
    {
        $resultContent = '';
        if (is_array($contents)) {
            foreach ($contents as $content) {
                // Проверяем нам передали итоговую HTML или имя файла шаблона
                if ($this->design->templateExists($content)) {
                    $resultContent .= $this->design->fetch($content);
                } else {
                    $resultContent .= $content;
                }
            }
        } else {
            // Проверяем нам передали итоговую HTML или имя файла шаблона
            if ($this->design->templateExists($contents)) {
                $resultContent .= $this->design->fetch($contents);
            } else {
                $resultContent .= $contents;
            }
        }

        $this->design->assign('content', $resultContent);
        
        // Создаем текущую обертку сайта (обычно index.tpl)
        $wrapper = $this->design->getVar('wrapper');
        if (is_null($wrapper)) {
            $wrapper = 'index.tpl';
        }

        if (!empty($wrapper)) {
            print $this->design->fetch($wrapper);
        } else {
            print $resultContent;
        }
    }
}
