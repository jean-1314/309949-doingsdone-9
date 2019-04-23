INSERT INTO users (email, password, name, created_at)
VALUES ('ivan@example.com', '_very10ngPassw0rd_', 'Ivan', NOW()),
('randomuser@example.com', '12345', 'RandUsr', NOW());

INSERT INTO projects (title, author_id, created_at)
VALUES ('Входящие', 1, NOW()), ('Учеба', 2, NOW()), ('Работа', 2, NOW()), ('Домашние дела', 2, NOW()), ('Авто', 1, NOW());

INSERT INTO tasks (title, status, deadline, project_id)
VALUES ('Собеседование в IT компании', 0, 15572736, 3),
('Выполнить тестовое задание', 0, 15561504, 3),
('Сделать задание первого раздела', 1, 15558048, 2),
('Встреча с другом', 0, 15564096, 1),
('Купить корм для кота', 0, '', 4),
('Заказать пиццу', 0, '', 4);
