<?php
require_once('init.php');
require_once('helpers.php');
require_once('functions.php');

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: /');
}

$projects = [];
$projectData = [];
$error = false;
$errorText = '';
$userData = $_SESSION['user'] ?: [];

if (!$connection) {
    $connectionError = mysqli_connect_error();
    print('Что-то пошло не так. ' . $connectionError);
} else {
    $projects = getProjectsByUser($userData['id'], $connection);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectData = $_POST;
    $projectTitles = getProjectTitles($userData['id'], $connection);

    if ($projectData['title'] === '') {
        $error = true;
        $errorText = 'Это поле надо заполнить';
    } elseif (in_array($projectData['title'], $projectTitles)) {
        $error = true;
        $errorText = 'Название проекта совпадает с названием одного из существующих проектов';
    }

    if (!$error) {
        $sql = 'INSERT INTO projects (title, author_id, created_at) VALUES (?, ?, NOW())';
        $result = db_insert_data($connection, $sql, [htmlspecialchars($projectData['title']), $userData['id']]);
    }

    if ($result) {
        header('Location: /');
    }
}

$page_content = include_template('add-project.php', [
    'projects' => $projects,
    'projectData' => $projectData,
    'error' => $error,
    'errorText' => $errorText,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Дела в порядке | Добавление проекта',
    'userData' => $userData,
]);

print($layout_content);
