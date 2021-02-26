<?php


namespace Okay\Admin\Controllers;


use Okay\Entities\SupportInfoEntity;
use Okay\Core\Support;

class SupportAdmin extends IndexAdmin
{

    public function fetch(Support $support, SupportInfoEntity $supportInfoEntity)
    {
        $error = '';
        if ($this->request->method('post') && !empty($this->request->post('get_new_keys'))) {
            $result = $support->getNewKeys($this->manager->email);
            if (is_null($result) || (empty($result) && $result!==false)) {
                $error = 'unknown_error';
            } elseif ($result === false) {
                $error = 'request_has_already_sent';
            } elseif (!$result->success) {
                $error = $result->error ? $result->error : 'unknown_error';
            } else {
                $this->response->addHeader("Refresh:0");
                $this->response->sendHeaders();
                exit();
            }
        }
        
        if ($this->request->method('post') && !empty($this->request->post('manual_save_keys'))) {
            $supportInfoEntity->updateInfo([
                'public_key' => str_replace("\r\n", "\n", trim($this->request->post('public_key')) . "\r\n"),
                'private_key' => str_replace("\r\n", "\n", trim($this->request->post('private_key')) . "\r\n"),
            ]);
            $this->response->addHeader("Refresh:0");
            $this->response->sendHeaders();
            exit();
        }

        $supportInfo = $supportInfoEntity->getInfo();
        if ($error) {
            $this->design->assign('message_error', $error);
        } elseif (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '0:0:0:0:0:0:0:1'))) {
            $this->design->assign('message_error', 'localhost');
        } elseif (empty($supportInfo->public_key)) {
            $this->design->assign('message_error', 'empty_key');
        } else {
            // Обработка действий
            /*if ($this->request->method('post')) {
                // Действия с выбранными
                $ids = $this->request->post('check');
                if (is_array($ids)) {
                    switch ($this->request->post('action')) {
                        case 'close': {
                            // TODO close topic
                            break;
                        }
                    }
                }
            }*/

            $filter = array();
            $filter['page'] = max(1, $this->request->get('page', 'integer'));
            $filter['limit'] = 100;

            // Поиск
            $keyword = $this->request->get('keyword', 'string');
            if (!empty($keyword)) {
                $filter['keyword'] = $keyword;
                $this->design->assign('keyword', $keyword);
            }

            $result = $support->getTopics($filter);
            if (!$result) {
                $this->design->assign('message_error', 'unknown_error');
            } elseif (!$result->success) {
                $this->design->assign('message_error', $result->error ? $result->error : 'unknown_error');
            } else {
                $this->design->assign('topics_count', $result->topics_count);
                $this->design->assign('pages_count', ceil($result->topics_count / $filter['limit']));
                $this->design->assign('current_page', $filter['page']);
                $this->design->assign('topics', (array)$result->topics);
            }
        }

        $this->response->setContent($this->design->fetch('support.tpl'));
    }

}
