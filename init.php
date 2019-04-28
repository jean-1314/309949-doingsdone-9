<?php
$db = [
    'host' => 'localhost',
    'user' => 'root',
    'password' => '',
    'database' => 'doingsdone',
];

$connection = mysqli_connect($db['host'], $db['user'], $db['password'], $db['database']);
mysqli_set_charset($connection, 'utf8');

$projects = [];
$tasks = [];
