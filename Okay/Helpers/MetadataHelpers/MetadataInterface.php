<?php


namespace Okay\Helpers\MetadataHelpers;


interface MetadataInterface
{
    /** @return string */
    public function setUp();
    
    /** @return string */
    public function getH1();

    /** @return string */
    public function getDescription();

    /** @return string */
    public function getMetaTitle();

    /** @return string */
    public function getMetaKeywords();

    /** @return string */
    public function getMetaDescription();
    
    /** @return string */
    public function getH1Template();

    /** @return string */
    public function getDescriptionTemplate();

    /** @return string */
    public function getMetaTitleTemplate();

    /** @return string */
    public function getMetaKeywordsTemplate();

    /** @return string */
    public function getMetaDescriptionTemplate();
}