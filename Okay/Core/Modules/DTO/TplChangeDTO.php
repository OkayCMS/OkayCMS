<?php

namespace Okay\Core\Modules\DTO;

class TplChangeDTO
{
    private string $find;
    private string $like;
    private bool $parent = false;
    private string $closestFind = '';
    private string $closestLike = '';
    private string $childrenFind = '';
    private string $childrenLike = '';

    private string $append = '';
    private string $appendBefore = '';
    private string $prepend = '';
    private string $appendAfter = '';
    private string $html = '';
    private string $text = '';
    private string $replace = '';
    private bool $remove = false;
    private string $comment = '';

    public function __construct(string $find, string $like)
    {
        $this->find = $find;
        $this->like = $like;
    }

    /**
     * @return string
     */
    public function getFind(): string
    {
        return $this->find;
    }

    /**
     * @return string
     */
    public function getLike(): string
    {
        return $this->like;
    }

    /**
     * @return string
     */
    public function getAppend(): string
    {
        return $this->append;
    }

    /**
     * @param string $append
     * @return self
     */
    public function setAppend(string $append): self
    {
        $this->append = $append;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppendBefore(): string
    {
        return $this->appendBefore;
    }

    /**
     * @param string $appendBefore
     * @return self
     */
    public function setAppendBefore(string $appendBefore): self
    {
        $this->appendBefore = $appendBefore;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrepend(): string
    {
        return $this->prepend;
    }

    /**
     * @param string $prepend
     * @return self
     */
    public function setPrepend(string $prepend): self
    {
        $this->prepend = $prepend;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppendAfter(): string
    {
        return $this->appendAfter;
    }

    /**
     * @param string $appendAfter
     * @return self
     */
    public function setAppendAfter(string $appendAfter): self
    {
        $this->appendAfter = $appendAfter;
        return $this;
    }

    /**
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * @param string $html
     * @return self
     */
    public function setHtml(string $html): self
    {
        $this->html = $html;
        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return self
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getReplace(): string
    {
        return $this->replace;
    }

    /**
     * @param string $replace
     * @return self
     */
    public function setReplace(string $replace): self
    {
        $this->replace = $replace;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRemove(): bool
    {
        return $this->remove;
    }

    /**
     * @param bool $remove
     * @return self
     */
    public function setRemove(bool $remove = true): self
    {
        $this->remove = $remove;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return self
     */
    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return bool
     */
    public function isParent(): bool
    {
        return $this->parent;
    }

    /**
     * @param bool $parent
     * @return self
     */
    public function setParent(bool $parent = true): self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return string
     */
    public function getClosestFind(): string
    {
        return $this->closestFind;
    }

    /**
     * @param string $closestFind
     * @return self
     */
    public function setClosestFind(string $closestFind): self
    {
        $this->closestFind = $closestFind;
        return $this;
    }

    /**
     * @return string
     */
    public function getClosestLike(): string
    {
        return $this->closestLike;
    }

    /**
     * @param string $closestLike
     * @return self
     */
    public function setClosestLike(string $closestLike): self
    {
        $this->closestLike = $closestLike;
        return $this;
    }

    /**
     * @return string
     */
    public function getChildrenFind(): string
    {
        return $this->childrenFind;
    }

    /**
     * @param string $childrenFind
     * @return self
     */
    public function setChildrenFind(string $childrenFind): self
    {
        $this->childrenFind = $childrenFind;
        return $this;
    }

    /**
     * @return string
     */
    public function getChildrenLike(): string
    {
        return $this->childrenLike;
    }

    /**
     * @param string $childrenLike
     * @return self
     */
    public function setChildrenLike(string $childrenLike): self
    {
        $this->childrenLike = $childrenLike;
        return $this;
    }
}