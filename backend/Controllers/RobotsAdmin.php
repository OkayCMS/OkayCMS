<?php


namespace Okay\Admin\Controllers;


class RobotsAdmin extends IndexAdmin
{

    public function fetch()
    {
        if ($this->request->post()){
            $robots_data = $this->request->post('robots');
            $this->getRobots($robots_data,'write');
        }

        /*Обновление файла*/
        $robots_txt = $this->getRobots('','read');
        $this->design->assign('robots_txt', $robots_txt);
        $perms = is_writable('robots.txt');
        if (!$perms) {
            $this->design->assign('message_error','write_error');
        }
        
        $this->response->setContent($this->design->fetch('robots.tpl'));
    }

    /*Чтение/Запись файла*/
    private function getRobots($data,$type)
    {
        if ($type == 'write') {
            $perms = is_writable('robots.txt');
            if ($perms) {
                file_put_contents('robots.txt', strip_tags($data), LOCK_EX);
                $this->design->assign('message_success', 'updated');
            } else {
                $this->design->assign('message_error','write_error');
            }
        } elseif ($type='read') {
            $robots = file_get_contents("robots.txt");
            return $robots;
        }
    }

}
