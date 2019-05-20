<?php
require_once('init.php');
require_once('helpers.php');
require_once('functions.php');

session_start();

$userData = [];
$show_complete_tasks = rand(0, 1);

if (isset($_SESSION['user'])) {
    $userData = $_SESSION['user'];

    if (!$connection) {
        $error = mysqli_connect_error();
        print('Что-то пошло не так. ' . $error);
    } else {
        $projects = getProjectsByUser($userData['id'], $connection);
        $projectIds = getProjectIds($userData['id'], $connection);

        if (isset($_GET['id']) && in_array($_GET['id'], $projectIds)) {
            $tasks = getTasksByProjectId($_GET['id'], $connection);
        } else if (isset($_GET['id']) && !in_array($_GET['id'], $projectIds)) {
            http_response_code(404);
        } else if (!empty($projectIds)) {
            $tasks = getTasksByUserProjects($projectIds, $connection);
        } else {
            $tasks = [];
        }
    }

    $page_content = include_template('index.php', [
        'projects' => $projects,
        'tasks' => $tasks,
        'show_complete_tasks' => $show_complete_tasks,
    ]);
} else {
    $page_content = include_template('guest.php');
}


$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Дела в порядке',
    'userData' => $userData,
]);

print($layout_content);
