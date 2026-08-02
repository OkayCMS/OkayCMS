<?php

namespace Okay\Controllers;

use Okay\Entities\SupportInfoEntity;
use Okay\Entities\UserAuthAttemptsEntity;

class SupportController extends AbstractController
{
    private const SUPPORT_RATE_LIMIT_KEY = 'support.php';

    public function checkDomain(
        SupportInfoEntity $supportInfoEntity,
        UserAuthAttemptsEntity $userAuthAttemptsEntity
    ) {
        $clientIp = $this->clientIp();
        if (
            $userAuthAttemptsEntity->isBlocked(
                UserAuthAttemptsEntity::ACTION_SUPPORT_ENDPOINT,
                self::SUPPORT_RATE_LIMIT_KEY,
                $clientIp
            )
        ) {
            $this->logSupportSecurityEvent('rate_limited', null);
            $this->response->setStatusCode(429);
            $this->response->setContent(json_encode(['success' => 0, 'error' => 'rate_limited']), RESPONSE_JSON);
            return;
        }

        if (!$this->request->isPost()) {
            $this->rejectSupportRequest($userAuthAttemptsEntity, 405, 'method_not_allowed', 'invalid_method', null);
            return;
        }

        if (!$this->isJsonRequest()) {
            $this->rejectSupportRequest(
                $userAuthAttemptsEntity,
                415,
                'unsupported_media_type',
                'invalid_content_type',
                null
            );
            return;
        }

        $info = $supportInfoEntity->getInfo();
        if (empty($info)) {
            $this->response->setContent(json_encode(['success' => 0, 'error' => 'empty_local_info']), RESPONSE_JSON);
            return;
        }
        /** @var object{temp_key: mixed, temp_time: mixed, new_messages: int, public_key: mixed}&\stdClass $info */

        $rawData = $this->request->post();
        if (!is_string($rawData) || trim($rawData) === '') {
            $this->rejectSupportRequest($userAuthAttemptsEntity, 400, 'empty_data', 'empty_data', null);
            return;
        }

        $data = json_decode($rawData);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->rejectSupportRequest($userAuthAttemptsEntity, 400, 'malformed_json', 'malformed_json', null);
            return;
        }

        $invalidResult = $this->preValidateData($data);
        if (!empty($invalidResult)) {
            $this->rejectSupportRequest(
                $userAuthAttemptsEntity,
                400,
                (string)$invalidResult['error'],
                (string)$invalidResult['error'],
                null
            );
            return;
        }
        /** @var object{action: string, temp_key: mixed, private_key: mixed, public_key: mixed, balance: mixed, new_messages: mixed, key: mixed} $data */

        $result = ['success' => 0];
        switch ($data->action) {
            case 'new_keys':
                $this->logSupportSecurityEvent('new_keys', $data->action);
                $tempKey = $data->temp_key ?? null;
                if (empty($info->temp_key) || empty($info->temp_time) || strtotime($info->temp_time) + 300 < time()) {
                    $supportInfoEntity->updateInfo(['temp_key' => null, 'temp_time' => null]);
                    $result['error'] = 'rule_1';
                    $this->registerFailedSupportAuth($userAuthAttemptsEntity, 'rule_1', $data->action);
                    break;
                }

                if (!is_string($tempKey) || !hash_equals((string)$info->temp_key, $tempKey)) {
                    $result['error'] = 'rule_2';
                    $this->registerFailedSupportAuth($userAuthAttemptsEntity, 'rule_2', $data->action);
                    break;
                }

                $supportInfoEntity->updateInfo([
                    'private_key'  => $data->private_key ?? null,
                    'public_key'   => $data->public_key ?? null,
                    'new_messages' => intval(isset($data->new_messages) ? $data->new_messages : 0),
                    'balance'      => intval(isset($data->balance) ? $data->balance : 0),
                    'temp_key'     => null,
                    'temp_time'    => null,
                ]);
                $result = ['success' => 1];
                $userAuthAttemptsEntity->clear(
                    UserAuthAttemptsEntity::ACTION_SUPPORT_ENDPOINT,
                    self::SUPPORT_RATE_LIMIT_KEY,
                    $clientIp
                );
                $this->logSupportSecurityEvent('new_keys', $data->action, 'success');
                break;
            case 'receive_info':
                $key = $data->key ?? null;
                if (!is_string($key) || empty($info->public_key) || !hash_equals((string)$info->public_key, $key)) {
                    $result['error'] = 'wrong_key';
                    $this->registerFailedSupportAuth($userAuthAttemptsEntity, 'wrong_key', $data->action);
                    break;
                }

                $supportInfoEntity->updateInfo([
                    'balance'      => intval(isset($data->balance) ? $data->balance : 0),
                    'new_messages' => $info->new_messages + intval($data->new_messages),
                ]);
                $result = ['success' => 1];
                $userAuthAttemptsEntity->clear(
                    UserAuthAttemptsEntity::ACTION_SUPPORT_ENDPOINT,
                    self::SUPPORT_RATE_LIMIT_KEY,
                    $clientIp
                );
                break;
        }

        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }

    /**
     * @return array{success: 0, error: string}|null
     */
    private function preValidateData($data): ?array
    {
        $error = null;
        if (empty($data)) {
            $error = 'empty_data';
        } elseif (!is_object($data)) {
            $error = 'invalid_data';
        } elseif (!isset($data->action) || empty($data->action)) {
            $error = 'empty_action';
        }

        if ($error !== null) {
            return ['success' => 0, 'error' => $error];
        }

        return null;
    }

    private function isJsonRequest(): bool
    {
        $contentType = (string)($_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '');

        return stripos($contentType, 'application/json') !== false;
    }

    private function rejectSupportRequest(
        UserAuthAttemptsEntity $userAuthAttemptsEntity,
        int $statusCode,
        string $responseError,
        string $logReason,
        ?string $action
    ): void {
        $this->registerFailedSupportAuth($userAuthAttemptsEntity, $logReason, $action);
        $this->response->setStatusCode($statusCode);
        $this->response->setContent(json_encode(['success' => 0, 'error' => $responseError]), RESPONSE_JSON);
    }

    private function registerFailedSupportAuth(
        UserAuthAttemptsEntity $userAuthAttemptsEntity,
        string $reason,
        ?string $action
    ): void {
        $userAuthAttemptsEntity->registerFailure(
            UserAuthAttemptsEntity::ACTION_SUPPORT_ENDPOINT,
            self::SUPPORT_RATE_LIMIT_KEY,
            $this->clientIp()
        );
        $this->logSupportSecurityEvent('failed_auth', $action, $reason);
    }

    private function logSupportSecurityEvent(string $event, ?string $action, ?string $reason = null): void
    {
        $message = sprintf(
            '[OkayCMS] Support endpoint %s action=%s ip=%s',
            $event,
            $action ?? 'unknown',
            $this->clientIp()
        );

        if ($reason !== null) {
            $message .= ' reason=' . $reason;
        }

        error_log($message);
    }

    private function clientIp(): string
    {
        return (string)($_SERVER['REMOTE_ADDR'] ?? '');
    }
}
