<?php
require_once('init.php');
require_once('helpers.php');
require_once('functions.php');

session_start();

$userData = [];

if (isset($_SESSION['user'])) {
    $userData = $_SESSION['user'];

    if (!$connection) {
        $error = mysqli_connect_error();
        print('Что-то пошло не так. ' . $error);
    } else {
        $projectIdParamExists = isset($_GET['id']);
        $projects = getProjectsByUser($userData['id'], $connection);
        $projectIds = getProjectIds($userData['id'], $connection);
        $show_completed_tasks = isset($_GET['show_completed']) ? $_GET['show_completed'] : 0;

        if ($projectIdParamExists && in_array($_GET['id'], $projectIds)) {
            $tasks = getTasksByProjectId($_GET['id'], $connection);
        } elseif ($projectIdParamExists && !in_array($_GET['id'], $projectIds)) {
            http_response_code(404);
        } elseif (!empty($projectIds)) {
            $tasks = getTasksByUserProjects($projectIds, $connection);
        } else {
            $tasks = [];
        }

        if (!$projectIdParamExists && isset($_GET['filter'])) {
            $tasks = getTasksByFilter($projectIds, $_GET['filter'], $connection);
        } elseif ($projectIdParamExists && isset($_GET['filter'])) {
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

        if (isset($_GET['search'])) {
            mysqli_query($connection, 'CREATE FULLTEXT INDEX tasksFulltextSearch ON tasks(title)');

            if ($_GET['search']) {
                $sql = 'SELECT t.id, t.title, t.status, t.file_name, t.deadline, t.project_id FROM tasks t'
                    . ' JOIN projects p ON p.id = t.project_id'
                    . ' WHERE p.author_id = ' . $userData['id']
                    . ' AND MATCH(t.title) AGAINST(?)';
                $tasks = db_fetch_data($connection, $sql, [$_GET['search']]);
            }
        }
    }

    $page_content = include_template('index.php', [
        'projects' => $projects,
        'tasks' => $tasks,
        'show_completed_tasks' => $show_completed_tasks,
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
