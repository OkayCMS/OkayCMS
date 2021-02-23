<?php

require_once 'configure.php';

use Okay\Entities\LessonsEntity;

if (!$managers->access('learning', $manager)) {
    exit();
}

$lessonsEntity = $entityFactory->get(LessonsEntity::class);
$lessonsEntity->update($request->get('lesson'), [
    'done' => 1
]);