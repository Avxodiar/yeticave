<?php
namespace yeticave\database;

/**
 * Установка соединения с БД
 */
try{
    $mysqli = new \mysqli(DB_CONFIG['host'], DB_CONFIG['user'], DB_CONFIG['password'], DB_CONFIG['database']);
    $mysqli->set_charset('utf8');
} catch (\Exception $e) {
    errorLog("Ошибка подключения к БД ({$e->getCode()}) {$e->getMessage()}");
    errorPage(500);
}

/**
 * Подготовка запроса
 * @param string $sql
 * @return \mysqli_stmt
 */
function prepareStmt(string $sql) {
    global $mysqli;
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        error('Не удалось подготовить запрос:');
    }
    return $stmt;
}

/**
 * Выполнение подготовленного запроса по привязанным параметрам
 * @param \mysqli_stmt $stmt
 * @param              $params
 * @param bool         $insert
 * @return int|string
 */
function executeStmt(\mysqli_stmt $stmt, $params, $insert = false) {
    global $mysqli;

    $keys = '';
    $vars = [];
    $types = [
        'integer' => 'i',
        'string' => 's',
        'double' => 'd'
    ];

    foreach ($params as $key => $param) {
        $type = $types[ gettype($param) ] ?? null;
        if($type) {
            $keys .= $type;
            $vars[] = ($key === 'password') ? $param : mysqli_real_escape_string($mysqli, $param);
        }
    }

    if (!$stmt->bind_param($keys, ...$vars)) {
        error('Не удалось привязать параметры:');
        return false;
    }

    $res = $stmt->execute();
    if (!$res) {
        error('Не удалось выполнить запрос:');
        return false;
    }

    return ($insert) ? mysqli_insert_id($mysqli) : $res;
}

function transact(array $queries) {
    global $mysqli;

    mysqli_begin_transaction($mysqli);

    $results = [];
    foreach ($queries as $id => $query) {
        $sql = $query['sql'];
        $fields = $query['fields'];
        $insert = (bool) ($query['insert'] ?? 0);

        $stmt = prepareStmt($sql);
        $res = executeStmt($stmt, $fields, $insert);
        if($res) {
            $results[$id] = $res;
        } else {
            mysqli_rollback($mysqli);
            return false;
        }
    }

    mysqli_commit($mysqli);

    return $results;
}

/**
 * Получение результата выполнения подготовленного запроса
 * @param \mysqli_stmt $stmt
 * @param bool         $all - все результаты
 * @return array|mixed|null
 */
function getAssocResult(\mysqli_stmt $stmt, $all = false){
    $res = $stmt->get_result();

    return ($all) ? $res->fetch_all(MYSQLI_ASSOC) : $res->fetch_assoc();
}

/**
 * Получение данных из базы
 * только для безопасных запросов!
 * @param string $sql
 * @return array|bool|null
 */
function query(string $sql) {
    if(empty($sql)) {
        return false;
    }

    global $mysqli;
    $result = mysqli_query($mysqli, $sql);
    if ($result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    $error = mysqli_error($mysqli);
    error('Ошибка выполнения запроса:' . $error);
    return false;
}

/**
 * Обработка ошибок
 * @param $text
 */
function error($text) {
    global $mysqli;
    die("{$text} ({$mysqli->errno}) {$mysqli->error}");
}
