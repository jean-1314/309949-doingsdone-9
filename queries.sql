INSERT INTO users (email, password, name, created_at)
VALUES ('ivan@example.com', '_very10ngPassw0rd_', 'Ivan', NOW()),
('randomuser@example.com', '12345', 'RandUsr', NOW());

INSERT INTO projects (title, author_id, created_at)
VALUES ('Входящие', 1, NOW()), ('Учеба', 2, NOW()), ('Работа', 2, NOW()), ('Домашние дела', 2, NOW()), ('Авто', 1, NOW());

INSERT INTO tasks (title, status, deadline, project_id)
VALUES ('Собеседование в IT компании', 0, '2019-05-08 00:00:00', 3),
('Выполнить тестовое задание', 0, '2019-04-25 00:00:00', 3),
('Сделать задание первого раздела', 1, '2019-04-21 00:00:00', 2),
('Встреча с другом', 0, '2019-04-19 00:00:00', 1),
('Купить корм для кота', 0, '2019-04-26 00:00:00', 1),
('Заказать пиццу', 0, '2019-04-25 00:00:00', 4),
('Сменить резину', 0, '2019-04-26 00:00:00', 5);

-- получаем список проектов для 1 пользователя, привязываем счетчик задач
SELECT p.id, p.title, p.created_at,
(
  SELECT COUNT(*)
  FROM tasks t
  WHERE t.project_id = p.id
) AS tasks_count
FROM projects p
WHERE p.author_id = 1;

-- Получаем список задач одного проекта
SELECT t.id, t.title, t.created_at, t.status, t.file_name, t.deadline, p.title FROM tasks t
INNER JOIN projects p ON t.project_id = p.id WHERE p.id = 1;

-- Переводим задачу в статус выполненной
UPDATE tasks SET status = 1 WHERE id = 1;

-- Обновляем название задачи
UPDATE tasks SET title = 'Новое название задачи' WHERE id = 1;
