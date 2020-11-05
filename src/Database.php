<?php
namespace yeticave;

use mysqli, Exception;

class Database
{
    use SingletonTrait;

    // ресурс соединения с бд
    private $dbResource;

    // подготовленный запрос
    private $stmt;

    private function __construct()
    {
        // Установка соединения с БД
        try {
            $this->dbResource = new mysqli(DB_CONFIG['host'], DB_CONFIG['user'], DB_CONFIG['password'], DB_CONFIG['database']);
            $this->dbResource->set_charset('utf8');
        } catch (Exception $e) {
            $this->error('Ошибка подключения к БД.');
        }
    }

    /**
     * Выполнение подготовленного запроса по привязанным параметрам
     * @param string $sql
     * @param        $params
     * @param bool   $insert
     * @return mixed
     */
    public function prepareQuery(string $sql, $params, $insert = false)
    {
        $this->stmt = $this->dbResource->prepare($sql);
        if (!$this->stmt) {
            $this->error("Не удалось подготовить запрос: ( {$sql} )");
        }

        // формируем ключи и параметры для привязки их к подготавливаемому запросу
        $keys = '';
        $vars = [];
        $types = [
            'integer' => 'i',
            'string' => 's',
            'double' => 'd'
        ];
        foreach ($params as $key => $param) {
            $type = $types[gettype($param)] ?? null;
            if ($type) {
                $keys .= $type;
                $vars[] = ($key === 'password') ? $param : $this->dbResource->real_escape_string($param);
            }
        }

        if (!$this->stmt->bind_param($keys, ...$vars)) {
            $this->error('Не удалось привязать параметры.');
        }

        $res = $this->stmt->execute();
        if (!$res) {
            $this->error('Не удалось выполнить подготовленный запрос.');
        }

        return ($insert) ? $this->dbResource->insert_id : $res;
    }

    /**
     * Выполнение нескольких запросов с транзакцией
     * @param array $queries
     * @return array|bool
     */
    public function transact(array $queries)
    {
        $this->dbResource->begin_transaction();

        $results = [];
        foreach ($queries as $id => $query) {
            $sql = $query['sql'];
            $fields = $query['fields'];
            $insert = (bool) ($query['insert'] ?? 0);

            $res = $this->prepareQuery($sql, $fields, $insert);
            if ($res) {
                $results[$id] = $res;
            } else {
                $this->dbResource->rollback();
                $this->error("Откат транзакции при выполнении запроса ( {$sql} ).");
                $this->stmt = null;

                return false;
            }
        }

        $this->dbResource->commit();
        $this->stmt = null;

        return $results;
    }

    /**
     * Получение результата выполнения подготовленного запроса
     * @param bool $all - возвращать все результаты
     * @return array|mixed
     */
    public function getAssocResult($all = false)
    {
        if ($this->stmt === null) {
            $this->error('Попытка выполнения неподготовленного запроса');
        }

        $res = $this->stmt->get_result();
        if (!$res) {
            $this->error('Ошибка получения результата подготовленного запроса.');
        }

        return ($all) ? $res->fetch_all(MYSQLI_ASSOC) : $res->fetch_assoc();
    }

    /**
     * Получение данных из базы
     * Важно: только для безопасных запросов!
     * @param string $sql
     * @return array|bool|null
     */
    public function query(string $sql)
    {
        if (empty($sql)) {
            return false;
        }

        $result = $this->dbResource->query($sql);
        if (!$result) {
            $this->error("Ошибка выполнения запроса ( {$sql} )");
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Выполнение подготовленного запроса и получение результата (сахар)
     * @param string $sql - запрос
     * @param array  $data - массив парметров подготовленного запроса
     * @param bool $all - возвращать все результаты
     * @return array|mixed
     */
    public function dbQueryAssoc(string $sql, array $data, bool $all = false)
    {
        $this->prepareQuery($sql, $data);

        return $this->getAssocResult($all);
    }

    /**
     * Обработка ошибок
     * @param $text
     */
    private function error($text): void
    {
        errorLog("{$text} ({$this->dbResource->errno} : {$this->dbResource->error} )");
        errorPage(500);
    }

    public function __destruct()
    {
        $this->dbResource->close();
    }
}
