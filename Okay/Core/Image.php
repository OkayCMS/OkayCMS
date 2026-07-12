<?php

namespace Okay\Core;

use Okay\Core\Adapters\Resize\AbstractResize;
use Okay\Core\Adapters\Resize\AdapterManager;
use Okay\Core\Adapters\Resize\WebpConverter;
use Okay\Core\Modules\Extender\ExtenderFacade;

class Image
{
    private $allowedExtensions = ['png', 'gif', 'jpg', 'jpeg', 'ico', 'svg', 'webp'];

    private $rootDir;

    /**
     * @var AdapterManager
     */
    private $adapterManager;

    private WebpConverter $webpConverter;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var array<string, array{original_dir: string, resized_dir: string}>
     */
    private array $resizeObjects = [];

    private $originalsDir;
    private $productionDomain;

    public function __construct(
        Settings $settings,
        Config $config,
        AdapterManager $adapterManager,
        WebpConverter $webpConverter,
        Request $request,
        Response $response,
        QueryFactory $queryFactory,
        Database $db,
        EntityFactory $entityFactory,
        $rootDir
    ) {
        $this->settings       = $settings;
        $this->config         = $config;
        $this->adapterManager = $adapterManager;
        $this->webpConverter  = $webpConverter;
        $this->request        = $request;
        $this->rootDir        = $rootDir;
        $this->response       = $response;
        $this->queryFactory   = $queryFactory;
        $this->db             = $db;
        $this->entityFactory  = $entityFactory;
    }

    /**
     * @param string $originalImgDirDirective название директивы конфига, которая содержит путь к директории оригиналов изображений
     * @param string $resizedImgDirDirective название директивы конфига, которая содержит путь к директории нарезок изображений
     * @throws \Exception
     */
    public function addResizeObject($originalImgDirDirective, $resizedImgDirDirective)
    {
        if (($originalImgDir = $this->config->get($originalImgDirDirective)) && ($resizedImgDir = $this->config->get($resizedImgDirDirective))) {
            $object = pathinfo($resizedImgDir, PATHINFO_BASENAME);
            $this->resizeObjects[$object] = [
                'original_dir' => $originalImgDir,
                'resized_dir' => $resizedImgDir,
            ];
        }
    }

    /**
     * Метод возвращает массив объектов ресайза. В виде ключа выступает название конечной директории ресайза,
     * значение это массив с ключами original_dir и resized_dir,
     *
     * @return array<string, array{original_dir: string, resized_dir: string}>
     */
    public function getResizeObjects()
    {
        return $this->resizeObjects;
    }

    /**
     * Создание превью изображения
     *
     * @param  string $filename файл с изображением (без пути к файлу)
     * @param  string $imageSizes Возможные варианты ресайза, разделённые прямой чертой |
     * @param  string $originalImagesDir путь к диретории оригиналов изображений
     * @param  string $resizedImagesDir путь к диретории нарезок изображений
     * @return string имя файла превью
     * @throws \Exception
     */
    public function resize($filename, $imageSizes, $originalImagesDir = null, $resizedImagesDir = null)
    {
        $resizeParams = $this->getResizeParams($filename);
        if ($resizeParams === false) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        list($sourceFile, $width , $height, $setWatermark, $cropParams, $pseudoWebp) = $resizeParams;
        if ($this->hasUnsafeImagePath($sourceFile)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $size = $width . 'x' . $height . ($setWatermark === true ? 'w' : '');

        if (!is_array($imageSizes)) {
            $imageSizes = explode('|', $imageSizes);
        }

        if (!in_array($size, $imageSizes)) {
            $this->response->setStatusCode(404)->sendHeaders();
            exit();
        }

        $originalsDir = $this->rootDir . $originalImagesDir;
        $previewDir   = $this->rootDir . $resizedImagesDir;
        $this->originalsDir = $originalImagesDir;

        // Если файл удаленный (https?://), зальем его себе
        if (preg_match("~^https?://~", $sourceFile)) {
            if ($this->isNotHttpsSource($sourceFile)) {
                return ExtenderFacade::execute(__METHOD__, false, func_get_args());
            }

            // Имя оригинального файла
            if (!$originalFile = $this->downloadImage($sourceFile)) {
                return ExtenderFacade::execute(__METHOD__, false, func_get_args());
            }
        } else {
            $originalFile = $sourceFile;
        }

        $resizedFile = $this->addResizeParams($originalFile, $width, $height, $setWatermark, $cropParams);
        if ($this->hasUnsafeImagePath($originalFile) || $this->hasUnsafeImagePath($resizedFile)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $resizedPath = $this->buildPathWithinDirectory($previewDir, $resizedFile, false);
        if ($resizedPath === false) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $originalPath = $this->buildPathWithinDirectory($originalsDir, $originalFile, true);
        if ($originalPath === false) {
            // Намагаємось завантажити зображення з production сайта
            if (!empty($this->productionDomain)) {
                $ch = curl_init($this->productionDomain . $resizedImagesDir . $resizedFile);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $file = curl_exec($ch);
                $info = curl_getinfo($ch);
                $fileDestination = $resizedPath;
                if ($pseudoWebp) {
                    $fileDestination .= '.webp';
                }

                if ($info['http_code'] === 200 && $info['size_download'] > 0 && is_string($file)) {
                    $fp = fopen($fileDestination, 'w+');
                    if ($fp === false) {
                        return ExtenderFacade::execute(__METHOD__, false, func_get_args());
                    }

                    fwrite($fp, $file);
                    fclose($fp);
                    return ExtenderFacade::execute(__METHOD__, $fileDestination, func_get_args());
                }
            }

            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        if (strtolower(pathinfo($originalFile, PATHINFO_EXTENSION)) == 'svg') {
            copy($originalPath, $resizedPath);
            return ExtenderFacade::execute(__METHOD__, $resizedPath, func_get_args());
        }

        // Если в настройках выключена поддержка webp, но просят такое изображение - кадием 404
        if (!$this->settings->get('support_webp') && $pseudoWebp) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        /** @var AbstractResize $adapter */
        $adapter = $this->adapterManager->getAdapter();

        if (
            !$adapter->resize(
                $originalPath,
                $resizedPath,
                (int) $width,
                (int) $height,
                $setWatermark,
                $cropParams
            )
        ) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $destination = $resizedPath;

        // Если запросили псевдо webp, создаем еще дубль такого изображения в формате webp
        if ($pseudoWebp) {
            $source = $destination;
            $destination = $source . '.webp';
            if (!$this->webpConverter->convert($source, $destination)) {
                return ExtenderFacade::execute(__METHOD__, false, func_get_args());
            }
        }

        return ExtenderFacade::execute(__METHOD__, $destination, func_get_args());
    }

    /**
     * Метод формирует строку, по которой можно будет нарезать изображение
     *
     * @param $filename
     * @param int $width
     * @param int $height
     * @param bool $setWatermark
     * @param null $resizedDir
     * @param null $cropPositionX
     * @param null $cropPositionY
     * @return mixed|void|null
     * @throws \Exception
     */
    public function getResizeModifier(
        $filename,
        $width = 0,
        $height = 0,
        $setWatermark = false,
        $resizedDir = null,
        $cropPositionX = null,
        $cropPositionY = null
    ) {
        $cropParams = [];
        if (!empty($cropPositionX) && !empty($cropPositionY)) {
            $cropParams['x_pos'] = $cropPositionX;
            $cropParams['y_pos'] = $cropPositionY;
        }

        $resizedFilename = $this->addResizeParams($filename, $width, $height, $setWatermark, $cropParams);
        $resizedFilenameEncoded = $resizedFilename;

        $size = $width . 'x' . $height . ($setWatermark ? 'w' : '');

        if ($resizedDir === null || $resizedDir == $this->config->get('resized_images_dir')) {
            $this->addImagesSize($size, 'product');
        } else {
            $this->addImagesSize($size, 'other');
        }

        if (preg_match("~^https?://~", $resizedFilenameEncoded)) {
            $resizedFilenameEncoded = rawurlencode($resizedFilenameEncoded);
        }

        $resizedFilenameEncoded = rawurlencode($resizedFilenameEncoded);

        if ($resizedDir === null) {
            $resizedDir = $this->config->get('resized_images_dir');
        }

        $result = $this->request->getRootUrl() . '/' . $resizedDir . $resizedFilenameEncoded;
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    public function addImagesSize($size, $type) // todo сделать protected
    {
        if ($type == 'product') {
            $image_sizes = explode('|', $this->settings->get('products_image_sizes'));
            if (empty($image_sizes[0])) {
                $image_sizes = [];
            }
            if (!in_array($size, $image_sizes)) {
                if (empty($image_sizes[0])) {
                    $image_sizes = [];
                }
                $image_sizes[] = $size;
                $this->settings->set('products_image_sizes', implode('|', $image_sizes));
            }
        } else {
            $image_sizes = explode('|', $this->settings->get('image_sizes'));
            if (empty($image_sizes[0])) {
                $image_sizes = [];
            }
            if (!in_array($size, $image_sizes)) {
                $image_sizes[] = $size;
                $this->settings->set('image_sizes', implode('|', $image_sizes));
            }
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Добавление параетров нарезки картинок по форматам ширины и высоты
     *
     * @param string  $filename
     * @param int     $width
     * @param int     $height
     * @param boolean $setWatermark
     * @param array<string, mixed> $cropParams
     * @return string
     */
    public function addResizeParams($filename, $width = 0, $height = 0, $setWatermark = false, $cropParams = []) // todo сделать protected
    {
        if ('.' != ($dirname = pathinfo($filename, PATHINFO_DIRNAME))) {
            $file = $dirname . '/' . pathinfo($filename, PATHINFO_FILENAME);
        } else {
            $file = pathinfo($filename, PATHINFO_FILENAME);
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($width > 0 || $height > 0) {
            $resizedFilename = $file . '.' . ($width > 0 ? $width : '') . 'x' . ($height > 0 ? $height : '') . ($setWatermark ? 'w' : '');
        } else {
            $resizedFilename = $file . ($setWatermark ? '.w' : '') . $ext;
        }

        if (!empty($cropParams['x_pos']) && !empty($cropParams['y_pos'])) {
            $resizedFilename .= '.' . $cropParams['x_pos'] . '.' . $cropParams['y_pos'];
        }

        $result = $resizedFilename . '.' . $ext;
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    /**
     * Метод для скачивания изображенйи из удаленных ресурсов по ссылке
     *
     * @param string $filename
     * @return string|boolean
     * @throws \Exception
     */
    public function downloadImage($filename) // todo сделать protected
    {
        if (!$this->isRemoteImageSource($filename) || $this->isNotHttpsSource($filename) || $this->hasUnsafeImagePath($filename)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $encodedFilename = rawurlencode($filename);
        if (isset($_SESSION['resize_files'][$encodedFilename])) {
            if ($this->filenameAlreadyUses($this->getOriginalFilenameByResizeName($filename))) {
                return ExtenderFacade::execute(__METHOD__, $_SESSION['resize_files'][$encodedFilename], func_get_args());
            } else {
                unset($_SESSION['resize_files'][$encodedFilename]);
            }
        }

        if ($this->fileIsNotExists($filename)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $uploadedFile = $this->getOriginalFilenameByResizeName($filename);
        if ($this->hasUnsafeImagePath($uploadedFile)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        if ($this->filenameAlreadyUses($uploadedFile)) {
            $newName = $this->comeUpUniqueFilename($uploadedFile);
        } else {
            $newName = urldecode($uploadedFile);
        }

        $localFile = $this->buildPathWithinDirectory(
            $this->rootDir . $this->config->get('original_images_dir'),
            $newName,
            false
        );
        if ($localFile === false) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        if (!touch($localFile)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $ch = curl_init($filename);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $file = curl_exec($ch);
        $info = curl_getinfo($ch);

        if ($info['http_code'] === 200 && $info['size_download'] > 0 && is_string($file)) {
            $fp = fopen($localFile, 'w+');
            if ($fp === false) {
                @unlink($localFile);
                return ExtenderFacade::execute(__METHOD__, false, func_get_args());
            }

            fwrite($fp, $file);
            fclose($fp);

            $encodedFilename = rawurlencode($filename);
            $update = $this->queryFactory->newUpdate();
            $update->table('__images')
                ->cols(['filename' => $newName])
                ->where('filename=:encoded_filename')
                ->orWhere('filename=:filename_original')
                ->bindValues([
                    'encoded_filename' => $encodedFilename,
                    'filename_original' => $filename,
                ]);
            $this->db->query($update);
            $_SESSION['resize_files'][$encodedFilename] = $newName;
            return ExtenderFacade::execute(__METHOD__, $newName, func_get_args());
        }

        @unlink($localFile);
        return ExtenderFacade::execute(__METHOD__, false, func_get_args());
    }

    /*Загрузка изображения*/
    public function uploadImage($filename, $name, $originalDir = null)
    {
        // Имя оригинального файла
        $name = preg_replace('~(.+)\.([0-9]*)x([0-9]*)(w)?\.([^.?]+)$~', '${1}.${5}', $name);
        $name = $this->correctFilename($name);
        $uploadedFile = $newName = pathinfo($name, PATHINFO_BASENAME);
        $base = pathinfo($uploadedFile, PATHINFO_FILENAME);
        $ext = pathinfo($uploadedFile, PATHINFO_EXTENSION);

        if (!$originalDir) {
            $originalDir = $this->config->get('original_images_dir');
        }

        if (!in_array(strtolower($ext), $this->allowedExtensions)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        while (file_exists($this->rootDir . $originalDir . $newName)) {
            $new_base = pathinfo($newName, PATHINFO_FILENAME);
            if (preg_match('/_([0-9]+)$/', $new_base, $parts)) {
                $newName = $base . '_' . ($parts[1] + 1) . '.' . $ext;
            } else {
                $newName = $base . '_1.' . $ext;
            }
        }
        if (move_uploaded_file($filename, $this->rootDir . $originalDir . $newName)) {
            return ExtenderFacade::execute(__METHOD__, $newName, func_get_args());
        }

        return ExtenderFacade::execute(__METHOD__, false, func_get_args());
    }

    /**
     * @param string $domain
     * @return void
     *
     * Вказуємо production домен, щоб тягти зображення з нього
     */
    public function setProductionDomain(string $domain): void
    {
        if (!empty($domain)) {
            $domain = rtrim($domain, '/') . '/';
        }
        $this->productionDomain = $domain;
    }

    /*Выборка параметров изображения для ресайза*/
    private function getResizeParams($filename)
    {
        // Определаяем параметры ресайза
        if (!preg_match('/(.+)\.([0-9]*)x([0-9]*)(w)?(\.(left|center|right)\.(top|center|bottom))?\.([^.]+)(\.webp)?$/', $filename, $matches)) {
            return false;
        }

        $file = $matches[1];                 // имя запрашиваемого файла
        $width = $matches[2];                // ширина будущего изображения
        $height = $matches[3];               // высота будущего изображения
        $set_watermark = $matches[4] == 'w'; // ставить ли водяной знак
        $ext = $matches[8];                  // расширение файла
        $pseudoWebp = !empty($matches[9]);   // признак что запрашивается webp, но оригинал в jpeg или png
        // crop params
        $crop_params = [];
        if (!empty($matches[5])) {
            $crop_params['x_pos'] = $matches[6];
            $crop_params['y_pos'] = $matches[7];
        }

        return array($file . '.' . $ext, $width, $height, $set_watermark, $crop_params, $pseudoWebp);
    }

    /*Транслит названия изображения*/
    public function correctFilename($filename)
    {
        if (!is_scalar($filename)) {
            $filename = '';
        }

        $filename = (string) $filename;
        $ru = explode('-', "А-а-Б-б-В-в-Ґ-ґ-Г-г-Д-д-Е-е-Ё-ё-Є-є-Ж-ж-З-з-И-и-І-і-Ї-ї-Й-й-К-к-Л-л-М-м-Н-н-О-о-П-п-Р-р-С-с-Т-т-У-у-Ф-ф-Х-х-Ц-ц-Ч-ч-Ш-ш-Щ-щ-Ъ-ъ-Ы-ы-Ь-ь-Э-э-Ю-ю-Я-я");
        $en = explode('-', "A-a-B-b-V-v-G-g-G-g-D-d-E-e-E-e-E-e-ZH-zh-Z-z-I-i-I-i-I-i-J-j-K-k-L-l-M-m-N-n-O-o-P-p-R-r-S-s-T-t-U-u-F-f-H-h-TS-ts-CH-ch-SH-sh-SCH-sch---Y-y---E-e-YU-yu-YA-ya");

        $res = str_replace($ru, $en, $filename);
        $res = preg_replace("/[\s]+/ui", '-', $res);
        $res = is_string($res) ? $res : '';
        $res = preg_replace("/[^a-zA-Z0-9.\-_]+/ui", '', $res);
        $res = is_string($res) ? $res : '';
        $res = strtolower($res);
        return ExtenderFacade::execute(__METHOD__, $res, func_get_args());
    }

    /**
     * Удаления изображения и его ресайзов
     * @param $entityId - id сущьности, чьё изображение будем удалять
     * @param $field - поле в таблице
     * @param $entityName - название сущности
     * @param $originalDir
     * @param null $resizedDir
     * @param int $langId
     * @param string $langField поле в таблице ok_lang по которому происходит join
     * @return bool
     * @throws \Exception
     */
    public function deleteImage($entityId, $field, $entityName, $originalDir, $resizedDir = null, $langId = 0, $langField = '')
    {
        if (empty($field) || empty($entityName) || empty($originalDir)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $entity = $this->entityFactory->get($entityName);

        if (!$langId) {
            $select = $this->queryFactory->newSelect();
            $select->from($entity::getTable())
                ->cols([$field])
                ->where('id=:id')
                ->bindValue('id', $entityId);

            $this->db->query($select);
            $filename = $this->db->result($field);

            if (is_string($filename) && $filename !== '') {
                $update = $this->queryFactory->newUpdate();
                $update->table($entity::getTable())
                    ->cols([$field => ''])
                    ->where('id=:id')
                    ->bindValue('id', $entityId);
                $this->db->query($update);

                $select = $this->queryFactory->newSelect();
                $select->from($entity::getTable())
                    ->cols(['count(*) as count'])
                    ->where("$field=:filename")
                    ->bindValue('filename', $filename)
                    ->limit(1);
                $this->db->query($select);
                $count = $this->db->result('count');

                if ($count == 0) {
                    $file = pathinfo($filename, PATHINFO_FILENAME);
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);

                    // Удалить все ресайзы
                    if (!empty($resizedDir)) {
                        $resizedImages = glob($this->rootDir . $resizedDir . $file . '.*x*.' . $ext);
                        if (is_array($resizedImages)) {
                            foreach ($resizedImages as $f) {
                                @unlink($f);
                            }
                        }
                        $resizedImagesWebp = glob($this->rootDir . $resizedDir . $file . '.*x*.' . $ext . '.webp');
                        if (is_array($resizedImagesWebp)) {
                            foreach ($resizedImagesWebp as $f) {
                                @unlink($f);
                            }
                        }
                    }

                    @unlink($this->rootDir . $originalDir . $filename);
                }
            }
        } else {
            $select = $this->queryFactory->newSelect();
            $select->from($entity::getLangTable())
                ->cols([$field])
                ->where("$langField=:lang_field")
                ->where("lang_id=:lang_id")
                ->bindValues([
                    'lang_field' => $entityId,
                    'lang_id' => $langId,
                ]);
            $this->db->query($select);
            $filename = $this->db->result($field);

            if (is_string($filename) && $filename !== '') {
                $update = $this->queryFactory->newUpdate();
                $update->table($entity::getLangTable())
                    ->cols([$field => ''])
                    ->where("$langField=:lang_field")
                    ->where("lang_id=:lang_id")
                    ->bindValues([
                        'lang_field' => $entityId,
                        'lang_id' => $langId,
                    ]);
                $this->db->query($update);

                $select = $this->queryFactory->newSelect();
                $select->from($entity::getLangTable())
                    ->cols(['count(*) as count'])
                    ->where("$field=:filename")
                    ->bindValue('filename', $filename)
                    ->limit(1);
                $this->db->query($select);

                $count = $this->db->result('count');
                if ($count == 0) {
                    $file = pathinfo($filename, PATHINFO_FILENAME);
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);

                    // Удалить все ресайзы
                    if (!empty($resizedDir)) {
                        $resizedImages = glob($this->rootDir . $resizedDir . $file . '.*x*.' . $ext);
                        if (is_array($resizedImages)) {
                            foreach ($resizedImages as $f) {
                                @unlink($f);
                            }
                        }
                    }
                    $resizedImagesWebp = glob($this->rootDir . $resizedDir . $file . '.*x*.' . $ext . '.webp');
                    if (is_array($resizedImagesWebp)) {
                        foreach ($resizedImagesWebp as $f) {
                            @unlink($f);
                        }
                    }

                    @unlink($this->rootDir . $originalDir . $filename);
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    private function fileIsNotExists($filename)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['id'])
            ->from('__images')
            ->where('filename=:filename')
            ->orWhere('filename=:filename_encoded')
            ->limit(1)
            ->bindValue('filename', $filename)
            ->bindValue('filename_encoded', rawurlencode($filename));
        $this->db->query($select);

        if (!$this->db->result()) {
            return true;
        }

        return false;
    }

    private function getOriginalFilenameByResizeName($filename)
    {
        $basename = preg_replace('~(.+)\.([0-9]*)x([0-9]*)(w)?\.([^.?]+)(\?.*)?$~', '${1}.${5}', $filename);
        $uploadedFile = pathinfo($basename, PATHINFO_BASENAME);
        return $this->correctFilename($uploadedFile);
    }

    private function comeUpUniqueFilename($filename)
    {
        $base = urldecode(pathinfo($filename, PATHINFO_FILENAME));
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $newName = urldecode($filename);
        while (file_exists($this->rootDir . $this->originalsDir . $newName)) {
            $new_base = pathinfo($newName, PATHINFO_FILENAME);

            if (preg_match('/_([0-9]+)$/', $new_base, $parts)) {
                $newName = $base . '_' . ($parts[1] + 1) . '.' . $ext;
                continue;
            }

            $newName = $base . '_1.' . $ext;
        }

        return $newName;
    }

    private function filenameAlreadyUses($filename)
    {
        $file = $this->buildPathWithinDirectory($this->rootDir . $this->originalsDir, urldecode($filename), true);

        return $file !== false && file_exists($file);
    }

    private function buildPathWithinDirectory(string $baseDir, string $relativePath, bool $mustExist): string|false
    {
        if ($this->hasUnsafeImagePath($relativePath)) {
            return false;
        }

        $basePath = realpath($baseDir);
        if ($basePath === false || !is_dir($basePath)) {
            return false;
        }

        $basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $targetPath = $basePath . ltrim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);

        if ($mustExist) {
            $resolvedTarget = realpath($targetPath);
            if ($resolvedTarget === false || !str_starts_with($resolvedTarget, $basePath)) {
                return false;
            }

            return $resolvedTarget;
        }

        $resolvedParent = realpath(dirname($targetPath));
        if ($resolvedParent === false) {
            return false;
        }

        $resolvedParent = rtrim($resolvedParent, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!str_starts_with($resolvedParent, $basePath)) {
            return false;
        }

        return $targetPath;
    }

    private function hasUnsafeImagePath(string $path): bool
    {
        if (str_contains($path, "\0")) {
            return true;
        }

        $decodedPath = rawurldecode($path);
        if (str_contains($decodedPath, "\0")) {
            return true;
        }

        if ($this->isRemoteImageSource($decodedPath)) {
            $urlPath = parse_url($decodedPath, PHP_URL_PATH);
            if (!is_string($urlPath)) {
                return false;
            }

            return $this->hasParentDirectorySegment($urlPath);
        }

        if (preg_match('~^(?:/|\\\\|[a-zA-Z]:[\\\\/])~', $decodedPath)) {
            return true;
        }

        return $this->hasParentDirectorySegment($decodedPath);
    }

    private function hasParentDirectorySegment(string $path): bool
    {
        foreach (explode('/', str_replace('\\', '/', $path)) as $segment) {
            if ($segment === '..') {
                return true;
            }
        }

        return false;
    }

    private function isRemoteImageSource(string $filename): bool
    {
        return preg_match("~^https?://~i", $filename) === 1;
    }

    private function responseSuccess($responseHeaders)
    {
        if (empty($responseHeaders[0])) {
            return false;
        }

        preg_match('/\d{3}/', $responseHeaders[0], $matches);
        if (isset($matches[0]) && $matches[0] == '200') {
            return true;
        }

        return false;
    }

    private function isNotHttpsSource($filename)
    {
        if (!preg_match("~^https://~i", $filename)) {
            return true;
        }

        return false;
    }

    public function convertFilenameToWebp($filename)
    {
        if (pathinfo($filename, PATHINFO_EXTENSION) !== 'webp') {
            $filename = $filename . '.webp';
        }

        return $filename;
    }
}
