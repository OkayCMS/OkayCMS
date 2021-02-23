<?php

use Okay\Entities\UsersEntity;

require_once 'configure.php';

if (!$managers->access('orders', $manager) && !$managers->access('users', $manager)) {
    exit();
}

$keyword = $request->get('query', 'string');

/** @var UsersEntity $usersEntity */
$usersEntity = $entityFactory->get(UsersEntity::class);

$users = $usersEntity->find(['keyword' => $keyword]);

$suggestions = [];
foreach($users as $user) {
    $suggestion = new \stdClass();
    $suggestion->value = $user->name." ($user->email)";
    $suggestion->data = $user;
    $suggestions[] = $suggestion;
}

$result = new \stdClass;
$result->query = $keyword;
$result->suggestions = $suggestions;
$response->setContent(json_encode($result), RESPONSE_JSON);
$response->sendContent();
