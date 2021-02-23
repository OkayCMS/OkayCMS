<?php


namespace Okay\Modules\OkayCMS\Integration1C\Controllers;


use Okay\Controllers\AbstractController;
use Okay\Modules\OkayCMS\Integration1C\Integration\Export\ExportFactory\ExportFactory;
use Okay\Modules\OkayCMS\Integration1C\Integration\Import\AbstractImport;
use Okay\Modules\OkayCMS\Integration1C\Integration\Import\ImportFactory\ImportFactory;
use Okay\Modules\OkayCMS\Integration1C\Integration\Integration1C;

class Integration1cController extends AbstractController
{

    public function runIntegration(
        Integration1C $integration1C,
        ImportFactory $importFactory,
        ExportFactory $exportFactory
    ) {
        
        $this->response->setContentType(RESPONSE_TEXT);
        
        // Аутентификация (лигинимся под менеджером из админки)
        if ($integration1C->checkAuth() === false) {
            $this->response->addHeader("WWW-Authenticate: Basic realm=\"1C integration for OkayCMS {$this->config->version} {$this->config->version_type}\"");
            $this->response->setStatusCode(401);
            $this->response->sendHeaders();
            return;
        }
        
        if ($this->request->get('mode') == 'checkauth') {
            $this->response->setContent("success\n");
            $this->response->setContent(session_name()."\n");
            $this->response->setContent(session_id()."\n");
        }

        // Инициализация обмена
        if ($this->request->get('mode') == 'init') {

            $integration1C->rrmdir($integration1C->getTmpDir());

            // Очищаем все временнные данные
            $integration1C->clearStorage();

            // Если нужно, очищаем базу
            if ($integration1C->deleteAll === true) {
                $integration1C->flushDatabase();
            }

            $this->response->setContent("zip=no\n");
            $this->response->setContent("file_limit=1000000\n");
        }

        if ($this->request->get('mode') == 'file' && in_array($this->request->get('type'), array('catalog', 'sale'))) {

            $filename = $this->request->get('filename');
            $xmlFileName = $integration1C->getFullPath($filename);

            // Загружаем файл
            $integration1C->uploadFile($xmlFileName);

            // Если файл не валидный, прекращаем всё
            if ($integration1C->validateFile($xmlFileName) === false) {
                $this->response->setContent("error import file\n");
                $this->response->sendContent();
                return;
            }

            // Здесь "success" отвечаем только когда импортируется каталог, в случае с заказами, ответ отдаст клас импорта заказов
            if ($this->request->get('type') == 'catalog') {
                $this->response->setContent("success\n");
            }
        }

        if ($this->request->get('type') == 'sale') {

            if ($this->request->get('mode') == 'success') {
                $this->settings->last_1c_orders_export_date = date("Y-m-d H:i:s");
                $this->response->setContent("success\n");
                $this->response->sendContent();
                return;
            } elseif ($this->request->get('mode') == 'query') {
                
                $export = $exportFactory->create('orders');

                if ($xml = $export->export()) {
                    $this->settings->last_1c_orders_export_date = date("Y-m-d H:i:s");
                    $this->response->setContent("\xEF\xBB\xBF", RESPONSE_XML); // Добавим BOM
                    $this->response->setContent($xml, RESPONSE_XML);
                    $this->response->addHeader("Content-type: text/xml; charset=utf-8");
                }
            }

            if ($this->request->get('mode') == 'file') {
                $import = $importFactory->create('orders');
                $this->settings->last_1c_orders_export_date = date("Y-m-d H:i:s");
            }

            if ($this->request->get('mode') == 'success') {
                $this->settings->last_1c_orders_export_date = date("Y-m-d H:i:s");
            }

        } elseif ($this->request->get('type') == 'catalog') {

            if ($this->request->get('mode') == 'import') {
                $filename = $this->request->get('filename');
                // Определяем какую фабрику импорта создать, импорта товаров или предложений
                if (preg_match('~^.*import.*\.xml$~', $filename)) {
                    $import = $importFactory->create('products');
                } elseif (preg_match('~^.*(offers|prices|rests).*\.xml$~', $filename)) {
                    $import = $importFactory->create('offers');
                } else {
                    throw new \Exception('Wrong filename "' . $filename . '"');
                }
            }
        }
        
        // Если определили импорт, тогда запустим его
        if (!empty($import) && $import instanceof AbstractImport) {

            $filename = $this->request->get('filename');
            $xmlFile = $integration1C->getFullPath($filename);

            // Запускаем импорт, и утанавливаем результат как контент ответа
            $result = $import->import($xmlFile);
            $this->response->setContent($result);
        }
    }
}