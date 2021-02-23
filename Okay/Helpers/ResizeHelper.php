<?php


namespace Okay\Helpers;


use Okay\Core\Config;
use Okay\Core\Image;
use Okay\Core\Modules\Extender\ExtenderFacade;

class ResizeHelper
{
    private $image;
    private $config;

    public function __construct(Image $image, Config $config)
    {
        $this->image = $image;
        $this->config = $config;
    }

    /**
     * Метод возвращает массив, следующего вида:
     * [
     *     0 => 'path/to/originals/images',
     *     1 => 'path/to/resized/images',
     * ]
     * 
     * @param string $object папка с нарезанными картинками
     * @return array|null
     * @throws \Exception
     */
    public function getResizeDirs($object)
    {
        $resizeDirs = [];
        
        if (!empty($object)) {
            
            if ($object == 'products') {
                $originalImgDir = $this->config->get('original_images_dir');
                $resizedImgDir = $this->config->get('resized_images_dir');
            }
            if ($object == 'blog') {
                $originalImgDir = $this->config->get('original_blog_dir');
                $resizedImgDir = $this->config->get('resized_blog_dir');
            }
            if ($object == 'blog_categories') {
                $originalImgDir = $this->config->get('original_blog_categories_dir');
                $resizedImgDir = $this->config->get('resized_blog_categories_dir');
            }
            if ($object == 'brands') {
                $originalImgDir = $this->config->get('original_brands_dir');
                $resizedImgDir = $this->config->get('resized_brands_dir');
            }
            if ($object == 'categories') {
                $originalImgDir = $this->config->get('original_categories_dir');
                $resizedImgDir = $this->config->get('resized_categories_dir');
            }
            if ($object == 'deliveries') {
                $originalImgDir = $this->config->get('original_deliveries_dir');
                $resizedImgDir = $this->config->get('resized_deliveries_dir');
            }
            if ($object == 'payments') {
                $originalImgDir = $this->config->get('original_payments_dir');
                $resizedImgDir = $this->config->get('resized_payments_dir');
            }
            if ($object == 'advantages') {
                $originalImgDir = $this->config->get('original_advantages_dir');
                $resizedImgDir = $this->config->get('resized_advantages_dir');
            }
            if ($object == 'lang') {
                $originalImgDir = $this->config->get('lang_images_dir');
                $resizedImgDir = $this->config->get('lang_resized_dir');
            }
            if ($object == 'authors') {
                $originalImgDir = $this->config->get('original_authors_dir');
                $resizedImgDir = $this->config->get('resized_authors_dir');
            }
            $extendsResizeObjects = $this->image->getResizeObjects();

            // Проверим, может кто расширил директории ресайзов из модуля
            if (isset($extendsResizeObjects[$object])) {
                $originalImgDir = $extendsResizeObjects[$object]['original_dir'];
                $resizedImgDir  = $extendsResizeObjects[$object]['resized_dir'];
            }
        }

        if (!empty($originalImgDir) && !empty($resizedImgDir)) {
            $resizeDirs = [
                $originalImgDir,
                $resizedImgDir
            ];
        }
        
        return ExtenderFacade::execute(__METHOD__, $resizeDirs, func_get_args());
    }
}