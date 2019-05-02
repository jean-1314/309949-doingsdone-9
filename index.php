<?php
require_once('init.php');
require_once('helpers.php');
require_once('functions.php');

$show_complete_tasks = rand(0, 1);
$userName = 'Иван';

if (!$connection) {
    $error = mysqli_connect_error();
    print('Что-то пошло не так. ' . $error);
} else {
    $projects = getProjectsByUser(1, $connection);
    $projectIds = getProjectIds(1, $connection);
    $tasks = getTasksByProjectId($projectIds, $connection);
}

$page_content = include_template('index.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'show_complete_tasks' => $show_complete_tasks,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Дела в порядке',
    'userName' => $userName,
]);

print($layout_content);
