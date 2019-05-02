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
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
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
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
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
function db_fetch_data($link, $sql, $data = []): array {
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
 * Запрос на получение всех проектов пользователя
 *
 * @param  int connection
 * @param  $connection mysqli Ресурс соединения
 *
 * @return array
 */
function getProjectsByUser($userId, $connection): array {
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
function getProjectIds($userId, $connection): array {
    $sql = 'SELECT id FROM projects WHERE author_id = ' . $userId;
    return array_column(db_fetch_data($connection, $sql), 'id');
}


/**
 * Запрос на получение задач из полученных проектов
 * Если параметр в урле есть, отрисовываем все задачи внутри проекта
 * Если параметр есть, но его нет в массиве айдишников $projectIds, стреляем 404. Чтобы нельзя было увидеть проекты другого пользователя.
 * Если параметра нет, отрисовываем все задачи пользователя по всем проектам
 * @param  array $projectIds
 * @param  $connection mysqli Ресурс соединения
 *
 * @return array
 */
function getTasksByProjectId($projectIds, $connection): array {
    $selectTaskSql = 'SELECT t.id, t.title, t.created_at, t.status, t.file_name, t.deadline, p.title AS project_title '
    . 'FROM tasks t '
    . 'INNER JOIN projects p '
    . 'ON t.project_id = p.id ';

    if (isset($_GET['id']) && in_array($_GET['id'], $projectIds)) {
        $projectId = mysqli_real_escape_string($connection, $_GET['id']);
        $sql = $selectTaskSql
        . 'WHERE p.id = "%s"';
        $sql = sprintf($sql, $projectId);
    } else if (isset($_GET['id']) && !in_array($_GET['id'], $projectIds)) {
        http_response_code(404);
    } else {
        $sql = $selectTaskSql
        . 'WHERE p.author_id = ' . 1;
    }

    return db_fetch_data($connection, $sql);
}
