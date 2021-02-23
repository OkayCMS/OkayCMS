<?php


namespace Okay\Core\TplMod\Nodes;


class BaseNode
{
    
    protected $element;
    protected $elementModified;
    protected $selfClose = false;
    protected $close;
    protected $children = [];
    protected $patent;

    /**
     * BaseNode constructor.
     * @param $element
     * @param string|false $close название закрывающего тега, если передать false - считается что тег самозакрывающийся
     */
    public function __construct($element, $close = '')
    {
        $this->element = $element;
        if ($close === false) {
            $this->selfClose = true;
        } elseif (!empty($close)) {
            $this->close = $close;
        }
    }

    public function modifyElement($element)
    {
        $this->elementModified = $element;
    }
    
    public function getElement()
    {
        return !empty($this->elementModified) ? $this->elementModified : $this->element;
    }

    public function getOriginalElement()
    {
        return $this->element;
    }
    
    /**
     * Метод возвращает массив дочерних элементов
     * 
     * @return array
     */
    public function children()
    {
        return $this->children;
    }
    
    // Метод заменяет всё содержимое элемента новым элементом
    public function text(TextNode $node)
    {
        // Удаляем все дочерние ноды
        $this->removeAllChild();
        // Добавляем новую ноду
        $this->append($node);
    }
    
    public function remove()
    {
        if (!empty($this->parent())) {
            $parent = $this->parent();
            $childrenOfPrent = $parent->children();
            // Удаляем все дочерние элементы родителя
            $parent->removeAllChild();
            foreach ($childrenOfPrent as $child) {
                // Возвращаем в дочерние элементы все, кроме текущего
                if ($child !== $this) {
                    $parent->append($child);
                    //break;
                }
            }
        }
    }

    public function removeAllChild()
    {
        $this->children = [];
    }

    public function appendBefore(BaseNode $node)
    {
        $parent = $this->parent();
        foreach ($parent->children() as $key => $child) {
            if ($child === $this) {
                $parent->addChildByIndex($node, $key);
                break;
            }
        }
    }
    
    public function appendAfter(BaseNode $node)
    {
        $parent = $this->parent();
        foreach ($parent->children() as $key => $child) {
            if ($child === $this) {
                $parent->addChildByIndex($node, $key+1);
                break;
            }
        }
    }

    public function append(BaseNode $children)
    {
        if ($this->selfClose === true) {
            throw new \Exception('Self-closing tag can\'t have children nodes');
        }
        $children->setParent($this);
        $this->children[] = $children;
    }

    public function prepend(BaseNode $children)
    {
        if ($this->selfClose === true) {
            throw new \Exception('Self-closing tag can\'t have children nodes');
        }
        $children->setParent($this);
        array_unshift($this->children, $children);
    }

    /**
     * @return BaseNode
     */
    public function parent()
    {
        return $this->patent;
    }

    public function setCloseTag($close)
    {
        $this->close = $close;
    }

    public function getCloseTag()
    {
        return $this->close;
    }
    
    protected function setParent(BaseNode $parent)
    {
        $this->patent = $parent;
    }

    protected function addChildByIndex(BaseNode $node, $index)
    {
        array_splice( $this->children, $index, 0, [$node]);
    }
}