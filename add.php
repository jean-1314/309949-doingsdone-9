<?php
require_once('init.php');
require_once('helpers.php');
require_once('functions.php');

$userName = 'Иван';
$task = [];
$errors = [];

if (!$connection) {
    $error = mysqli_connect_error();
    print('Что-то пошло не так. ' . $error);
} else {
    $projects = getProjectsByUser(1, $connection);
    $projectIds = getProjectIds(1, $connection);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task = $_POST;
    $fileName = '';
    $required = ['title', 'project'];

    foreach ($required as $key) {
		if (empty($_POST[$key])) {
            $errors[$key] = 'Это поле надо заполнить';
		}
    }

    foreach ($_POST as $key => $value) {
        switch ($key) {
            case 'title':
                if ($value == '') {
                    $errors[$key] = 'Название не должно быть пустым';
                }
                break;
            case 'project':
                if (!in_array($value, $projectIds)) {
                    $errors[$key] = 'Выбран некорректный проект';
                }
                break;
            case 'date':
                if (!is_date_valid($value)) {
                    $errors[$key] = 'Введите дату в формате ГГГГ-ММ-ДД';
                }

                if (!isDateValid($value)) {
                    $errors[$key] = 'Дата должна быть больше или равна текущей';
                }
                break;
        }
    }

    if (isset($_FILES['file'])) {
        $fileName = $_FILES['file']['name'];
        $filePath = __DIR__ . '/';
        move_uploaded_file($_FILES['file']['tmp_name'], $filePath . $fileName);
    }

    if (empty($errors)) {
        $sql = 'INSERT INTO tasks (title, created_at, status, file_name, deadline, project_id) VALUES (?, NOW(), 0, ?, ?, ?)';
        db_insert_data($connection, $sql, [$task['title'], $fileName, $task['date'], $task['project']]);
    }
}

$page_content = include_template('add.php', [
    'projects' => $projects,
    'task' => $task,
    'errors' => $errors,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Дела в порядке',
    'userName' => $userName,
]);

print($layout_content);
