<?php


namespace Okay\Admin\Controllers;


use Okay\Entities\LessonsEntity;

class LearningAdmin extends IndexAdmin
{
    public function fetch(LessonsEntity $lessonsEntity)
    {
        if ($this->skipLearning()) {
            $lessonsEntity->doneAll();
        }

        if ($this->lessonDone()) {
            $lessonId = $this->request->post('lesson_id');
            $lessonsEntity->update($lessonId, ['done' => 1]);
        }

        $countAllLessons = $lessonsEntity->count();
        $countDoneLessons = $lessonsEntity->count(['done' => 1]);
        $progress = ceil($countDoneLessons / $countAllLessons  * 100);

        $lessons = $lessonsEntity->find();
        $this->design->assign('progress', $progress);
        $this->design->assign('lessons', $lessons);
        $this->response->setContent($this->design->fetch('learning.tpl'));
    }

    private function lessonDone()
    {
        return !empty($this->request->post('action')) &&
               $this->request->post('action') === 'lesson_done';
    }

    private function skipLearning()
    {
        return !empty($this->request->post('skip_learning'));
    }
}