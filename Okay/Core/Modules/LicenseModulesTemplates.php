<?php


namespace Okay\Core\Modules;

use Okay\Core\Config;
use Okay\Core\Database;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Entities\ModulesEntity;


class LicenseModulesTemplates
{

    /**
     * @var Settings
     */
    private $settings;
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var Config
     */
    private $config;

    private string $themes_dir = 'design'.DIRECTORY_SEPARATOR;

    public function __construct(Settings $settings,
                                QueryFactory  $queryFactory,
                                Database      $database,
                                Config $config,
                                FrontTemplateConfig $frontTemplateConfig
                                )
    {
        $this->settings = $settings;
        $this->queryFactory = $queryFactory;
        $this->db = $database;
        $this->config = $config;
        $this->frontTemplateConfig = $frontTemplateConfig;
    }


    public function emailRequest()
    {
        return ['email' => $this->settings->get('email_for_module')];
    }

    public function domainRequest()
    {
        return ['domain' => Request::getDomain()];
    }

    public function signRequest()
    {
       // $sign = ['sign' => $this->sign];
        $this->sign = 1;
        return ['sign' => $this->sign];
    }

    public function vendorNameRequest()
    {
        $select = $this->queryFactory->newSelect()
            ->from(ModulesEntity::getTable())
            ->cols(['id', 'vendor', 'module_name', 'enabled'])
            ->orderBy(['position ASC']);

        $this->db->query($select);
        $modulesDb = $this->db->results();

        foreach ($modulesDb as $module) {

            $moduleCodeConfig  = $module->vendor."/".$module->module_name;
            $modules[] = $moduleCodeConfig;

        }

        return ['modules' => $modules];
    }

    public function templateRequest()
    {
        $rootDir = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR;
        $theme = $this->settings->get('theme');
        $theme = 'barbie';
       // D:\OpenServer\OSPanel\domains\okay440l\design\barbie\html
        $imagePath = $rootDir.$this->themes_dir.$theme.DIRECTORY_SEPARATOR.'preview.png';
        $contentPng = base64_encode(file_get_contents($imagePath));
        $templateRequestModules['preview.png'] = $contentPng;
        //  $folderPath = $rootDir.$this->themes_dir.DIRECTORY_SEPARATOR.$this->settings->get('theme').DIRECTORY_SEPARATOR.'html';
        $folderPath = $rootDir.$this->themes_dir/*.DIRECTORY_SEPARATOR*/.$theme.DIRECTORY_SEPARATOR.'html';
        $templateFile = $this->getTplFiles($folderPath);

        foreach ($templateFile as $file) {
            if (is_file($folderPath.DIRECTORY_SEPARATOR.$file)) {
                $templateRequestModules[$file] = base64_encode($this->getLastCharacters($folderPath.DIRECTORY_SEPARATOR.$file));
            }
        }
       // $tamplateRequestMpodules =  ['tamplate' => $tamplateRequestMpodules];

        return ['template' => $templateRequestModules];
    }

    public function getTplFiles($folderPath, $subfolder = '')
    {
        $tplFiles = [];

        // Получить список файлов и папок
        $files = scandir($folderPath);

        // Перебор полученных файлов и папок
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;

                // Проверка файла .tpl
                if (is_file($filePath) && pathinfo($file, PATHINFO_EXTENSION) == 'tpl') {
                    //относительный путь с именем подпапки в качестве значения
                    $tplFiles[] = $subfolder.DIRECTORY_SEPARATOR.$file;
                }

                // Проверка папки
                if (is_dir($filePath)) {
                    //Рекурсивный вызов для вложенной папки с обновленным значением подпапки
                    $nestedTplFiles = $this->getTplFiles($filePath, $subfolder.DIRECTORY_SEPARATOR.$file);

                    //Добавление файлов из вложенной папки в общий список
                    $tplFiles = array_merge($tplFiles, $nestedTplFiles);
                }
            }
        }

        return $tplFiles;
    }


    public function getLastCharacters($filePath)
    {
        // Отримання вмісту файлу
        $fileContent = file_get_contents($filePath);

        // Видалення пробілів і символів нового рядка
        $fileContentWithoutSpaces = str_replace([" ", "\n", "\r"], '', $fileContent);

        // Отримання останніх символів
        $lastCharactersWithoutSpaces = substr($fileContentWithoutSpaces, -100);

        // Повернення результату
        return $lastCharactersWithoutSpaces;

    }

    public function buildFullRequest()
    {
        $emailRequest = $this->emailRequest();
        $domainRequest = $this->domainRequest();

        ///!!!
        //$signRequest = $this->signRequest();
        $vendorNameRequest = $this->vendorNameRequest();
        $templateRequest = $this->templateRequest();

        $request = array_merge($emailRequest,$domainRequest/*,$signRequest*/,$vendorNameRequest,$templateRequest);
        $url = $this->config->get('marketplace_url')."api/v2/modules/access/user";

        return $this->request($url,$request);
    }

    private function request(string $url, array $request = [])
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

        if (!empty($request)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
        }

       // !!!
//        // Додаємо авторизацію по токену
//        curl_setopt($ch, CURLOPT_HTTPHEADER, [
//            'Token: ' . $this->config->get('modules_api_auth_token'),
//        ]);

        $result = curl_exec($ch);

        curl_close ($ch);
        ///!!!

       $this->embedLicenseToFile($result);

        return json_decode($result);
    }

    private function embedLicenseToFile($result){

        $compileCodeDir = $this->frontTemplateConfig->getCompileCodeDir();

        $domain = Request::getDomain();

        $fullFilePath = $compileCodeDir.md5($domain)."codes.php";

        file_put_contents($fullFilePath, '');

        file_put_contents($fullFilePath, $result, LOCK_EX);
    }

    public function initCodes(){

        $select = $this->queryFactory->newSelect()
            ->from(ModulesEntity::getTable())
            ->cols(['id', 'vendor', 'module_name', 'enabled'])
            ->orderBy(['position ASC']);

        $this->db->query($select);
        $modulesDb = $this->db->results();
        foreach ($modulesDb as $module) {

            $moduleCodeConfig  = $module->vendor."/".$module->module_name;
            $modules[] = $moduleCodeConfig;

        }

        $domain = Request::getDomain();
        $compileCodeDir = $this->frontTemplateConfig->getCompileCodeDir();

        $filename = $compileCodeDir.md5($domain)."codes.php";

        if (file_exists($filename)) {

            $fileContents = file_get_contents($filename);

            if (empty($fileContents)) {
                $this->buildFullRequest();
            } else {
                if (strpos($fileContents, "\n") !== false) {
                    $this->buildFullRequest();
                }else{
                    $fileContents = json_decode($fileContents,true);

                    if (count($fileContents['modules']) != count($modules)){
                        $this->buildFullRequest();
                    }
                }
            }
        } else {
            $this->buildFullRequest();
        }
    }

}