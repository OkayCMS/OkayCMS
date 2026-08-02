<?php

namespace Okay\Admin\Controllers;

use Okay\Core\Import;
use Okay\Core\Import\CsvImportNormalizer;
use Okay\Core\Request;
use Okay\Core\QueryFactory;
use Okay\Entities\FeaturesEntity;

class ImportAdmin extends IndexAdmin
{
    /** @var Request */
    protected $request;

    /** @var Import */
    protected $importCore;

    /** @var QueryFactory */
    protected $queryFactory;


    /** @var FeaturesEntity */
    protected $featuresEntity;


    public function fetch(
        Import $importCore,
        CsvImportNormalizer $csvImportNormalizer,
        Request $request,
        QueryFactory $queryFactory,
        FeaturesEntity $featuresEntity
    ) {
        $this->request      = $request;
        $this->importCore   = $importCore;
        $this->queryFactory = $queryFactory;

        $this->featuresEntity = $featuresEntity;

        $this->design->assign('import_files_dir', $importCore->getImportFilesDir());
        if (!is_writable($importCore->getImportFilesDir())) {
            $this->design->assign('message_error', 'no_permission');
        }

        $oldLocale = setlocale(LC_ALL, 0);
        setlocale(LC_ALL, $importCore->getLocale());
        if (setlocale(LC_ALL, 0) != $importCore->getLocale()) {
            $this->design->assign('message_error', 'locale_error');
            $this->design->assign('locale', $importCore->getLocale());
        }
        if ($oldLocale !== false) {
            setlocale(LC_ALL, $oldLocale);
        }

        if ($request->method('post')) {
            $uploadedFile = $request->files("file");
            if (is_array($uploadedFile) && ($uploadedFile['error'] ?? null) == UPLOAD_ERR_OK) {
                $uploaded_name = $request->files("file", "tmp_name");
                $temp = tempnam($importCore->getImportFilesDir(), 'temp_');
                if (!is_string($uploaded_name) || $temp === false) {
                    $this->design->assign('message_error', 'upload_error');
                } elseif (!move_uploaded_file($uploaded_name, $temp)) {
                    $this->design->assign('message_error', 'upload_error');
                }

                if (!$this->design->getVar('message_error')) {
                    try {
                        if (!is_string($temp)) {
                            throw new \RuntimeException('Import temp file was not created.');
                        }

                        $csvImportNormalizer->normalize($temp, $importCore->getImportFilesDir() . $importCore->getImportFile());
                    } catch (\RuntimeException) {
                        $this->design->assign('message_error', 'convert_error');
                    }
                }

                if (!$this->design->getVar('message_error')) {
                    $importCore->initColumns();
                    $lcColumns = array_map("mb_strtolower", $importCore->getColumns());
                    $duplicatedColumns = array_diff_assoc($lcColumns, array_unique($lcColumns));
                    $duplicatedColumns = array_unique($duplicatedColumns);
                    $duplicatedColumns_pairs = array();
                    foreach ($this->importCore->getColumnsNames() as $columns) {
                        $cnt = 0;
                        foreach ($columns as $column) {
                            if (in_array(mb_strtolower($column), $lcColumns) && ++$cnt > 1) {
                                $duplicatedColumns_pairs[] = $columns;
                            }
                        }
                    }
                    if (!empty($duplicatedColumns)) {
                        $this->design->assign('message_error', 'duplicated_columns');
                        $this->design->assign('duplicated_columns', $duplicatedColumns);
                    } elseif (!empty($duplicatedColumns_pairs)) {
                        $this->design->assign('message_error', 'duplicated_columns_pairs');
                        $this->design->assign('duplicated_columns_pairs', $duplicatedColumns_pairs);
                    } else {
                        $this->design->assign('filename', $request->files("file", "name"));
                        $this->assignColumnsInfo();
                    }
                }
                // Валідація шляху: перевіряємо, що тимчасовий файл знаходиться в дозволеній директорії
                if (!empty($temp) && strpos($temp, '..') === false) {
                    $real_temp = realpath($temp);
                    $real_import_dir = realpath($importCore->getImportFilesDir());
                    if ($real_temp !== false && $real_import_dir !== false && strpos($real_temp, $real_import_dir) === 0) {
                        @unlink($temp);
                    }
                }
            } elseif ($request->post('import')) {
                unset($_SESSION['csv_fields']);
                $fields = $request->post('csv_fields');
                if (empty($fields) || !in_array('sku', $fields) && !in_array('name', $fields)) {
                    $this->design->assign('message_error', 'required_fields');
                    $this->design->assign('filename', 1);
                    $importCore->initColumns();
                    $this->assignColumnsInfo($fields);
                } else {
                    $_SESSION['csv_fields'] = $fields;
                    $this->design->assign('import', 1);
                }
            }
        }

        $file = new \stdClass();
        if (file_exists($importCore->getImportFilesDir() . $importCore->getImportFile())) {
            $file->name = $importCore->getImportFile();
            $fileTime = filemtime($importCore->getImportFilesDir() . $importCore->getImportFile());
            $file->date = $fileTime !== false ? date("d.m.Y H:i:s", $fileTime) : '';
            $file->size = filesize($importCore->getImportFilesDir() . $importCore->getImportFile());
        }
        $this->design->assign('file', $file);

        $this->response->setContent($this->design->fetch('import.tpl'));
    }

    private function assignColumnsInfo($fields = array())
    {
        $source_columns = $this->importCore->getColumns();
        $this->design->assign('columns_names', array_keys($this->importCore->getColumnsNames()));

        $features = $this->featuresEntity->col('name')->order('position')->find();

        $this->design->assign('features', $features);

        $this->importCore->initInternalColumns();
        $internal_columns = array_keys($this->importCore->getInternalColumnsNames());

        if (empty($fields)) {
            $selected = array();
            foreach ($features as $f) {
                $selected[$f] = $f;
            }
            $selected = array_merge($selected, $this->importCore->getInternalColumnsNames());
        } else {
            $selected = $fields;
        }

        foreach ($source_columns as &$column) {
            $c = new \stdClass();
            $c->name = $column;
            $c->value = isset($selected[$c->name]) ? $selected[$c->name] : '';
            $c->is_feature = in_array($c->name, $features);
            $c->is_exist = in_array($c->name, $internal_columns) || $c->is_feature;
            $c->is_nf_selected = !$c->is_exist && $c->value == $c->name;
            $column = $c;
        }
        $this->design->assign('source_columns', $source_columns);
    }
}
