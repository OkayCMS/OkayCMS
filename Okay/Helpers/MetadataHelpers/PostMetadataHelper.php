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
 

    public function getH1Template()
    {
        $post = $this->design->getVar('post');

        if ($pageH1 = parent::getH1Template()) {
            $h1 = $pageH1;
        } else {
            $h1 = $post->name;
        }
        
        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }
    
    public function getDescriptionTemplate()
    {
        $post = $this->design->getVar('post');
        
        if ($pageDescription = parent::getDescriptionTemplate()) {
            $description = $pageDescription;
        } else {
            $description = $post->description;
        }

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }
    
    public function getMetaTitleTemplate()
    {
        $post = $this->design->getVar('post');
        if ($pageTitle = parent::getMetaTitleTemplate()) {
            $metaTitle = $pageTitle;
        } else {
            $metaTitle = $post->meta_title;
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }
    
    public function getMetaKeywordsTemplate()
    {
        $post = $this->design->getVar('post');
        
        if ($pageKeywords = parent::getMetaKeywordsTemplate()) {
            $metaKeywords = $pageKeywords;
        } else {
            $metaKeywords = $post->meta_keywords;
        }

        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }
    
    public function getMetaDescriptionTemplate()
    {
        $post = $this->design->getVar('post');
        
        if ($pageMetaDescription = parent::getMetaDescriptionTemplate()) {
            $metaDescription = $pageMetaDescription;
        } else {
            $metaDescription = $post->meta_description;
        }

        return ExtenderFacade::execute(__METHOD__, $metaDescription, func_get_args());
    }
}