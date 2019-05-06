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

    if (isset($_GET['id']) && in_array($_GET['id'], $projectIds)) {
        $tasks = getTasksByProjectId($_GET['id'], $connection);
    } else if (isset($_GET['id']) && !in_array($_GET['id'], $projectIds)) {
        http_response_code(404);
    } else {
        $tasks = getTasksByUserProjects($projectIds, $connection);
    }
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
