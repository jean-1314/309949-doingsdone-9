<?php
require_once 'vendor/autoload.php';
require_once 'init.php';
require_once 'helpers.php';
require_once 'functions.php';

$transport = new Swift_SmtpTransport('phpdemo.ru', 25);
$transport->setUsername('keks@phpdemo.ru');
$transport->setPassword('htmlacademy');

$mailer = new Swift_Mailer($transport);
$logger = new Swift_Plugins_Loggers_ArrayLogger();
$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

$users = getAllUsers($connection);

if (!empty($users)) {
    foreach ($users as $user) {
        $tasks = getUserTasksForEmail($connection, $user['id']);

        if (!empty($tasks)) {
            $recipient = [$user['email'] => $user['name']];
            $message = new Swift_Message();
            $message->setSubject("Уведомление от сервиса «Дела в порядке»");
            $message->setFrom(['keks@phpdemo.ru' => 'Дела в порядке']);
            $message->setTo($recipient);

            if (count($tasks) === 1) {
                $msg_content = 'Уважаемый(ая) ' . $user['name'] . '! У вас запланирована задача «' . $tasks[0]['title'] . '» на ' . $tasks[0]['deadline'];
                $message->setBody($msg_content, 'text/plain');
            } elseif (count($tasks) > 1) {
                $msg_content = include_template('notify.php', ['tasks' => $tasks, 'user' => $user]);
                $message->setBody($msg_content, 'text/html');
            }

            $result = $mailer->send($message);

            if ($result) {
                header('Location: index.php');
            } else {
                print("Не удалось отправить рассылку: " . $logger->dump());
            }
        }
    }
}
