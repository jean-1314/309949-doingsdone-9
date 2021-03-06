<?php
/**
 * calculateTimeToDeadline
 *
 * @param  string $tsString
 *
 * @return string
 */
function calculateTimeToDeadline(string $tsString): string
{
    $currentTimeStamp = time();
    $taskTs = strtotime($tsString);
    return $taskTs - $currentTimeStamp;
}

/**
 * isDeadlineClose
 *
 * @param  string $taskDatetime
 *
 * @return boolean
 */
function isDeadlineClose(string $taskDatetime): bool
{
    $hoursInDay = 3600;
    $hoursToDeadline = 24;
    return floor(calculateTimeToDeadline($taskDatetime) / $hoursInDay) < $hoursToDeadline;
}

/**
 * isDateValid
 * Проверяем дату, если сегодня или позднее, отдаем true
 * @param  string $taskDatetime
 *
 * @return boolean
*/
function isDateValid(string $date): bool
{
    $currentTimeStamp = strtotime(date('Y-m-d'));
    $taskTimeStamp = strtotime($date);
    $diff = $currentTimeStamp - $taskTimeStamp;
    return $diff <= 0;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = [])
{
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            } elseif (is_string($value)) {
                $type = 's';
            } elseif (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Функция получения записей с помощью подготовленного выражения
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return $result array
 */
function db_fetch_data($link, $sql, $data = []): array
{
    $result = [];
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res) {
        $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }
    return $result;
}

/**
 * Функция добавления записей с помощью подготовленного выражения
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return $result integer
 */
function db_insert_data($link, $sql, $data = []): int
{
    $stmt = db_get_prepare_stmt($link, $sql, $data);
    $result = mysqli_stmt_execute($stmt);
    if ($result) {
        $result = mysqli_insert_id($link);
    }
    return $result;
}

/**
 * Запрос на получение всех проектов пользователя
 *
 * @param  int connection
 * @param  $connection mysqli Ресурс соединения
 *
 * @return array
 */
function getProjectsByUser($userId, $connection): array
{
    $sql = 'SELECT p.id, p.title, p.created_at,
        (
            SELECT COUNT(*)
            FROM tasks t
            WHERE t.project_id = p.id
        ) AS tasks_count
        FROM projects p
        WHERE p.author_id = ' . $userId;

    return db_fetch_data($connection, $sql);
}

/**
 * Вытаскиваем массив айдишников проектов пользователя,
 * Составляем одномерный массив айдишников из двумерного
 * @param  int $userId
 * @param  $connection mysqli Ресурс соединения
 *
 * @return array
 */
function getProjectIds($userId, $connection): array
{
    $sql = 'SELECT id FROM projects WHERE author_id = ' . $userId;
    return array_column(db_fetch_data($connection, $sql), 'id');
}

/**
 * Вытаскиваем массив названий проектов пользователя для проверки на совпадения,
 * Составляем одномерный массив названий из двумерного
 * @param  int $userId
 * @param  $connection mysqli Ресурс соединения
 *
 * @return array
 */
function getProjectTitles($userId, $connection): array
{
    $sql = 'SELECT title FROM projects WHERE author_id = ' . $userId;
    return array_column(db_fetch_data($connection, $sql), 'title');
}

/**
 * Запрос на получение задач из проекта
 * @param  int $queryId
 * @param  $connection mysqli Ресурс соединения
 *
 * @return array
 */
function getTasksByProjectId($queryId, $connection): array
{
    $sql = 'SELECT id, title, created_at, status, file_name, deadline '
    . 'FROM tasks '
    . 'WHERE project_id = "%s"'
    . 'ORDER BY created_at DESC';

    $projectId = mysqli_real_escape_string($connection, $queryId);
    $sql = sprintf($sql, $projectId);

    return db_fetch_data($connection, $sql);
}

/**
 * Запрос на получение задач всех проектов одного пользователя
 * @param  array $projectIds
 * @param  $connection mysqli Ресурс соединения
 *
 * @return array
 */
function getTasksByUserProjects($projectIds, $connection): array
{
    $sql = 'SELECT id, title, created_at, status, file_name, deadline '
    . 'FROM tasks WHERE project_id IN (' . implode(',', $projectIds) . ')'
    . 'ORDER BY created_at DESC';

    return db_fetch_data($connection, $sql);
}

/**
 * Запрос на получение данных пользователя по его email
 *
 * @param  string $email
 * @param  $connection mysqli Ресурс соединения
 *
 * @return array
 */
function getUserByEmail($email, $connection): array
{
    $sql = "SELECT * FROM users WHERE email ='$email'";
    return db_fetch_data($connection, $sql);
}

/**
 * Вспомогательная функция получения фильтра по дате для строки запроса
 *
 * @param  string $filter
 *
 * @return string
 */
function createFilterQuery($filter): string
{
    switch ($filter) {
        case 'today':
            return 'DATE(deadline) = CURDATE()';
        case 'tomorrow':
            return 'DATE(deadline) = CURDATE() + INTERVAL 1 DAY';
        case 'expired':
            return 'DATE(deadline) < CURDATE() OR status = 1';
        default:
            return '';
    }
}

/**
 * Запрос на получение отфильтрованных задач всех проектов одного пользователя
 * @param  array $projectIds
 * @param  string $filter
 * @param  $connection mysqli Ресурс соединения
 *
 * @return array
 */
function getTasksByFilter($projectIds, $filter, $connection): array
{
    $sql = 'SELECT id, title, created_at, status, file_name, deadline '
    . 'FROM tasks '
    . 'WHERE project_id IN (' . implode(',', $projectIds) . ')' . ' AND ' . createFilterQuery($filter)
    . ' ORDER BY created_at DESC';

    return db_fetch_data($connection, $sql);
}

/**
 * Запрос на получение отфильтрованных задач всех проектов одного пользователя
 * @param  int $projectId
 * @param  string $filter
 * @param  $connection mysqli Ресурс соединения
 *
 * @return array
 */
function getTasksByFilterAndId($projectId, $filter, $connection): array
{
    $sql = 'SELECT id, title, created_at, status, file_name, deadline '
    . 'FROM tasks '
    . 'WHERE project_id = ' . $projectId . ' AND ' . createFilterQuery($filter)
    . ' ORDER BY created_at DESC';

    return db_fetch_data($connection, $sql);
}

/**
 * Запрос на получение истекающих сегодня задач
 * @param  array $projectIds
 * @param  string $filter
 * @param  $connection mysqli Ресурс соединения
 *
 * @return array
 */
function getUserTasksForEmail($connection, $userId): array
{
    $sql = 'SELECT t.title, t.status, DATE_FORMAT(t.deadline, "%d.%m.%Y") AS deadline FROM tasks t'
    . ' JOIN projects p ON p.id = t.project_id'
    . ' WHERE DATE(t.deadline) = CURDATE() AND t.status = 0'
    . ' AND p.author_id = ' . $userId;

    return db_fetch_data($connection, $sql);
}

/**
 * Запрос на получение всех пользователей
 *
 * @param  $connection mysqli Ресурс соединения
 *
 * @return array
 */
function getAllUsers($connection): array
{
    $sql = "SELECT id, name, email FROM users";
    return db_fetch_data($connection, $sql);
}
