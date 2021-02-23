<?php


namespace Okay\Admin\Controllers;


use Okay\Entities\SupportInfoEntity;
use Okay\Core\Support;
use Okay\Core\Request;

class TopicAdmin extends IndexAdmin {

    public function fetch(SupportInfoEntity $supportInfoEntity, Support $support) {
        $supportInfo = $supportInfoEntity->getInfo();

        if (empty($supportInfo->public_key)) {
            $this->response->redirectTo($this->request->getRootUrl() . '/backend/index.php?controller=SupportAdmin');
        }

        if (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '0:0:0:0:0:0:0:1'))) {
            $this->design->assign('message_error', 'localhost');
            $this->response->setContent($this->design->fetch('topic.tpl'));
            return;
        }

        $this->design->assign("accesses", $supportInfo->accesses);
        $topic = new \stdClass();
        if ($this->request->method('post')) {

            $accesses = $this->request->post('accesses');
            $supportInfoEntity->updateInfo(['accesses'=>$accesses]);
            $this->design->assign('accesses', $accesses);
            $topic->id = $this->request->post('id', 'integer');

            if ($this->request->post('new_message')) {
                $topic = new \stdClass();
                $topic->id = $this->request->post('id', 'integer');
                $topic->header = $this->request->post('header');

                $comment = new \stdClass();
                $comment->text = $this->request->post('comment_text');
                if (!$topic->id && empty($topic->header)) {
                    $this->design->assign('message_error', 'empty_name');
                    $this->design->assign('topic_message', $comment->text);
                } elseif (empty($comment->text)) {
                    $this->design->assign('message_error', 'empty_comment');
                    $this->design->assign('topic_header', $topic->header);
                } else {
                    $manager = $this->design->getVar('manager');
                    $comment->manager = $manager->login;
                    if (empty($topic->id)) {
                        $result = $support->addTopic([
                            'header'=>$topic->header,
                            'manager'=>$comment->manager,
                            'text'=>$comment->text,
                            'accesses'=>$accesses,
                        ]);
                    } else {
                        $result = $support->addComment([
                            'topic_id'=>$topic->id,
                            'manager'=>$comment->manager,
                            'text'=>$comment->text,
                            'accesses'=>$accesses,
                        ]);
                    }

                    if (!$result) {
                        $this->design->assign('message_error', 'unknown_error');
                    } elseif (!$result->success) {
                        $this->design->assign('message_error', $result->error ? $result->error : 'unknown_error');
                    } else {
                        if (isset($result->topic_id)) {
                            $topic->id = $result->topic_id;
                        }
                        $this->design->assign('message_success', 'added');
                    }
                }
            } elseif ($this->request->post('close_topic') && $topic->id) {
                $result = $support->closeTopic($topic->id);
                if (!$result) {
                    $this->design->assign('message_error', 'unknown_error');
                } elseif (!$result->success) {
                    $this->design->assign('message_error', $result->error ? $result->error : 'unknown_error');
                } else {
                    $this->design->assign('message_success', 'closed');
                }
            }
        } else {
            $topic->id = $this->request->get('id', 'integer');
        }

        if ($topic->id) {
            $filter = array();
            $filter['topic_id'] = $topic->id;
            $filter['page'] = max(1, $this->request->get('page', 'integer'));
            $filter['limit'] = 100;

            // Поиск
            $keyword = $this->request->get('keyword', 'string');
            if (!empty($keyword)) {
                $filter['keyword'] = $keyword;
                $this->design->assign('keyword', $keyword);
            }

            $result = $support->getTopic($filter);
            if (empty($result)) {
                $this->design->assign('message_error', 'unknown_error');
            } elseif (empty($result->success)) {
                $this->design->assign('message_error', $result->error ? $result->error : 'unknown_error');
            } else {
                $result->comments = (array)$result->comments;
                if (!empty($result->comments)) {
                    $read_messages = 0;
                    foreach ($result->comments as $comment) {
                        if (!$comment->is_read) {
                            $read_messages++;
                        }
                    }
                    if ($read_messages) {
                        $supportInfoEntity->updateInfo(['new_messages' => max(0, $supportInfo->new_messages - $read_messages)]);
                        $this->design->assign('support_info', $supportInfoEntity->getInfo());
                    }
                }

                $this->design->assign('topic', $result->topic);
                $this->design->assign('comments_count', $result->comments_count);
                $this->design->assign('pages_count', ceil($result->comments_count / $filter['limit']));
                $this->design->assign('current_page', $filter['page']);
                $this->design->assign('comments', $result->comments);
            }
        }

        $this->design->assign('subfolder', Request::getSubDir());

        $this->response->setContent($this->design->fetch('topic.tpl'));
    }

}
