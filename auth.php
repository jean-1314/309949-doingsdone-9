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
    $wrongEmailOrPassword = false;
    $wrongEmailOrPasswordText = 'Вы ввели неверный email/пароль';

    foreach ($required as $field) {
        if (empty($form[$field])) {
            $errors[$field] = 'Это поле надо заполнить';
        }
    }

    $email = mysqli_real_escape_string($connection, $form['email']);
    $result = getUserByEmail($email, $connection);
    $user = $result[0] ?? null;

    if (count($errors) == 0 && $user) {
        if (password_verify($form['password'], $user['password'])) {
            $_SESSION['user'] = $user;
        } else {
            $wrongEmailOrPassword = true;
        }
    } else {
        $wrongEmailOrPassword = true;
    }

    $values = $form;

    if (count($errors)) {
        $page_content = include_template('auth.php', [
            'errors' => $errors,
            'values' => $values,
        ]);
    } elseif ($wrongEmailOrPassword) {
        $page_content = include_template('auth.php', [
            'wrongEmailOrPassword' => $wrongEmailOrPassword,
            'wrongEmailOrPasswordText' => $wrongEmailOrPasswordText,
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
