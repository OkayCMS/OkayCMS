<?php


namespace Okay\Core\TplMod;


use Okay\Core\Config;
use Okay\Core\ServiceLocator;
use Okay\Core\TplMod\Nodes\BaseNode;
use Okay\Core\TplMod\Nodes\HtmlCommentNode;
use Okay\Core\TplMod\Nodes\TextNode;

class TplMod
{
    private $parser;
    private $debug;
    
    public function __construct(Parser $parser, Config $config)
    {
        $this->parser = $parser;
        $this->debug = (bool)$config->get('dev_mode');
    }

    public function buildFile($content, $mods)
    {
        $SL = ServiceLocator::getInstance();
        
        /** @var Config $config */
        $config = $SL->getService(Config::class);
        
        if ($config->get('disable_tpl_mod')) {
            return $content; // todo отключение модификаторов
        }
        
        $res = $this->parser->parse($content);
        
        $this->walkByFile($res, $mods);
        
        //print $this->build($res);die; // todo вывод содержимого файла
        
        return $this->build($res);
    }
    
    private function walkByFile(BaseNode &$node, array $mods)
    {
        foreach ($mods as &$mod) {
            if (!empty($mod->find) && strpos($node->getOriginalElement(), $mod->find) !== false) {
                $this->applyMod($node, $mod);
            } elseif (!empty($mod->like) && preg_match('~'.$mod->like.'~', $node->getOriginalElement())) {
                $this->applyMod($node, $mod);
            }
        }
        
        if ($node->children()) {
            foreach ($node->children() as $child) {
                $this->walkByFile($child, $mods);
            }
        }
    }

    private function applyMod(BaseNode $node, $mod)
    {
        // Вдруг запросили относительную ноду
        if (property_exists($mod, 'parent')) {
            $node = $node->parent();
        }
        
        if (property_exists($mod, 'closestFind')) {
            while ($node = $node->parent()) {
                if (strpos($node->getOriginalElement(), $mod->closestFind) !== false) {
                    break;
                }
            }
        } elseif (property_exists($mod, 'closestLike')) {
            while ($node = $node->parent()) {
                if (preg_match('~'.$mod->closestFind.'~', $node->getOriginalElement())) {
                    break;
                }
            }
        }
        
        if (property_exists($mod, 'childrenFind')) {
            if ($childNode = $this->findChildNode($node, $mod->childrenFind)) {
                $node = $childNode;
            } else {
                return;
            }
        } elseif (property_exists($mod, 'childrenLike')) {
            if ($childNode = $this->likeChildNode($node, $mod->childrenLike)) {
                $node = $childNode;
            } else {
                return;
            }
        }
        
        if (property_exists($mod, 'append')) {
            $userNode = new TextNode($mod->append);
            if ($this->debug === true && !empty($mod->comment)) {
                $node->append(new HtmlCommentNode("<!--{$mod->comment}-->"));
            }
            $node->append($userNode);
            if ($this->debug === true && !empty($mod->comment)) {
                $node->append(new HtmlCommentNode("<!--/{$mod->comment}-->"));
            }
        }

        if (property_exists($mod, 'appendBefore')) {
            $userNode = new TextNode($mod->appendBefore);
            if ($this->debug === true && !empty($mod->comment)) {
                $node->appendBefore(new HtmlCommentNode("<!--{$mod->comment}-->"));
            }
            $node->appendBefore($userNode);
            if ($this->debug === true && !empty($mod->comment)) {
                $node->appendBefore(new HtmlCommentNode("<!--/{$mod->comment}-->"));
            }
        }
        
        if (property_exists($mod, 'prepend')) {
            $userNode = new TextNode($mod->prepend);
            if ($this->debug === true && !empty($mod->comment)) {
                $node->prepend(new HtmlCommentNode("<!--/{$mod->comment}-->"));
            }
            $node->prepend($userNode);
            if ($this->debug === true && !empty($mod->comment)) {
                $node->prepend(new HtmlCommentNode("<!--{$mod->comment}-->"));
            }
        }

        if (property_exists($mod, 'appendAfter')) {
            $userNode = new TextNode($mod->appendAfter);
            if ($this->debug === true && !empty($mod->comment)) {
                $node->appendAfter(new HtmlCommentNode("<!--/{$mod->comment}-->"));
            }
            $node->appendAfter($userNode);
            if ($this->debug === true && !empty($mod->comment)) {
                $node->appendAfter(new HtmlCommentNode("<!--{$mod->comment}-->"));
            }
        }

        if (property_exists($mod, 'html')) {
            $userNode = new TextNode($mod->html);
            $node->text($userNode);
            if ($this->debug === true && !empty($mod->comment)) {
                $node->prepend(new HtmlCommentNode("<!--replaced by {$mod->comment}-->"));
            }
        }

        if (property_exists($mod, 'text')) {
            $userNode = new TextNode($mod->text);
            $node->text($userNode);
            if ($this->debug === true && !empty($mod->comment)) {
                $node->prepend(new HtmlCommentNode("<!--replaced by {$mod->comment}-->"));
            }
        }

        if (property_exists($mod, 'replace')) {
            $node->modifyElement($mod->replace);
        }

        if (property_exists($mod, 'remove')) {
            $node->remove();
        }
        unset($node);
    }
    
    private function findChildNode(BaseNode $node, $search)
    {
        $result = false;
        if ($children = $node->children()) {
            foreach ($children as $child) {
                if (strpos($child->getOriginalElement(), $search) !== false) {
                    return $child;
                }
                if ($result = $this->findChildNode($child, $search)) {
                    return $result;
                }
            }
        }
        return $result;
    }
    
    private function likeChildNode(BaseNode $node, $search)
    {
        $result = false;
        if ($children = $node->children()) {
            foreach ($children as $child) {
                if (preg_match('~'.$search.'~', $child->getOriginalElement())) {
                    return $child;
                }
                if ($result = $this->likeChildNode($child, $search)) {
                    return $result;
                }
            }
        }
        return $result;
    }
    
    private function build(BaseNode $node, $level = 0)
    {
        $resultString = '';
        /** @var BaseNode $child */
        foreach ($node->children() as $child) {
            if (strpos($node->getOriginalElement(), '<textarea') === false) {
                $resultString .= PHP_EOL;
                
                // Добавляем отступы для форматирования
                for ($i=1; $i<=$level; $i++) {
                    $resultString .= '    ';
                }
            }

            $resultString .= $child->getElement();

            if (!empty($child->children())) {
                $resultString .= $this->build($child, $level+1);
            }

            if (!empty($child->getCloseTag())) {
                // Добавляем отступы для форматирования
                if (!empty($child->children()) && strpos($child->getOriginalElement(), '<textarea') === false) {
                    $resultString .= PHP_EOL;
                    for ($i = 1; $i <= $level; $i++) {
                        $resultString .= '    ';
                    }
                }
                $resultString .= $child->getCloseTag();
            }

        }
        return $resultString;
    }
    
}