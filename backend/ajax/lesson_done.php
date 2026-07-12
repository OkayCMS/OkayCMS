<?php

require_once 'configure.php';

use Okay\Entities\LessonsEntity;

if (!$managers->access('learning', $manager)) {
    exit();
}

if (($_SERVER['HTTP_X_OKAY_SESSION_ID'] ?? null) !== session_id()) {
    exit();
}

$lessonsEntity = $entityFactory->get(LessonsEntity::class);
$lessonsEntity->update($request->get('lesson'), [
    'done' => 1
]);
