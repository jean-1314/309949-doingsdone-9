<?php
require_once('helpers.php');

$show_complete_tasks = rand(0, 1);
$userName = 'Иван';
$projects = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$tasks = [
    [
        'id' => 100,
        'title' => 'Собеседование в IT компании',
        'deadline' => '01.12.2018',
        'is_completed' => false,
        'category' => 'Работа'
    ],
    [
        'id' => 101,
        'title' => 'Выполнить тестовое задание',
        'deadline' => '25.12.2018',
        'is_completed' => false,
        'category' => 'Работа'
    ],
    [
        'id' => 102,
        'title' => 'Сделать задание первого раздела',
        'deadline' => '21.12.2018',
        'is_completed' => true,
        'category' => 'Учеба'
    ],
    [
        'id' => 103,
        'title' => 'Встреча с другом',
        'deadline' => '22.12.2018',
        'is_completed' => false,
        'category' => 'Входящие'
    ],
    [
        'id' => 104,
        'title' => 'Купить корм для кота',
        'deadline' => '',
        'is_completed' => false,
        'category' => 'Домашние дела'
    ],
    [
        'id' => 105,
        'title' => 'Заказать пиццу',
        'deadline' => '',
        'is_completed' => false,
        'category' => 'Домашние дела'
    ],
];

/**
 * calculateTasks
 *
 * @param  array $tasksList
 * @param  string $projectTitle
 *
 * @return int
 */
function calculateTasks(array $tasksList, string $projectTitle): int
{
    $counter = 0;
    foreach($tasksList as $key => $task) {
        if ($task['category'] == $projectTitle) {
            $counter++;
        }
    }

    return $counter;
};

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
