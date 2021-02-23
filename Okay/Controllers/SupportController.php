<?php


namespace Okay\Controllers;


use Okay\Entities\SupportInfoEntity;

class SupportController extends AbstractController
{
    public function checkDomain(SupportInfoEntity $supportInfoEntity) {
        $info = $supportInfoEntity->getInfo();
        if (empty($info)) {
            $this->response->setContent(json_encode(['success' => 0, 'error' => 'empty_local_info']), RESPONSE_JSON);
            return;
        }

        $data = $this->request->post();
        $data = json_decode($data);

        $invalidResult = $this->preValidateData($data);
        if (!empty($invalidResult)) {
            $this->response->setContent(json_encode($invalidResult), RESPONSE_JSON);
            return;
        }

        $result = ['success' => 0];
        switch ($data->action) {
            case 'new_keys': {
                if (empty($info->temp_key) || empty($info->temp_time) || strtotime($info->temp_time)+300 < time()) {
                    $supportInfoEntity->updateInfo(['temp_key'=>null, 'temp_time'=>null]);
                    $result['error'] = 'rule_1';
                    break;
                }
                if ($info->temp_key != $data->temp_key) {
                    $result['error'] = 'rule_2';
                    break;
                }                

                $info->temp_time = strtotime($info->temp_time);
                $supportInfoEntity->updateInfo([
                    'private_key'  => $data->private_key,
                    'public_key'   => $data->public_key,
                    'new_messages' => intval(isset($data->new_messages) ? $data->new_messages : 0),
                    'balance'      => intval(isset($data->balance) ? $data->balance : 0),
                    'temp_key'     => null,
                    'temp_time'    => null
                ]);
                $result = ['success' => 1];
                break;
            }
            case 'receive_info': {
                if (empty($data->key) || empty($info->public_key) || $data->key != $info->public_key) {
                    $result['error'] = 'wrong_key';
                    break;
                }
                $supportInfoEntity->updateInfo([
                    'balance'      => intval(isset($data->balance) ? $data->balance : 0),
                    'new_messages' => $info->new_messages + intval($data->new_messages)
                ]);
                $result = ['success' => 1];
                break;
            }
        }

        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }

    private function preValidateData($data) {
        $error = null;
        if (empty($data)) {
            $error = 'empty_data';
        } elseif (!is_object($data)) {
            $error = 'invalid_data';
        } elseif (!isset($data->action) || empty($data->action)) {
            $error = 'empty_action';
        }

        $errorMatch = !is_null($error);
        if ($errorMatch) {
            return ['success' => 0, 'error' => $error];
        }

        return null;
    }

}