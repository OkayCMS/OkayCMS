<?php


namespace Okay\Controllers;


use Okay\Core\Image;
use Okay\Helpers\ResizeHelper;

class ResizeController extends AbstractController
{
    
    public function resize(Image $image, ResizeHelper $resizeHelper, $object, $filename)
    {

        $filename = rawurldecode($filename);
        
        $originalImgDir = null;
        $resizedImgDir = null;
        $imageSizes = null;
        
        $resizeDirs = $resizeHelper->getResizeDirs($object);
        if (!empty($resizeDirs)) {
            list($originalImgDir, $resizedImgDir) = $resizeDirs;
        }
        
        if ($object == 'products') {
            $imageSizes = $this->settings->get('products_image_sizes');
        } else {
            $imageSizes = $this->settings->get('image_sizes');
        }
        
        if (empty($originalImgDir) && empty($resizedImgDir) && $object != 'products') {
            return false;
        }

        if (($resizedFilename = $image->resize($filename, $imageSizes, $originalImgDir, $resizedImgDir)) === false) {
            return false;
        }
        
        if (is_readable($resizedFilename)) {
            
            $responseType = RESPONSE_IMAGE;
            switch (strtolower(pathinfo($resizedFilename, PATHINFO_EXTENSION))) {
                case 'png':
                    $responseType = RESPONSE_IMAGE_PNG;
                    break;
                case 'jpg':// No break
                case 'jpeg':
                    $responseType = RESPONSE_IMAGE_JPG;
                    break;
                case 'gif':
                    $responseType = RESPONSE_IMAGE_GIF;
                    break;
                case 'svg':
                    $responseType = RESPONSE_IMAGE_SVG;
                    break;
                case 'webp':
                    $responseType = RESPONSE_IMAGE_WEBP;
                    break;
            }
            
            $this->response->setContent(file_get_contents($resizedFilename), $responseType);
        }
    }
    
}
