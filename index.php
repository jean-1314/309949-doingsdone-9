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
        $projectIdParamExists = isset($_GET['id']);
        $projects = getProjectsByUser($userData['id'], $connection);
        $projectIds = getProjectIds($userData['id'], $connection);

        if ($projectIdParamExists && in_array($_GET['id'], $projectIds)) {
            $tasks = getTasksByProjectId($_GET['id'], $connection);
        } else if ($projectIdParamExists && !in_array($_GET['id'], $projectIds)) {
            http_response_code(404);
        } else if (!empty($projectIds)) {
            $tasks = getTasksByUserProjects($projectIds, $connection);
        } else {
            $tasks = [];
        }

        if (!$projectIdParamExists && isset($_GET['filter'])) {
            $tasks = getTasksByFilter($projectIds, $_GET['filter'], $connection);
        } else if ($projectIdParamExists && isset($_GET['filter'])) {
            $tasks = getTasksByFilterAndId($_GET['id'], $_GET['filter'], $connection);
        }

        if (isset($_GET['task_id']) && isset($_GET['check'])) {
            $safeTaskId = mysqli_real_escape_string($connection, $_GET['task_id']);
            $safeCheck = mysqli_real_escape_string($connection, $_GET['check']);

            $sql = 'UPDATE tasks SET status = ' . $safeCheck . ' WHERE id = ' . $safeTaskId;
            $result = mysqli_query($connection, $sql);

            if ($result) {
                header('Location: index.php');
            }
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
