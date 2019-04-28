<?php
require_once('helpers.php');
require_once('init.php');
$show_complete_tasks = rand(0, 1);
$userName = 'Иван';

if (!$connection) {
    $error = mysqli_connect_error();
    print('Что-то пошло не так. ' . $error);
} else {
    // Запрос на получение всех проектов пользователя
    $sql = 'SELECT p.id, p.title, p.created_at,
        (
            SELECT COUNT(*)
            FROM tasks t
            WHERE t.project_id = p.id
        ) AS tasks_count
        FROM projects p
        WHERE p.author_id = 1';

    $projects = db_fetch_data($connection, $sql);


    // запрос на получение задач из полученных проектов
    $sql = 'SELECT t.id, t.title, t.created_at, t.status, t.file_name, t.deadline, p.title AS project_title
        FROM tasks t
        INNER JOIN projects p
        ON t.project_id = p.id
        WHERE p.id = 1 OR p.id = 2';

    $tasks = db_fetch_data($connection, $sql);
}

/**
 * calculateTimeToDeadline
 *
 * @param  string $tsString
 *
 * @return string
 */
function calculateTimeToDeadline(string $tsString): string
{
    $currentTimeStamp = time();
    $taskTs = strtotime($tsString);
    return $taskTs - $currentTimeStamp;
}

/**
 * isDeadlineClose
 *
 * @param  string $taskDatetime
 *
 * @return boolean
 */
function isDeadlineClose(string $taskDatetime): bool
{
    $hoursInDay = 3600;
    $hoursToDeadline = 24;
    return floor(calculateTimeToDeadline($taskDatetime) / $hoursInDay) < $hoursToDeadline;
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
