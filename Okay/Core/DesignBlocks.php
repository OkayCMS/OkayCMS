<?php


namespace Okay\Core;


use Okay\Core\DebugBar\DebugBar;
use Okay\Core\OkayContainer\MethodDI;

/**
 * Class DesignBlocks
 * @package Okay\Core
 * 
 * Класс для работы с блоками в админке. Чтобы зарегистрировать ещё один блок,
 * нужно добавить его здесь и добавить его вызов в соответствующем месте админки
 * 
 * Пример вызова:
    <div class="okay_block_wrap">
        {$block = {get_design_block block="product_variant"}}
        {if !empty($block) || $config->dev_mode}
            {if $config->dev_mode}
            <div class="block_name">product_variant</div>
            {/if}
            {$variant_block}
        {/if}
    </div>
 */

class DesignBlocks
{
    use MethodDI;

    /**
     * @var array список зарегистрированных блоков
     */
    private $registeredBlocks;
    
    private $callbacks = [];
    
    private $design;
    private $entityFactory;
    private $SL;

    public function __construct(Design $design, EntityFactory $entityFactory)
    {
        $this->design = $design;
        $this->entityFactory = $entityFactory;
        $this->SL = ServiceLocator::getInstance();
    }

    /**
     * @param string $blockName название блока
     *
     * @param string $blockTplFile путь к .tpl файлу из модуля, который представляет верстку блока
     * @param string $callback callback ф-ция, которую нужно выполнить во время отработки шортблока
     * @throws \Exception
     *
     * Регистрация блока для админки
     */
    public function registerBlock($blockName, $blockTplFile, $callback = null)
    {
        if (!is_file($blockTplFile)) {
            throw new \Exception('File ' . $blockTplFile . ' not found');
        }
        
        if (!empty($callback)) {
            $this->callbacks[$blockName][$blockTplFile][] = $callback;
        }
        
        $this->registeredBlocks[$blockName][] = $blockTplFile;
    }

    /**
     * @param string $blockName
     * @return string
     * Метод компилирует и возвращает HTML блока
     */
    public function getBlockHtml($blockName)
    {
        $blockHtml = '';
        if (!empty($this->registeredBlocks[$blockName])) {
            // Разворачиваем, потому что модули регистрируются обратно тому,
            // как они расположены в админ панели, а их отображение должно соответствовать
            // порядку в админ панели
            $reversedBlocks = array_reverse($this->registeredBlocks[$blockName]);
            foreach ($reversedBlocks as $blockTplFile) {
                DebugBar::startDesignBlockFetch($blockTplFile);
                // Если с блоком регистрировали калбеки, запускаем их в порядке регистрации
                if (!empty($this->callbacks[$blockName][$blockTplFile])) {
                    foreach ($this->callbacks[$blockName][$blockTplFile] as $callback) {
                        call_user_func_array($callback, $this->getMethodArguments(new \ReflectionFunction($callback)));
                    }
                }
                
                $blockHtml .= $this->design->fetch($blockTplFile);
                DebugBar::finishDesignBlockFetch($blockName, $blockTplFile);
            }
        }
        return $blockHtml;
    }

}
