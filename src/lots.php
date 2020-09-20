<?php
namespace yeticave\lot;

use yeticave\Database;
use function yeticave\user\getId;

// список основных категорий лотов
const CATEGORIES = array(1 => 'Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное');
// список полей лота
const LOT_FIELDS = ['name', 'category', 'pict', 'alt', 'price', 'minPrice', 'timer', 'description'];

/**
 * Список категорий лотов
 * @return array|bool|null
 */
function getCategories()
{
    $res = Database::getInstance()->query('SELECT * FROM categories ORDER BY ID ASC');

    $cats = [];
    foreach ($res as $elem) {
        $cats[ $elem['id'] ] = $elem['name'];
    }
    return !empty($cats) ? $cats : CATEGORIES;
}

/**
 * Получение списка новых лотов для главной страницы с не истекшим сроком публикации
 * @return array
 */
function getNewLots()
{
    $sql = 'SELECT lots.id, lots.name, categories.name AS `category`, lots.price_start AS `price`,
                lots.price_rate, lots.price_step, lots.image_url AS `pict`, lots.description
            FROM `lots`
            LEFT JOIN `categories` ON lots.category_id = categories.id
            WHERE lots.active = 1 AND lots.data_finish > NOW()
            ORDER BY `data_start` DESC
            LIMIT ' . LOTS_ON_INDEX;

    $lots = Database::getInstance()->query($sql);

    return check($lots);
}

/**
 * Получение количества лотов в указанной категории
 * @param int $id
 * @return int
 */
function getLotsCategoryCount(int $id)
{
    $sql = 'SELECT COUNT(*) AS CNT FROM `lots`
            WHERE lots.category_id = ? AND active = 1 and data_finish > NOW()';

    $DB = Database::getInstance();
    $DB->prepareQuery($sql, [$id]);
    $data = $DB->getAssocResult();

    return $data['CNT'] ?? 0;
}

/**
 * Получение списка лотов для указанной категории
 * @param int $id
 * @param int $limit
 * @param int $offset
 * @return array|bool
 */
function getCategoryLots(int $id, $limit = 0, $offset = 0)
{
    $categories = getCategories();
    if(!isset($categories[$id])) {
        return false;
    }

    $sql = 'SELECT lots.id, lots.name, categories.name AS `category`, lots.price_start AS `price`,
                lots.price_rate, lots.price_step, lots.image_url AS `pict`, lots.description
            FROM `lots`
            LEFT JOIN `categories` ON lots.category_id = categories.id
            WHERE lots.active = 1 AND lots.category_id = ? AND lots.data_finish > NOW()
            ORDER BY `data_start` DESC';
    if ($limit) {
        $offset = $offset >= 0 ? $offset : 0;
        $sql .= " LIMIT {$limit} OFFSET {$offset}";
    }

    $DB = Database::getInstance();
    $DB->prepareQuery($sql, [$id]);
    $lots = $DB->getAssocResult(true) ?? [];

    return check($lots);
}

/**
 * Добавление лота в БД
 * @param $data
 * @return bool|int
 */
function addLot($data)
{
    if(empty($data)) {
        return false;
    }
    $fields = [
        'name' => (string) $data['lot-name'],
        'category_id' => (int) $data['category'],
        'user_id' => (int) getId(),
        'image_url' => (string) $data['lot-image'],
        'data_finish' => (string) $data['lot-date'],
        'price_start' => (int) $data['lot-rate'],
        'price_step' => (int) $data['lot-step'],
        'description' => (string) $data['message']
    ];

    $sql = 'INSERT INTO `lots`
    (`name`, `category_id`, `user_id`, `image_url`, `data_start`, `data_finish`, `price_start`, `price_rate`, `price_step`, `description`)
    VALUES (?, ?, ?, ?, NOW(), ?, ?, 0, ?, ?)';


    return Database::getInstance()->prepareQuery($sql, $fields, true);
}

/**
 * Кол-во активных лотов из указанного списка
 * @param array $ids
 * @return int
 */
function getLotsCount(array $ids)
{
    if(empty($ids)) {
        return 0;
    }
    $listId = str_repeat(', ?', count($ids)-1);
    $sql = "SELECT COUNT(*) AS CNT FROM `lots`
            WHERE id in (?{$listId}) AND active = 1 and data_finish > NOW()";

    $DB = Database::getInstance();
    $DB->prepareQuery($sql, $ids);
    $data = $DB->getAssocResult();

    return $data['CNT'] ?? 0;
}

/**
 * Получение списка активных лотов по их id
 * @param array $ids
 * @param int   $limit
 * @param int   $offset
 * @return array
 */
function getLots(array $ids, $limit = 0, $offset = 0)
{
    if(empty($ids)) {
        return [[]];
    }
    $listId = str_repeat(', ?', count($ids)-1);
    $sql = "SELECT lots.id, lots.name, categories.name AS `category`, lots.price_start AS `price`,
                lots.price_rate, lots.price_step, lots.image_url AS `pict`, lots.description
            FROM `lots`
            LEFT JOIN `categories` ON lots.category_id = categories.id
            WHERE lots.id in (?{$listId}) AND lots.active = 1 and lots.data_finish > NOW()";
    if ($limit) {
        $sql .= " LIMIT {$limit} OFFSET {$offset}";
    }

    $DB = Database::getInstance();
    $DB->prepareQuery($sql, $ids);
    $lots = $DB->getAssocResult(true) ?? [];

    return check($lots);
}

/**
 * Полнотекстовый поиск по полям названия и описания лотов
 * @param string $search
 * @param int   $limit
 * @param int   $offset
 * @return array|null
 */
function search(string $search, $limit = 0, $offset = 0)
{
    $sql =  'SELECT lots.id, lots.name, categories.name AS `category`, lots.price_start AS `price`,
                lots.price_rate, lots.price_step, lots.image_url AS `pict`, lots.description
            FROM `lots`
            LEFT JOIN `categories` ON lots.category_id = categories.id
            WHERE lots.active = 1 AND lots.data_finish > NOW() AND
                  MATCH(lots.name, description) AGAINST(? IN BOOLEAN MODE)';
    if ($limit) {
        $sql .= " LIMIT {$limit} OFFSET {$offset}";
    }

    $DB = Database::getInstance();
    $DB->prepareQuery($sql, [$search]);
    $data = $DB->getAssocResult(true) ?? [];

    return check($data);
}

/**
 * Возвращает количество искомых лотов по указанному запросу
 * @param string $search - строка поиска
 * @return int
 */
function searchCount(string $search)
{
    $sql =  'SELECT COUNT(*) AS CNT FROM `lots`
            WHERE lots.active = 1 AND lots.data_finish > NOW() AND
                  MATCH(lots.name, description) AGAINST(? IN BOOLEAN MODE)';

    $DB = Database::getInstance();
    $DB->prepareQuery($sql, [$search]);
    $data = $DB->getAssocResult();

    return $data['CNT'] ?? 0;
}

/**
 * Валидация данных по лотам
 * @todo Добавить изображение лота (pict) по умолчанию, если указанное изображение отсутсвует
 * @param array $data - массив со списком лотов
 * @return array
 */
function check(array $data)
{
    array_walk($data, '\yeticave\lot\checkFields');
    foreach ($data as $key => &$lot) {
        $lot['name'] = html_entity_decode($lot['name']);
        $lot['alt'] = $lot['alt'] ?? $lot['name'];
        $lot['description'] = htmlspecialchars($lot['description']);

        $lot['price'] = (int) $lot['price'];
        $lot['price_rate'] = (int) $lot['price_rate'];
        $lot['price_step'] = (int) $lot['price_step'];

        $minPrice = ($lot['price_rate'] === 0) ? $lot['price'] :
            $lot['price'] + (floor( ($lot['price_rate'] - $lot['price']) / $lot['price_step'] ) + 1) * $lot['price_step'];

        $lot['minPrice'] = $minPrice;
        $lot['priceFormat'] = priceFormat( $lot['price'], true);
        $lot['minPriceFormat'] = priceFormat($minPrice, true);

        $lot['timer'] = getLeftMidnight();
    }
    return $data;
}

/**
 * Дополняет массив с лотом отсутсвующими ключами в соответствии со списком полей ($lotFields)
 * @param $value
 */
function checkFields(&$value)
{
    $diffArray = array_diff_key(array_fill_keys(LOT_FIELDS, ''), $value);
    $value = array_merge($value, $diffArray);
}

/**
 * Добавление ставки и обновление цены лота на значение ставки
 * @param int $lotId
 * @param int $cost
 * @return int|bool
 */
function addBet(int $lotId, int $cost)
{
    $userId = getId();

    $queries = [
      [
          'sql' => 'INSERT INTO bids (`user_id`, `lot_id`, `data_insert`, `sum`) VALUES (?, ?, NOW(), ?)',
          'fields' => [$userId, $lotId, $cost],
          'insert' => 1
      ],
      [
          'sql' => 'UPDATE lots SET price_rate = ? WHERE id = ?',
          'fields' => [$cost, $lotId]
      ],
    ];

    $result = Database::getInstance()->transact($queries);

    //$result[0] - id добавленной записи ставки
    //$result[1] - удалось ли обновить лот
    return ($result[0] && $result[1]) ? $result[0] : false;
}

/**
 * Список ставок по указанному лоту
 * @param int $lotId
 * @return array
 */
function getBets(int $lotId) {

    $sql = 'SELECT b.id, u.name, UNIX_TIMESTAMP(b.data_insert) AS timestamp, b.sum AS price FROM bids b
            JOIN users u ON b.user_id = u.id
            WHERE lot_id = ? ORDER BY ID DESC';

    $DB = Database::getInstance();
    $DB->prepareQuery($sql, [$lotId]);
    $data = $DB->getAssocResult(true) ?? [];

    //форматирование ставок
    foreach ($data as &$bet) {
        $bet['ts'] = timeFormat( $bet['timestamp'] );
    }
    unset($bet);

    return $data;
}

/**
 * Сохранение просмотренного лота в историю
 * @param $id
 */
function addLotHistory($id) {
    $history = getLotHistory();

    $history[] = $id;
    $history = array_unique($history, SORT_NUMERIC);

    setcookie('lot-history', json_encode($history), time() + 7 * 86400);
}

/**
 * Возвращает список просмотренных лотов
 * @return array|mixed
 */
function getLotHistory() {
    $history = $_COOKIE['lot-history'] ?? [];

    return empty($history) ? [] : json_decode($history, false);
}

/**
 * Кол-во лотов у текущего пользователя
 * @return int
 */
function getUserLotCount() {
    $userId = getId();

    $sql = 'SELECT COUNT(*) AS CNT FROM lots WHERE user_id = ?';

    $DB = Database::getInstance();
    $DB->prepareQuery($sql, [$userId]);
    $data = $DB->getAssocResult();

    return $data['CNT'] ?? 0;
}

/**
 * Кол-во ставок у текущего пользователя
 * @return int
 */
function getUserBetCount() {
    $userId = getId();

    $sql = 'SELECT count(DISTINCT lot_id) AS CNT FROM bids WHERE user_id = ?';

    $DB = Database::getInstance();
    $DB->prepareQuery($sql, [$userId]);
    $data = $DB->getAssocResult();

    return $data['CNT'] ?? 0;
};

/**
 * Возвращение списка лотов пользователя
 * @return array
 */
function getUserLots(){
    $userId = getId();

    $sql = 'SELECT l.id, l.name, c.name cat_name, l.image_url, UNIX_TIMESTAMP(l.data_finish) dt_finish,
                l.price_start, l.price_rate, l.price_step, l.winner_id, l.description, MAX(b.sum) max_bet
            FROM lots l
            JOIN categories c ON l.category_id = c.id
            LEFT JOIN bids b ON b.lot_id = l.id
            WHERE l.user_id = ?
            GROUP BY l.id
            ORDER BY l.id DESC';

    $DB = Database::getInstance();
    $DB->prepareQuery($sql, [$userId]);
    $data = $DB->getAssocResult(true) ?? [];

    foreach ($data as &$lot) {
        $lot['tsFinish'] = timeFormat( $lot['dt_finish'] );
        $priceStart = (int) ceil($lot['price_start']);
        $lot['price_start'] = number_format($priceStart, 0, '', ' ');
        $priceStep = (int) ceil($lot['price_step']);
        $lot['price_step'] = number_format($priceStep, 0, '', ' ');
        $maxBet = (int) ceil($lot['max_bet']);
        $lot['max_bet'] = ($maxBet) ? number_format($maxBet, 0, '', ' ') . 'р': ' Нет ставок';

        $lot['status'] = ($lot['winner_id'] === $userId ) ? 'win' : '';
        if(!$lot['status'] && time() > $lot['dt_finish'] ) {
            $lot['status'] = 'end';
        }
    }
    unset($lot);

    return $data;
}

/**
 * Возвращение списка ставок пользователя
 * @return array
 */
function getUserBets() {
    $userId = getId();

    $sql = 'SELECT MAX(b.id) AS id, b.lot_id, MAX(UNIX_TIMESTAMP(b.data_insert)) data_insert, MAX(b.sum) price,
                l.name, l.image_url, l.description, c.name cat_name, l.price_rate, l.winner_id, l.active,
                UNIX_TIMESTAMP(l.data_finish) data_finish
            FROM bids b
            JOIN lots l ON l.id = b.lot_id
            JOIN categories c ON c.id = l.category_id
            WHERE b.user_id = ?
            GROUP BY b.lot_id';

    $DB = Database::getInstance();
    $DB->prepareQuery($sql, [$userId]);
    $res = $DB->getAssocResult(true) ?? [];

    $lots = [];
    $data = [];
    foreach ($res as $bet) {
        $bet['tsInsert'] = timeFormat( $bet['data_insert'] );
        $bet['tsFinish'] = timeFormat( $bet['data_finish'] );
        $priceFormat = (int) ceil($bet['price']);
        $bet['price'] = number_format($priceFormat, 0, '', ' ');

        $bet['status'] = '';
        $lotId = (int) $bet['lot_id'];
        // если лот завершен
        if( time() > $bet['data_finish'] ) {
            $winnerId = (int) $bet['winner_id'];
            // если победитель не указан но была ставка
            if ( !$bet['winner_id'] && $bet['price_rate'] ) {
                    $winnerId = checkLotWinner($lotId);
            }
            //если победитель текущий пользователь то ставка победила, иначе торги окончены
            $bet['status'] = ($winnerId === $userId )? 'win' : 'end';
            $bet['process'] = '';
        }
        // список лотов для определения статуса ставки
        else {
            $lots[] = $lotId;
        }
        // собираем ставки по их id
        $data[ $bet['id']] = $bet;
    }
    unset($res);

    //сортируем список ставок - новые должны показываться первыми
    krsort($data);

    /*  Определение "перебитых" ставок
    * Реализуется отдельным запросом, т.к. при большом кол-ве лотов и ставок,
    * если делать подзапросом, объем данных вырастет на порядки
    */
    if($lots) {
        $listId = str_repeat(', ?', count($lots)-1);
        $sql = "SELECT id, user_id, lot_id FROM bids
            WHERE lot_id IN (?{$listId}) AND
            id IN ( SELECT MAX(id) FROM bids GROUP BY lot_id)";

        $DB = Database::getInstance();
        $DB->prepareQuery($sql, $lots);
        $bids = $DB->getAssocResult(true) ?? [];

        $bidList = [];
        //список ставок по id лоту с пользователем сделавшего последнюю ставку
        foreach ($bids as $bid) {
            $lotId = (int) $bid['lot_id'];
            $bidList[ $lotId ] = [
                'id' => (int) $bid['id'],
                'user_id' => (int) $bid['user_id'],
                'lot_id' => $lotId
            ];
        }

        // перебита ли ставка другим пользователем
        foreach ($data as &$bet) {
            $lotId = (int) $bet['lot_id'];
            if( isset($bidList[ $lotId ])) {
                $bet['process'] = ($bidList[ $lotId ]['user_id'] === $userId);
            }
        }
    }

    return $data;
}

/**
 * Проверка и установка победителя для завершенного лота
 * @param int $lotId
 * @return int
 * @todo На реальном проекте должна быть заменена на функцию автосканирование и установку победителей ставок запускаемую ежедневно кроном
 */
function checkLotWinner(int $lotId) {

    $sql = 'SELECT user_id FROM bids WHERE id = (SELECT MAX(id) FROM bids WHERE lot_id = ?)';

    $DB = Database::getInstance();
    $DB->prepareQuery($sql, [$lotId]);
    $data = $DB->getAssocResult();

    $winUserId = $data['user_id'] ?? NULL;
    if($winUserId) {
        $sql= 'UPDATE lots SET winner_id = ? WHERE id = ?';

        // проверяем что обновление удалось
        if ( !$DB->prepareQuery($sql, [$winUserId, $lotId]) ) {
            return false;
        }
    }

    return $winUserId;
}
