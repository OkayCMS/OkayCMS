<?php


namespace Okay\Core;


use Okay\Core\Entity\Entity;

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

                // Если с блоком регистрировали калбеки, запускаем их в порядке регистрации
                if (!empty($this->callbacks[$blockName][$blockTplFile])) {
                    foreach ($this->callbacks[$blockName][$blockTplFile] as $callback) {
                        $reflectionMethod = new \ReflectionFunction($callback);
                        $methodParams = [];
                        foreach ($reflectionMethod->getParameters() as $parameter) {

                            if ($parameter->getClass() !== null) { // Если для аргумента указан type hint, передадим экземляр соответствующего класса
                                // Определяем это Entity или сервис из DI
                                if (is_subclass_of($parameter->getClass()->name, Entity::class)) {
                                    $methodParams[$parameter->getClass()->name] = $this->entityFactory->get($parameter->getClass()->name);
                                } else {
                                    $methodParams[$parameter->getClass()->name] = $this->SL->getService($parameter->getClass()->name);
                                }
                            } else { // Если не нашли значения аргументу, и он не имеет значения по умолчанию в ф-ции - ошибка
                               
                                throw new \Exception("Missing argument \"\${$parameter->name}\" in callback for block \"{$blockName}\"");
                            }
                        }
                        call_user_func_array($callback, $methodParams);
                    }
                }
                
                $blockHtml .= $this->design->fetch($blockTplFile);
            }
        }
        return $blockHtml;
    }

}
