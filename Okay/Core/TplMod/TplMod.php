<?php


namespace Okay\Core\TplMod;


use Okay\Core\Config;
use Okay\Core\Modules\DTO\TplChangeDTO;
use Okay\Core\ServiceLocator;
use Okay\Core\TplMod\Nodes\BaseNode;
use Okay\Core\TplMod\Nodes\HtmlCommentNode;
use Okay\Core\TplMod\Nodes\TextNode;

class TplMod
{
    private Parser $parser;
    private bool $debug;
    
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

    /**
     * @param BaseNode $node
     * @param TplChangeDTO[] $changes
     * @return void
     */
    private function walkByFile(BaseNode $node, array $changes)
    {
        foreach ($changes as $changeDTO) {
            if (!empty($changeDTO->getFind()) && strpos($node->getOriginalElement(), $changeDTO->getFind()) !== false) {
                $this->applyMod($node, $changeDTO);
            } elseif (!empty($changeDTO->getLike()) && preg_match('~'.$changeDTO->getLike().'~', $node->getOriginalElement())) {
                $this->applyMod($node, $changeDTO);
            }
        }
        
        if ($node->children()) {
            foreach ($node->children() as $child) {
                $this->walkByFile($child, $changes);
            }
        }
    }

    private function applyMod(BaseNode $node, TplChangeDTO $changeDTO)
    {
        // Вдруг запросили относительную ноду
        if ($changeDTO->isParent()) {
            $node = $node->parent();
        }
        
        if (!empty($changeDTO->getClosestFind())) {
            while ($node = $node->parent()) {
                if (strpos($node->getOriginalElement(), $changeDTO->getClosestFind()) !== false) {
                    break;
                }
            }
        } elseif (!empty($changeDTO->getClosestLike())) {
            while ($node = $node->parent()) {
                if (preg_match('~'.$changeDTO->getClosestLike().'~', $node->getOriginalElement())) {
                    break;
                }
            }
        }
        
        if (!empty($changeDTO->getChildrenFind())) {
            if ($childNode = $this->findChildNode($node, $changeDTO->getChildrenFind())) {
                $node = $childNode;
            } else {
                return;
            }
        } elseif (!empty($changeDTO->getChildrenLike())) {
            if ($childNode = $this->likeChildNode($node, $changeDTO->getChildrenLike())) {
                $node = $childNode;
            } else {
                return;
            }
        }
        
        if (!empty($changeDTO->getAppend())) {
            $userNode = new TextNode($changeDTO->getAppend());
            if ($this->debug === true && !empty($changeDTO->getComment())) {
                $node->append(new HtmlCommentNode("<!--{$changeDTO->getComment()}-->"));
            }
            $node->append($userNode);
            if ($this->debug === true && !empty($changeDTO->getComment())) {
                $node->append(new HtmlCommentNode("<!--/{$changeDTO->getComment()}-->"));
            }
        }

        if (!empty($changeDTO->getAppendBefore())) {
            $userNode = new TextNode($changeDTO->getAppendBefore());
            if ($this->debug === true && !empty($changeDTO->getComment())) {
                $node->appendBefore(new HtmlCommentNode("<!--{$changeDTO->getComment()}-->"));
            }
            $node->appendBefore($userNode);
            if ($this->debug === true && !empty($changeDTO->getComment())) {
                $node->appendBefore(new HtmlCommentNode("<!--/{$changeDTO->getComment()}-->"));
            }
        }
        
        if (!empty($changeDTO->getPrepend())) {
            $userNode = new TextNode($changeDTO->getPrepend());
            if ($this->debug === true && !empty($changeDTO->getComment())) {
                $node->prepend(new HtmlCommentNode("<!--/{$changeDTO->getComment()}-->"));
            }
            $node->prepend($userNode);
            if ($this->debug === true && !empty($changeDTO->getComment())) {
                $node->prepend(new HtmlCommentNode("<!--{$changeDTO->getComment()}-->"));
            }
        }

        if (!empty($changeDTO->getAppendAfter())) {
            $userNode = new TextNode($changeDTO->getAppendAfter());
            if ($this->debug === true && !empty($changeDTO->getComment())) {
                $node->appendAfter(new HtmlCommentNode("<!--/{$changeDTO->getComment()}-->"));
            }
            $node->appendAfter($userNode);
            if ($this->debug === true && !empty($changeDTO->getComment())) {
                $node->appendAfter(new HtmlCommentNode("<!--{$changeDTO->getComment()}-->"));
            }
        }

        if (!empty($changeDTO->getHtml())) {
            $userNode = new TextNode($changeDTO->getHtml());
            $node->text($userNode);
            if ($this->debug === true && !empty($changeDTO->getComment())) {
                $node->prepend(new HtmlCommentNode("<!--replaced by {$changeDTO->getComment()}-->"));
            }
        }

        if (!empty($changeDTO->getText())) {
            $userNode = new TextNode($changeDTO->getText());
            $node->text($userNode);
            if ($this->debug === true && !empty($changeDTO->getComment())) {
                $node->prepend(new HtmlCommentNode("<!--replaced by {$changeDTO->getComment()}-->"));
            }
        }

        if (!empty($changeDTO->getReplace())) {
            $node->modifyElement($changeDTO->getReplace());
        }

        if ($changeDTO->isRemove()) {
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
    
    private function build(BaseNode $node, $level = 0): string
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