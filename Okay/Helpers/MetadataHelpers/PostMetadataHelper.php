<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\Modules\Extender\ExtenderFacade;

class PostMetadataHelper extends CommonMetadataHelper
{
    /** @var object */
    private $post;

    public function setUp(object $post): void
    {
        $this->post = $post;
    }

    public function getH1Template(): string
    {
        if ($pageH1 = parent::getH1Template()) {
            $h1 = $pageH1;
        } else {
            $h1 = (string)$this->post->name;
        }
        
        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }
    
    public function getAnnotationTemplate(): string
    {
        if ($pageAnnotation = parent::getAnnotationTemplate()) {
            $annotation = $pageAnnotation;
        } else {
            $annotation = (string)$this->post->annotation;
        }

        return ExtenderFacade::execute(__METHOD__, $annotation, func_get_args());
    }
    
    public function getDescriptionTemplate(): string
    {
        if ($pageDescription = parent::getDescriptionTemplate()) {
            $description = $pageDescription;
        } else {
            $description = (string)$this->post->description;
        }

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }
    
    public function getMetaTitleTemplate(): string
    {
        if ($pageTitle = parent::getMetaTitleTemplate()) {
            $metaTitle = $pageTitle;
        } else {
            $metaTitle = (string)$this->post->meta_title;
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }
    
    public function getMetaKeywordsTemplate(): string
    {
        if ($pageKeywords = parent::getMetaKeywordsTemplate()) {
            $metaKeywords = $pageKeywords;
        } else {
            $metaKeywords = (string)$this->post->meta_keywords;
        }

        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }
    
    public function getMetaDescriptionTemplate(): string
    {
        if ($pageMetaDescription = parent::getMetaDescriptionTemplate()) {
            $metaDescription = $pageMetaDescription;
        } else {
            $metaDescription = (string)$this->post->meta_description;
        }

        return ExtenderFacade::execute(__METHOD__, $metaDescription, func_get_args());
    }
}