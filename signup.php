<?php
require_once('init.php');
require_once('helpers.php');
require_once('functions.php');

$userName = 'Иван';
$errors = [];
$values = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = $_POST;
    $req_fields = ['email', 'password', 'name'];

    foreach ($req_fields as $field) {
        if (empty($form[$field])) {
            $errors[$field] = "Заполните поле";
        }
    }

    if (empty($errors)) {
        $email = mysqli_real_escape_string($connection, $form['email']);
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = '$email'";
        $res = mysqli_query($connection, $sql);
        $emailQuantity = mysqli_fetch_row($res)[0];

        if ($emailQuantity > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Введенный e-mail не является валидным.';
        } else {
            $password = password_hash($form['password'], PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (created_at, email, name, password) VALUES (NOW(), ?, ?, ?)';
            $result = db_insert_data($connection, $sql, [htmlspecialchars($form['email']), htmlspecialchars($form['name']), $password]);

            if ($result && empty($errors)) {
                header('Location: /auth.php');
            }
        }
    }
    $values = $form;
}

$page_content = include_template('signup.php', [
    'errors' => $errors,
    'values' => $values,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'Дела в порядке | Регистрация',
    'userName' => $userName,
]);

print($layout_content);
