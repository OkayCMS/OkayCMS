<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendSettingsHelper;
use Okay\Helpers\OpenAiHelper;

class SettingsOpenAiAdmin extends IndexAdmin
{
    public function fetch(
        BackendSettingsHelper  $backendSettingsHelper,
        OpenAiHelper $openAiHelper
    ) {
        if ($this->request->method('post')) {
            $backendSettingsHelper->updateOpenAiSettings();
            $this->design->assign('message_success', 'saved');
        }
        $this->design->assign('open_ai_models', $openAiHelper->getTextModels());
        $this->response->setContent($this->design->fetch('settings_open_ai.tpl'));
    }
}