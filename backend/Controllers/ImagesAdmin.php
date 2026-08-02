<?php

namespace Okay\Admin\Controllers;

use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Core\Response;

class ImagesAdmin extends IndexAdmin
{
    public function fetch(FrontTemplateConfig $frontTemplateConfig, Response $response)
    {
        $currentTheme = $frontTemplateConfig->getTheme();

        $images_dir = 'design/' . $currentTheme . '/images/';
        $allowed_extentions = array('png', 'gif', 'jpg', 'jpeg', 'ico');
        $images = [];

        /*Сохранение изображений*/
        if ($this->request->method('post') && !is_file($images_dir . '../locked')) {
            $old_names = $this->request->post('old_name');
            $new_names = $this->request->post('new_name');
            if (is_array($old_names)) {
                foreach ($old_names as $i => $old_name) {
                    $new_name = $new_names[$i];
                    $new_name = trim(pathinfo($new_name, PATHINFO_FILENAME) . '.' . pathinfo($old_name, PATHINFO_EXTENSION), '.');

                    if (is_writable($images_dir) && is_file($images_dir . $old_name) && !is_file($images_dir . $new_name)) {
                        rename($images_dir . $old_name, $images_dir . $new_name);
                    } elseif (is_file($images_dir . $new_name) && $new_name != $old_name) {
                        $message_error = 'name_exists';
                    }
                }
            }

            $delete_image = trim($this->request->post('delete_image'), '.');

            if (!empty($delete_image)) {
                // Валідація шляху: перевіряємо, що немає path traversal та файл знаходиться в дозволеній директорії
                $delete_image = basename($delete_image); // Видаляємо будь-які шляхи
                if (strpos($delete_image, '..') === false && strpos($delete_image, '/') === false && strpos($delete_image, '\\') === false) {
                    $full_path = $images_dir . $delete_image;
                    // Додаткова перевірка: файл повинен бути в межах $images_dir
                    $real_images_dir = realpath($images_dir);
                    $real_file_path = realpath($full_path);
                    if ($real_file_path !== false && $real_images_dir !== false && strpos($real_file_path, $real_images_dir) === 0) {
                        @unlink($full_path);
                    }
                }
            }

            // Загрузка изображений
            $uploadedImages = $this->request->files('upload_images');
            if (
                is_array($uploadedImages)
                && isset($uploadedImages['name'], $uploadedImages['tmp_name'])
                && is_array($uploadedImages['name'])
                && is_array($uploadedImages['tmp_name'])
            ) {
                foreach ($uploadedImages['name'] as $i => $uploadedName) {
                    if (!is_string($uploadedName) || !isset($uploadedImages['tmp_name'][$i]) || !is_string($uploadedImages['tmp_name'][$i])) {
                        continue;
                    }
                    $name = trim($uploadedName, '.');
                    if (in_array(strtolower(pathinfo($name, PATHINFO_EXTENSION)), $allowed_extentions)) {
                        move_uploaded_file($uploadedImages['tmp_name'][$i], $images_dir . $name);
                    }
                }
            }

            if (!isset($message_error)) {
                $response->redirectTo($_SERVER['REQUEST_URI']);
                exit();
            } else {
                $this->design->assign('message_error', $message_error);
            }
        }

        // Читаем все файлы
        if ($handle = opendir($images_dir)) {
            while (false !== ($file = readdir($handle))) {
                if (is_file($images_dir . $file) && $file[0] != '.' && in_array(pathinfo($file, PATHINFO_EXTENSION), $allowed_extentions)) {
                    $image = new \stdClass();
                    $image->name = $file;
                    $image->size = filesize($images_dir . $file);
                    $imageSize = @getimagesize($images_dir . $file);
                    if ($imageSize !== false) {
                        list($image->width, $image->height) = $imageSize;
                    } else {
                        $image->width = null;
                        $image->height = null;
                    }
                    $images[$file] = $image;
                }
            }
            closedir($handle);
            ksort($images);
        }

        // Если нет прав на запись - передаем в дизайн предупреждение
        if (!is_writable($images_dir)) {
            $this->design->assign('message_error', 'permissions');
        } elseif (is_file($images_dir . '../locked')) {
            $this->design->assign('message_error', 'theme_locked');
        }

        $this->design->assign('theme', $currentTheme);
        $this->design->assign('images', $images);
        $this->design->assign('images_dir', $images_dir);
        $this->response->setContent($this->design->fetch('images.tpl'));
    }
}
