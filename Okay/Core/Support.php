<?php


namespace Okay\Core;


use Okay\Entities\SupportInfoEntity;

class Support
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var EntityFactory
     */
    private $entityFactory;


    public function __construct(Config $config, Settings $settings, EntityFactory $entityFactory)
    {
        $this->config        = $config;
        $this->settings      = $settings;
        $this->entityFactory = $entityFactory;
    }

    public function addComment($params = []) {
        $supportInfoEntity = $this->entityFactory->get(SupportInfoEntity::class);
        $info = $supportInfoEntity->getInfo();
        if (empty($info->public_key) || empty($params)) {
            return false;
        }
        $params['key'] = $info->public_key;
        $params['action'] = 'add_comment';
        return $this->supportRequest($params);
    }

    public function closeTopic($topic_id) {
        $supportInfoEntity = $this->entityFactory->get(SupportInfoEntity::class);
        $info = $supportInfoEntity->getInfo();
        if (empty($info->public_key) || empty($topic_id)) {
            return false;
        }
        $params = array(
            'topic_id' => $topic_id,
            'key'      => $info->public_key,
            'action'   => 'close_topic'
        );
        return $this->supportRequest($params);
    }

    public function addTopic($params = []) {
        $supportInfoEntity = $this->entityFactory->get(SupportInfoEntity::class);
        $info = $supportInfoEntity->getInfo();
        if (empty($info->public_key) || empty($params)) {
            return false;
        }
        $params['key']    = $info->public_key;
        $params['action'] = 'add_topic';
        return $this->supportRequest($params);
    }

    public function getTopic($params = ['page' => 1]) {
        $supportInfoEntity = $this->entityFactory->get(SupportInfoEntity::class);
        $info = $supportInfoEntity->getInfo();
        if (empty($info->public_key) || empty($params)) {
            return false;
        }
        $params['page']   = max(1, intval($params['page']));
        $params['key']    = $info->public_key;
        $params['action'] = 'get_topic';
        return $this->supportRequest($params);
    }

    public function getTopics($params = ['page' => 1]) {
        $supportInfoEntity = $this->entityFactory->get(SupportInfoEntity::class);
        $info = $supportInfoEntity->getInfo();
        if (empty($info->public_key) || empty($params)) {
            return false;
        }
        $params['page']   = max(1, intval($params['page']));
        $params['key']    = $info->public_key;
        $params['action'] = 'get_topics';
        return $this->supportRequest($params);
    }

    public function getNewKeys($email = '') {
        $supportInfoEntity = $this->entityFactory->get(SupportInfoEntity::class);        
        $info = $supportInfoEntity->getInfo();
        $info->temp_time = strtotime($info->temp_time);
        
        $invalidTempToken = !empty($info->temp_time) && $info->temp_time+300 < time();
        if ($invalidTempToken) {
            $supportInfoEntity->updateInfo(['temp_key'=>null, 'temp_time'=>null]);
            $info->temp_key = null;
        }
        
        $validTempTokenExists = !empty($info->temp_key) && !empty($info->temp_time) && $info->temp_time+300 > time();
        if ($validTempTokenExists) {
            return false;
        }

        $info->temp_time = date('Y-m-d H:i:s');
        $info->temp_key  = md5(uniqid("temp_key", true));
        $supportInfoEntity->updateInfo([
            'temp_time' => $info->temp_time, 
            'temp_key'  => $info->temp_key
        ]);

        $params = [
            'action'       => 'new_keys',
            'temp_key'     => $info->temp_key,
            'version'      => $this->config->version,
            'version_type' => (!empty($this->config->version_type) ? $this->config->version_type : null),
            'owner_email'  => $email,
            'owner_phone'  => $this->settings->get('admin_phone') ? $this->settings->get('admin_phone') : ''
        ];
        
        return $this->supportRequest($params);
    }

    private function supportRequest($params = []) {
        if (empty($params) || empty($params['action'])) {
            return false;
        }

        $supportInfoEntity = $this->entityFactory->get(SupportInfoEntity::class);
        $info = $supportInfoEntity->getInfo();
        $params['domain']       = $_SERVER['HTTP_HOST'];
        $params['version']      = $this->config->version;
        $params['version_type'] = $this->config->version_type;
    
        if (isset($params['accesses'])) {
            openssl_public_encrypt($info->accesses, $params['accesses'], $info->public_key);
            $params['accesses'] = bin2hex($params['accesses']);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://okay-cms.support/support/1.0/');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=UTF-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_POST, 1);
        $response = curl_exec($ch);

        curl_close($ch);
        $response = json_decode($response);
        if ($response && isset($response->balance) && $response->balance != $info->balance) {
            $supportInfoEntity->updateInfo(['balance'=>$response->balance]);
        }

        return $response;
    }
}