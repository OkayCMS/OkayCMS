<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendSettingsHelper;
use Okay\Core\Languages;
use Okay\Core\Notify;

class SettingsNotifyAdmin extends IndexAdmin
{

    public function fetch(
        BackendSettingsHelper $backendSettingsHelper,
        Languages $languages
    ) {
        if ($this->request->method('POST')) {
            $backendSettingsHelper->updateNotifySettings();
            $this->design->assign('message_success', 'saved');
        }

        $btrLanguages = [];
        foreach ($languages->getLangList() as $label=>$l) {
            if (file_exists("backend/lang/".$label.".php")) {
                $btrLanguages[$l->name] = $l->label;
            }
        }

        $this->design->assign('btr_languages', $btrLanguages);
        $this->response->setContent($this->design->fetch('settings_notify.tpl'));
    }

    public function testSMTP(Notify $notify)
    {

        $this->settings->set('smtp_server', $this->request->post('smtp_server'));
        $this->settings->set('smtp_port', $this->request->post('smtp_port'));
        $this->settings->set('smtp_user', $this->request->post('smtp_user'));
        $this->settings->set('smtp_pass', $this->request->post('smtp_pass'));
        $this->settings->set('disable_validate_smtp_certificate', $this->request->post('disable_validate_smtp_certificate', 'int'));

        $from = ($this->settings->get('notify_from_name') ? $this->settings->get('notify_from_name')." <".$this->settings->get('notify_from_email').">" : $this->settings->get('notify_from_email'));

        $trace = $notify->SMTP(
            $this->manager->email,
            'Test SMTP connection',
            'Test SMTP connection',
            $from,
            '',
            true
        );

        $result['status'] = false;
        if ($trace === true) {
            $result['message'] = 'Connected ok!';
            $result['status']  = true;
        } else {
            $result['trace'] = $trace;
            $result['message'] = 'Connect failed';
        }
        
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
    
}
