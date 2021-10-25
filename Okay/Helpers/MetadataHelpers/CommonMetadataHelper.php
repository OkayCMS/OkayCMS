<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\Design;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Money;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Helpers\MainHelper;

class CommonMetadataHelper implements MetadataInterface
{
    protected $parts = [];
    
    protected $page;
    /** @var Design */
    protected $design;
    /** @var Settings */
    protected $settings;
    /** @var Money */
    protected $money;
    /** @var MainHelper */
    protected $mainHelper;
    /** @var ServiceLocator */
    protected $SL;
    
    protected $h1 = '';
    protected $annotation = '';
    protected $description = '';
    protected $metaTitle = '';
    protected $metaKeywords = '';
    protected $metaDescription = '';

    public function __construct()
    {
        $SL = ServiceLocator::getInstance();
        $this->SL = $SL;
        $this->design = $SL->getService(Design::class);
        $this->settings = $SL->getService(Settings::class);
        $this->money = $SL->getService(Money::class);
        $this->mainHelper = $SL->getService(MainHelper::class);
        $this->page = $this->design->getVar('page');
    }
    
    /**
     * @inheritDoc
     */
    public function getH1Template(): string
    {
        if (empty($this->h1) && $this->page) {
            $this->h1 = empty($this->page->name_h1) ? $this->page->name : $this->page->name_h1;
        }
        
        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->h1, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getAnnotationTemplate(): string
    {
        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->annotation, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getDescriptionTemplate(): string
    {
        if (empty($this->description) && $this->page) {
            $this->description = $this->page->description;
        }
        
        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->description, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitleTemplate(): string
    {
        if (empty($this->metaTitle) && $this->page) {
            $this->metaTitle = $this->page->meta_title;
        }
        
        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->metaTitle, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeywordsTemplate(): string
    {
        if (empty($this->metaKeywords) && $this->page) {
            $this->metaKeywords = $this->page->meta_keywords;
        }
        
        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->metaKeywords, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescriptionTemplate(): string
    {
        if (empty($this->metaDescription) && $this->page) {
            $this->metaDescription = $this->page->meta_description;
        }
        
        return ExtenderFacade::execute([static::class, __FUNCTION__], $this->metaDescription, func_get_args());
    }
    
    public function getH1(): string
    {
        $h1 = $this->compileMetadata($this->getH1Template());
        return ExtenderFacade::execute([static::class, __FUNCTION__], $h1, func_get_args());
    }

    public function getAnnotation(): string
    {
        $annotation = $this->compileMetadata($this->getAnnotationTemplate());
        return ExtenderFacade::execute([static::class, __FUNCTION__], $annotation, func_get_args());
    }

    public function getDescription(): string
    {
        $description = $this->compileMetadata($this->getDescriptionTemplate());
        return ExtenderFacade::execute([static::class, __FUNCTION__], $description, func_get_args());
    }

    public function getMetaTitle(): string
    {
        $title = $this->compileMetadata($this->getMetaTitleTemplate());
        return ExtenderFacade::execute([static::class, __FUNCTION__], $title, func_get_args());
    }

    public function getMetaKeywords(): string
    {
        $keywords = $this->compileMetadata($this->getMetaKeywordsTemplate());
        return ExtenderFacade::execute([static::class, __FUNCTION__], $keywords, func_get_args());
    }

    public function getMetaDescription(): string
    {
        $description = $this->compileMetadata($this->getMetaDescriptionTemplate());
        return ExtenderFacade::execute([static::class, __FUNCTION__], $description, func_get_args());
    }

    /**
     * @return array
     */
    protected function getParts(): array
    {
        if (!empty($this->parts)) {
            return $this->parts; // no ExtenderFacade
        }
        
        if ($page = $this->design->getVar('page')) {

            $this->parts = [
                '{$page}' => ($page->name ? $page->name : ''),
                '{$page_h1}' => ($page->name_h1 ? $page->name_h1 : ''),
            ];
        }
        
        return $this->parts = ExtenderFacade::execute([static::class, __FUNCTION__], $this->parts, func_get_args());
    }

    protected function compileMetadata($pattern)
    {
        $metaData = strtr($pattern, $this->getParts());
        $metaData = trim(preg_replace('/{\$[^$]*}/', '', $metaData));
        return ExtenderFacade::execute([static::class, __FUNCTION__], $metaData, func_get_args());
    }
}