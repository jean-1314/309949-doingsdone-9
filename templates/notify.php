<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>

<p><?= 'Уважаемый(ая) ' . $user['name'] . '! У вас запланированы следующие задачи: ' ?></p>
<ul>
    <?php foreach ($tasks as $key => $task) { ?>
        <li><?= $task['title'] . ' на ' . $task['deadline'] ?></li>
    <?php } ?>
</ul>

</body>
</html>
