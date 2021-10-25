<?php


namespace Okay\Helpers\MetadataHelpers;


interface MetadataInterface
{
    /** @return string */
    public function getH1(): string;

    /** @return string */
    public function getAnnotation(): string;

    /** @return string */
    public function getDescription(): string;

    /** @return string */
    public function getMetaTitle(): string;

    /** @return string */
    public function getMetaKeywords(): string;

    /** @return string */
    public function getMetaDescription(): string;
    
    /** @return string */
    public function getH1Template(): string;

    /** @return string */
    public function getAnnotationTemplate(): string;

    /** @return string */
    public function getDescriptionTemplate(): string;

    /** @return string */
    public function getMetaTitleTemplate(): string;

    /** @return string */
    public function getMetaKeywordsTemplate(): string;

    /** @return string */
    public function getMetaDescriptionTemplate(): string;
}