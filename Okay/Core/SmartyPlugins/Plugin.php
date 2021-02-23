<?php


namespace Okay\Core\SmartyPlugins;


use Okay\Core\Design;
use Okay\Core\Modules\Module;

abstract class Plugin
{
    
    final public function register(Design $design, Module $module)
    {
        $reflector = new \ReflectionClass($this);
        
        if (!empty($this->tag)) {
            $tag = $this->tag;
        } else {
            $tag = strtolower($reflector->getShortName());
        }
        
        if (!$reflector->hasMethod('run')) {
            throw new \Exception('smarty plugin not exists!! Okay\Core\Plugins\Plugin');
        }
        
        if ($this instanceof Modifier) {
            $design->registerPlugin('modifier', $tag, function(...$params) use ($design, $module) {
                if ($module->isModuleClass(static::class)) {
                    $moduleTemplateDir = $module->generateModuleTemplateDir(
                        $module->getVendorName(static::class),
                        $module->getModuleName(static::class)
                    );

                    $design->setModuleTemplatesDir($moduleTemplateDir);
                    $design->setModuleDir(static::class);

                    $result = call_user_func_array([$this, 'run'], $params);
                    $design->rollbackTemplatesDir();
                    return $result;
                }

                return call_user_func_array([$this, 'run'], $params);
            });
        } elseif ($this instanceof Func) {
            $design->registerPlugin('function', $tag, function($params, $smarty = null) use ($design, $module) {
                if ($module->isModuleClass(static::class)) {
                    $moduleTemplateDir = $module->generateModuleTemplateDir(
                        $module->getVendorName(static::class),
                        $module->getModuleName(static::class)
                    );

                    $design->setModuleTemplatesDir($moduleTemplateDir);
                    $design->setModuleDir(static::class);

                    $result = $this->run($params, $smarty);
                    $design->rollbackTemplatesDir();
                    return $result;
                }

                return $this->run($params, $smarty);
            });
        } else {
            throw new \Exception('smarty plugin bad instanceof!! Okay\Core\Plugins\Plugin');
        }
    }
}