<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\FeaturesAliasesValuesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesAliasesValuesEntity;
use Okay\Entities\SEOFilterPatternsEntity;
use Okay\Helpers\FilterHelper;

class PostMetadataHelper extends CommonMetadataHelper
{
 

    public function getH1Template(): string
    {
        $post = $this->design->getVar('post');

        if ($pageH1 = parent::getH1Template()) {
            $h1 = $pageH1;
        } else {
            $h1 = (string)$post->name;
        }
        
        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }
    
    public function getAnnotationTemplate(): string
    {
        $post = $this->design->getVar('post');
        
        if ($pageAnnotation = parent::getAnnotationTemplate()) {
            $annotation = $pageAnnotation;
        } else {
            $annotation = (string)$post->annotation;
        }

        return ExtenderFacade::execute(__METHOD__, $annotation, func_get_args());
    }
    
    public function getDescriptionTemplate(): string
    {
        $post = $this->design->getVar('post');
        
        if ($pageDescription = parent::getDescriptionTemplate()) {
            $description = $pageDescription;
        } else {
            $description = (string)$post->description;
        }

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }
    
    public function getMetaTitleTemplate(): string
    {
        $post = $this->design->getVar('post');
        if ($pageTitle = parent::getMetaTitleTemplate()) {
            $metaTitle = $pageTitle;
        } else {
            $metaTitle = (string)$post->meta_title;
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }
    
    public function getMetaKeywordsTemplate(): string
    {
        $post = $this->design->getVar('post');
        
        if ($pageKeywords = parent::getMetaKeywordsTemplate()) {
            $metaKeywords = $pageKeywords;
        } else {
            $metaKeywords = (string)$post->meta_keywords;
        }

        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }
    
    public function getMetaDescriptionTemplate(): string
    {
        $post = $this->design->getVar('post');
        
        if ($pageMetaDescription = parent::getMetaDescriptionTemplate()) {
            $metaDescription = $pageMetaDescription;
        } else {
            $metaDescription = (string)$post->meta_description;
        }

        return ExtenderFacade::execute(__METHOD__, $metaDescription, func_get_args());
    }
}