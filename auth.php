<?php
require_once('init.php');
require_once('helpers.php');
require_once('functions.php');

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = $_POST;
    $required = ['email', 'password'];
    $errors = [];
    $values = [];

	foreach ($required as $field) {
	    if (empty($form[$field])) {
	        $errors[$field] = 'Это поле надо заполнить';
        }
    }

    $email = mysqli_real_escape_string($connection, $form['email']);
    $result = getUserByEmail($email, $connection);
    $user = $result[0] ?? null;

    if (!count($errors) and $user) {
        if (password_verify($form['password'], $user['password'])) {
            $_SESSION['user'] = $user;
        } else {
            $errors['password'] = 'Неверный пароль';
        }
    } else {
        $errors['email'] = 'Такой пользователь не найден';
    }

    $values = $form;

    if (count($errors)) {
        $page_content = include_template('auth.php', [
            'errors' => $errors,
            'values' => $values,
        ]);
    } else {
        header("Location: /");
        exit();
    }
} else {
    if (isset($_SESSION['user'])) {
        header("Location: /");
    } else {
        $page_content = include_template('auth.php', []);
    }
}

$layout_content = include_template('layout.php', [
	'content' => $page_content,
    'title' => 'Дела в порядке',
]);

print($layout_content);
