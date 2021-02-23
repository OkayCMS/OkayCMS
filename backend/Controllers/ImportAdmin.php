<?php


namespace Okay\Admin\Controllers;


use Okay\Core\Import;
use Okay\Core\Request;
use Okay\Core\QueryFactory;
use Okay\Entities\FeaturesEntity;

class ImportAdmin extends IndexAdmin
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Import
     */
    protected $importCore;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;


    public function fetch(
        Import $importCore,
        Request $request,
        QueryFactory $queryFactory
    ) {
        $this->request      = $request;
        $this->importCore   = $importCore;
        $this->queryFactory = $queryFactory;
        
        $this->design->assign('import_files_dir', $importCore->getImportFilesDir());
        if(!is_writable($importCore->getImportFilesDir())) {
            $this->design->assign('message_error', 'no_permission');
        }
        
        $oldLocale = setlocale(LC_ALL, 0);
        setlocale(LC_ALL, $importCore->getLocale());
        if (setlocale(LC_ALL, 0) != $importCore->getLocale()) {
            $this->design->assign('message_error', 'locale_error');
            $this->design->assign('locale', $importCore->getLocale());
        }
        setlocale(LC_ALL, $oldLocale);

        if ($request->method('post')) {
            if ($request->files("file") && $request->files("file")['error'] == UPLOAD_ERR_OK) {
                $uploaded_name = $request->files("file", "tmp_name");
                $temp = tempnam($importCore->getImportFilesDir(), 'temp_');
                if (!move_uploaded_file($uploaded_name, $temp)) {
                    $this->design->assign('message_error', 'upload_error');
                }

                if (!$this->convertFile($temp, $importCore->getImportFilesDir() . $importCore->getImportFile())) {
                    $this->design->assign('message_error', 'convert_error');
                } else {
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
                unlink($temp);
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
            $file->date = date("d.m.Y H:i:s", filemtime($importCore->getImportFilesDir() . $importCore->getImportFile()));
            $file->size = filesize($importCore->getImportFilesDir() . $importCore->getImportFile());
        }
        $this->design->assign('file', $file);
        
        $this->response->setContent($this->design->fetch('import.tpl'));
    }
    
    private function convertFile($source, $dest) {
        // Узнаем какая кодировка у файла
        $testString = file_get_contents($source, null, null, null, 1000000);

        if ($this->isUtf8Encoding($testString)) {
            return copy($source, $dest);
        }

        // Конвертируем в UFT8
        if(!$src = fopen($source, "r")) {
            return false;
        }

        if(!$dst = fopen($dest, "w")) {
            return false;
        }

        while (($line = fgets($src, 4096)) !== false) {
            $line = $this->winToRtf($line);
            fwrite($dst, $line);
        }
        fclose($src);
        fclose($dst);
        return true;
    }

    private function isUtf8Encoding($data)
    {
        if (preg_match('//u', $data)) {
            return true;
        }

        return false;
    }

    private function winToRtf($text) {
        if (function_exists('iconv')) {
            return @iconv('windows-1251', 'UTF-8', $text);
        } else {
            $t = '';
            for($i=0, $m=strlen($text); $i<$m; $i++) {
                $c=ord($text[$i]);
                if ($c<=127) {$t.=chr($c); continue; }
                if ($c>=192 && $c<=207) {$t.=chr(208).chr($c-48);  continue; }
                if ($c>=208 && $c<=239) {$t.=chr(208).chr($c-48);  continue; }
                if ($c>=240 && $c<=255) {$t.=chr(209).chr($c-112); continue; }
                if ($c==184) { $t.=chr(209).chr(145); continue; }; #ё
                if ($c==168) { $t.=chr(208).chr(129); continue; }; #Ё
                if ($c==179) { $t.=chr(209).chr(150); continue; }; #і
                if ($c==178) { $t.=chr(208).chr(134); continue; }; #І
                if ($c==191) { $t.=chr(209).chr(151); continue; }; #ї
                if ($c==175) { $t.=chr(208).chr(135); continue; }; #ї
                if ($c==186) { $t.=chr(209).chr(148); continue; }; #є
                if ($c==170) { $t.=chr(208).chr(132); continue; }; #Є
                if ($c==180) { $t.=chr(210).chr(145); continue; }; #ґ
                if ($c==165) { $t.=chr(210).chr(144); continue; }; #Ґ
                if ($c==184) { $t.=chr(209).chr(145); continue; }; #Ґ
            }
            return $t;
        }
    }

    private function assignColumnsInfo($fields = array()) {
        $source_columns = $this->importCore->getColumns();
        $this->design->assign('columns_names', array_keys($this->importCore->getColumnsNames()));

        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement("SELECT f.name FROM ".FeaturesEntity::getTable()." f ORDER BY f.position");
        $this->db->query($sql);

        $features = $this->db->results('name');
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
            $c->is_nf_selected = !$c->is_exist && $c->value==$c->name;
            $column = $c;
        }
        $this->design->assign('source_columns', $source_columns);
    }
    
}
