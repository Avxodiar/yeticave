<?php
namespace yeticave\db;

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
    }

    if (!$stmt->execute()) {
        error('Не удалось выполнить запрос:');
    }
    elseif ($insert) {
        return mysqli_insert_id($mysqli);
    }
}

/**
 * Получение результата выполнения подготовленного запроса
 * @param \mysqli_stmt $stmt
 * @return array|null
 */
function getAssocResult(\mysqli_stmt $stmt){
    $res = $stmt->get_result();
    return $res->fetch_assoc();
}

/**
 * Добавление записи в базу
 * @deprecated
 * @param $query
 * @return bool|int|string
 */
function insert($query) {
    global $mysqli;
    $res = mysqli_query($mysqli, $query);
    if($res) {
        return mysqli_insert_id($mysqli);
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
