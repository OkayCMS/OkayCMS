<?php

namespace Okay\Core\Modules;

use Okay\Core\Config;
use Okay\Core\Database;
use Okay\Core\Modules\DTO\LicenseDTO;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Entities\ModulesEntity;

class LicenseModulesTemplates
{
    private QueryFactory $queryFactory;

    private Database $db;

    private Config $config;

    private LicenseStorage $licenseStorage;

    private string $themesDir;

    private ?LicenseDTO $licenseDTO = null;

    private bool $isInitialized = false;

    private string $themeName = '';

    private string $licenseEmail = '';

    // Кількість символів в кінці файлу, де ми маємо шукати ліцензію
    private const END_FILE_LENGTH = 100;

    private const REQUEST_TIMEOUT = 1200;
    private const MAX_RETRY = 5;

    public function __construct(
        QueryFactory $queryFactory,
        Database $database,
        Config $config,
        LicenseStorage $licenseStorage,
        string $rootDir
    ) {
        $this->queryFactory = $queryFactory;
        $this->db = $database;
        $this->config = $config;
        $this->licenseStorage = $licenseStorage;
        $this->themesDir = $rootDir . 'design' . DIRECTORY_SEPARATOR;
    }

    public function setThemeName(string $themeName)
    {
        $this->themeName = $themeName;
    }

    public function setLicenseEmail(string $licenseEmail)
    {
        $this->licenseEmail = $licenseEmail;
    }

    public function updateLicenseInfo(): ?LicenseDTO
    {
        $emailRequest = $this->emailRequest();
        $domainRequest = $this->domainRequest();

        $vendorNameRequest = $this->vendorNameRequest();
        $templateRequest = $this->templateRequest();

        $request = array_merge(
            $emailRequest,
            $domainRequest,
            $vendorNameRequest,
            $templateRequest
        );
        $url = $this->config->get('marketplace_url') . 'api/v2/modules/access/user';
        $this->ensureSession();
        $retryCnt = $_SESSION['request_timeout_try_cnt'] ?? 0;

        if (time() > ($_SESSION['request_timeout'] ?? 0) && ($response = $this->request($url, $request))) {
            $licenseDTO = new LicenseDTO();
            if (!empty($response->modules)) {
                $licenseDTO->setModulesLicenses($response->modules);
            }
            if (!empty($response->official_modules)) {
                $licenseDTO->setOfficialModules($response->official_modules);
            }
            if (!empty($response->template)) {
                $licenseDTO->setTemplateLicense($response->template);
            }
            if (!empty($response->official_template)) {
                $licenseDTO->setIsOfficialTemplate((bool)$response->official_template);
            }
            $this->licenseStorage->saveLicense($licenseDTO);
            $this->licenseDTO = $licenseDTO;
            $_SESSION['request_timeout_try_cnt'] = 0;

            return $licenseDTO;
        } elseif (($response ?? null) === false) {
            if ($retryCnt < self::MAX_RETRY) {
                $retryCnt++;
            }
            $_SESSION['request_timeout_try_cnt'] = $retryCnt;
            $_SESSION['request_timeout'] = time() + (self::REQUEST_TIMEOUT * $retryCnt);
        }

        return null;
    }

    public function clearRequestRetry()
    {
        $this->ensureSession();
        unset($_SESSION['request_timeout_try_cnt']);
        unset($_SESSION['request_timeout']);
    }
    private function ensureSession(): void
    {
        $_SESSION ??= [];
    }

    public function isLicensedModule(string $vendor, string $moduleName): bool
    {
        if ($this->licenseDTO && !is_null($this->licenseDTO->getModulesLicenses())) {
            $moduleHash = md5(sprintf(
                '%s/%s/%s',
                Request::getDomain(),
                $vendor,
                $moduleName
            ));

            return in_array($moduleHash, $this->licenseDTO->getModulesLicenses());
        } elseif ($this->isInitialized) {
            return true;
        }

        return false;
    }

    public function isOfficialModule(string $vendor, string $moduleName): bool
    {
        if ($this->licenseDTO) {
            $module = sprintf(
                '%s/%s',
                $vendor,
                $moduleName
            );

            return in_array($module, $this->licenseDTO->getOfficialModules());
        }

        return false;
    }

    public function isLicensedTemplate(): bool
    {
        if ($this->licenseDTO && !is_null($this->licenseDTO->getTemplateLicense())) {
            return $this->licenseDTO->getTemplateLicense() === md5(Request::getDomain());
        } elseif ($this->isInitialized) {
            return true;
        }

        return false;
    }

    public function isOfficialTemplate(): bool
    {
        if ($this->licenseDTO) {
            return $this->licenseDTO->isOfficialTemplate();
        } elseif ($this->isInitialized) {
            return true;
        }

        return false;
    }

    public function getTemplateErrorHtml(): string
    {
        return <<<HTML
            <div style="
                display: flex;
                align-items: center;
                justify-content: center;
                font-family:arial;
                color: #fff;
                text-align: center;
                font-size: 18px;
                line-height: 1;
                font-weight:400;
                transform: rotate(90deg);
                border-radius:6px 6px 0 0;
                position: fixed;
                z-index: 999999;
                top: 364px;
                left: -94px;
                width: 224px;
                height: 38px;
                white-space:nowrap;
                background: #f1416c;
                pointer-events: none;
                ">Template is not verified!</div>
        HTML;
    }

    public function initLicenseInfo()
    {
        if ($this->isInitialized === false && is_null($this->licenseDTO)) {
            $this->licenseDTO = $this->licenseStorage->getLicense();
            if (is_null($this->licenseDTO)) {
                $this->licenseDTO = $this->updateLicenseInfo();
            } else {
                $select = $this->queryFactory->newSelect();
                $modulesNum = $select->from(ModulesEntity::getTable())
                    ->cols(['count(*) AS count'])
                    ->result('count');
                $modulesLicenses = $this->licenseDTO->getModulesLicenses();
                if (!is_null($modulesLicenses) && $modulesNum != count($modulesLicenses)) {
                    $this->licenseDTO = $this->updateLicenseInfo();
                }
            }
            $this->isInitialized = true;
        }
    }

    /**
     * @return array<string, string>
     */
    private function emailRequest(): array
    {
        return ['email' => $this->licenseEmail];
    }

    /**
     * @return array<string, string>
     */
    private function domainRequest(): array
    {
        return ['domain' => Request::getDomain()];
    }

    /**
     * @return array<string, list<string>>
     */
    private function vendorNameRequest(): array
    {
        $select = $this->queryFactory->newSelect()
            ->from(ModulesEntity::getTable())
            ->cols(['id', 'vendor', 'module_name', 'enabled'])
            ->orderBy(['position ASC']);

        $this->db->query($select);
        $modulesDb = $this->db->results();
        $modules = [];
        foreach ($modulesDb as $module) {
            $modules[] = $module->vendor . '/' . $module->module_name;
        }

        return ['modules' => $modules];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function templateRequest(): array
    {
        $templateRequestModules = [];

        $imagePath = $this->themesDir . $this->themeName . DIRECTORY_SEPARATOR . 'preview.png';
        if (is_file($imagePath)) {
            $contentPng = file_get_contents($imagePath);
            if (is_string($contentPng)) {
                $templateRequestModules['preview.png'] = base64_encode($contentPng);
            }
        }

        $folderPath = $this->themesDir . $this->themeName . DIRECTORY_SEPARATOR . 'html';
        $templateFile = $this->getTplFiles($folderPath);

        foreach ($templateFile as $file) {
            if (is_file($folderPath . DIRECTORY_SEPARATOR . $file)) {
                $templateRequestModules[$file] = base64_encode(
                    $this->getLastCharacters($folderPath . DIRECTORY_SEPARATOR . $file)
                );
            }
        }

        return ['template' => $templateRequestModules];
    }

    /**
     * @return list<string>
     */
    private function getTplFiles($folderPath, $subFolder = ''): array
    {
        $tplFiles = [];

        if (!is_dir($folderPath)) {
            return $tplFiles;
        }
        // Получить список файлов и папок
        $files = scandir($folderPath);
        if ($files === false) {
            return $tplFiles;
        }

        // Перебор полученных файлов и папок
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;

                // Проверка файла .tpl
                if (is_file($filePath) && pathinfo($file, PATHINFO_EXTENSION) == 'tpl') {
                    //относительный путь с именем подпапки в качестве значения
                    $tplFiles[] = $subFolder . DIRECTORY_SEPARATOR . $file;
                }

                // Проверка папки
                if (is_dir($filePath)) {
                    //Рекурсивный вызов для вложенной папки с обновленным значением подпапки
                    $nestedTplFiles = $this->getTplFiles($filePath, $subFolder . DIRECTORY_SEPARATOR . $file);

                    //Добавление файлов из вложенной папки в общий список
                    $tplFiles = array_merge($tplFiles, $nestedTplFiles);
                }
            }
        }

        return $tplFiles;
    }

    private function getLastCharacters($filePath): string
    {
        // Отримання вмісту файлу
        $fileContent = file_get_contents($filePath);
        if (!is_string($fileContent)) {
            return '';
        }

        // Отримання останніх символів
        $fileContent = substr($fileContent, -self::END_FILE_LENGTH);

        // Видалення пробілів і символів нового рядка
        return str_replace([" ", "\n", "\r"], '', $fileContent);
    }

    /**
     * @param array<string, mixed> $request
     */
    private function request(string $url, array $request = [])
    {
        if ($url === '') {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

        if (!empty($request)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            $jsonRequest = json_encode($request);
            if (!is_string($jsonRequest)) {
                return false;
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonRequest);
        }

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return false;
        }

        if (!is_string($result)) {
            return false;
        }

        $result = json_decode($result);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (!property_exists($result, 'success') || !$result->success) {
                return false;
            }
            return $result;
        }
        return false;
    }
}
