<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Config;
use Okay\Core\Request;

class BackendModulesHelper
{

    private $apiBaseUrl;
    private $marketplaceUrl;
    private $config;
    
    public function __construct(Config $config)
    {
        $this->config = $config;
        
        $this->marketplaceUrl = $config->get('marketplace_url');
        $this->apiBaseUrl = $this->marketplaceUrl . 'api/v1/';
    }

    public function checkDownloadVersions($accessUrl)
    {
        $pathAccess = parse_url($accessUrl, PHP_URL_PATH);
        $pathAccess = trim($pathAccess, '/');
        return $this->request($this->marketplaceUrl . $pathAccess . '/versions');
    }

    /**
     * Download module zip from marketplace to Okay/Modules
     * 
     * @param string $downloadUrl
     * @return bool|string
     * @throws \Exception
     * 
     */
    public function downloadModule($downloadUrl)
    {
        if (!$tempFileToSave = tempnam($this->config->get('tmp_dir'), 'module_zip_')) {
            return false;
        }
        
        if (!$tempDir = $this->tempDir($this->config->get('tmp_dir'))) {
            return false;
        }
        
        if ($this->download($downloadUrl, $tempFileToSave)) {
            $zip = new \ZipArchive();

            if ($zip->open($tempFileToSave) === true) {
                $zip->extractTo($tempDir);
                $zip->close();
                unlink($tempFileToSave);
                
                return $tempDir;
            }
        }
        unlink($tempFileToSave);
        $this->rRmdir($tempDir);
        return false;
        
    }
    
    public function moveModule($moduleTmpDir, $moduleVendor, $moduleName)
    {
        if (empty($moduleTmpDir) || empty($moduleVendor) || empty($moduleName)) {
            return false;
        }

        $relatedModuleDir = 'Okay/Modules/' . $moduleVendor . '/' . $moduleName . '/';
        
        $result = false;
        if (!is_dir($relatedModuleDir) && is_dir($moduleTmpDir . '/' . $relatedModuleDir)) {
            $this->rCopy($moduleTmpDir . '/' . $relatedModuleDir, $relatedModuleDir);
            $result = true;
        }

        $this->rRmdir($moduleTmpDir);
        return $result;
    }
    
    public function findModules($keyword = '', $page = 1, $perPage = 20)
    {
        $query = [
            'type' => 'module',
            'limit' => $perPage,
        ];
        
        if (!empty($keyword)) {
            $query['query'] = $keyword;
        }
        
        if (!empty($page) && $page > 1) {
            $query['page'] = $page;
        }
        
        return $this->request($this->apiBaseUrl . 'modules/list?' . http_build_query($query));
    }
    
    public function request($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $result = json_decode(curl_exec($ch));

        curl_close($ch);
        return $result;
    }
    
    private function download($url, $saveTo)
    {
        $cookieFile = tempnam(sys_get_temp_dir(), "CURLCOOKIE");
        
        $fp = fopen($saveTo, 'w+');
        
        $url .= '?domain=' . urlencode(base64_encode(Request::getDomainWithProtocol()));
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FILE, $fp);

        curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookieFile);
        
        curl_exec($ch);

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        fclose($fp);
        
        if ($statusCode == 200) {
            return true;
        }
        return false;
    }
    
    private function rCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);
        
        while (false !== ( $object = readdir($dir)) ) {
            if ($object != "." && $object != "..") {
                if (is_dir($src . '/' . $object)) {
                    $this->rCopy($src . '/' . $object,$dst . '/' . $object);
                } else {
                    copy($src . '/' . $object,$dst . '/' . $object);
                }
            }
        }
        closedir($dir);
    }

    private function rRmdir($dir, $level = 0)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->rRmdir($dir . "/" . $object, $level + 1);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            if ($level > 0) {
                rmdir($dir);
            }
        }
        if ($level == 0) {
            rmdir($dir);
        }
    }
    
    private function tempDir($subDir = null)
    {
        if ($subDir === null) {
            $subDir = sys_get_temp_dir();
        }
        $tempFile = tempnam($subDir,'');
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
        mkdir($tempFile);
        if (is_dir($tempFile)) {
            return $tempFile;
        }
        
        return false;
    }
    
}