<?php
namespace yeticave\lot;

use function yeticave\database\query;
use function yeticave\database\prepareStmt;
use function yeticave\database\executeStmt;
use function yeticave\database\getAssocResult;
use function yeticave\database\transact;
use function yeticave\user\getId;

// список основных категорий лотов
const CATEGORIES = array(1 => 'Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное');
// список полей лота
const LOT_FIELDS = ['name', 'category', 'pict', 'alt', 'price', 'minPrice', 'timer', 'description'];

/**
 * Список категорий лотов
 * @return array|bool|null
 */
function getCategories() {
    $sql = 'SELECT * FROM categories ORDER BY ID ASC';
    $res = query($sql);

    $cats = [];
    foreach ($res as $elem) {
        $cats[ $elem['id'] ] = $elem['name'];
    }
    return !empty($cats) ? $cats : CATEGORIES;
}

/**
 * Получение списка новых лотов для главной страницы с не истекшим сроком публикации
 * @param int $count
 * @return array
 */
function getNewLots($count = 9) {
    $sql = 'SELECT lots.id, lots.name, categories.name AS `category`, lots.price_start AS `price`,
                lots.price_rate, lots.price_step, lots.image_url AS `pict`, lots.description
            FROM `lots`
            LEFT JOIN `categories` ON lots.category_id = categories.id
            WHERE lots.active = 1 AND lots.data_finish > NOW()
            ORDER BY `data_start` DESC
            LIMIT ' . $count;
    $lots = query($sql);

    return check($lots);
}

/**
 * Получение списка лотов для указанной категории
 * @param int $id
 * @return array|bool
 */
function getCategoryLots(int $id) {

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

    $stmt = prepareStmt($sql);
    executeStmt($stmt, [$id]);
    $lots = getAssocResult($stmt, true) ?? [];

    return check($lots);
}

/**
 * Добавление лота в БД
 * @param $data
 * @return bool|int
 */
function addLot($data) {
    if(empty($data)) {
        return false;
    }
    $fields = [
        'name' => (string)$data['lot-name'],
        'category_id' => (int)$data['category'],
        'user_id' => (int)getId(),
        'image_url' => (string)$data['lot-image'],
        'data_finish' => (string)$data['lot-date'],
        'price_start' => (int)$data['lot-rate'],
        'price_step' => (int)$data['lot-step'],
        'description' => (string)$data['message']
    ];

    $sql = 'INSERT INTO `lots`
    (`name`, `category_id`, `user_id`, `image_url`, `data_start`, `data_finish`, `price_start`, `price_rate`, `price_step`, `description`)
    VALUES (?, ?, ?, ?, NOW(), ?, ?, 0, ?, ?)';
    $stmt = prepareStmt($sql);

    return executeStmt($stmt, $fields, true);
}

/**
 * Получение списка лотов по их id
 * @param array $ids
 * @return array
 */
function getLots(array $ids) {
    if(empty($ids)) {
        return [[]];
    }
    $listId = str_repeat(', ?', count($ids)-1);
    $sql = "SELECT lots.id, lots.name, categories.name AS `category`, lots.price_start AS `price`,
                lots.price_rate, lots.price_step, lots.image_url AS `pict`, lots.description
            FROM `lots`
            LEFT JOIN `categories` ON lots.category_id = categories.id
            WHERE lots.id in (?{$listId}) AND lots.active = 1 and lots.data_finish > NOW()";

    $stmt = prepareStmt($sql);
    executeStmt($stmt, $ids);

    $lots = getAssocResult($stmt, true) ?? [];

    return check($lots);
}

/**
 * Полнотекстовый поиск по полям названия и описания лотов
 * @param string $search
 * @return array|null
 */
function search(string $search) {
    //searchIndex();
    $sql =  'SELECT lots.id, lots.name, categories.name AS `category`, lots.price_start AS `price`,
                lots.price_rate, lots.price_step, lots.image_url AS `pict`, lots.description
            FROM `lots`
            LEFT JOIN `categories` ON lots.category_id = categories.id
            WHERE lots.active = 1 AND lots.data_finish > NOW() AND
                  MATCH(lots.name, description) AGAINST(?)';
    $stmt = prepareStmt($sql);
    executeStmt($stmt, [$search]);

    $data = getAssocResult($stmt, true) ?? [];

    return check($data);
}

/**
 * Валидация данных по лотам
 * @todo Добавить изображение лота (pict) по умолчанию, если указанное изображение отсутсвует
 * @param array $data - массив со списком лотов
 * @return array
 */
function check(array $data) {
    array_walk($data, '\yeticave\lot\checkFields');
    foreach ($data as $key => &$lot) {
        $lot['name'] = html_entity_decode($lot['name']);
        $lot['alt'] = $lot['alt'] ?? $lot['name'];
        $lot['description'] = htmlspecialchars($lot['description']);

        $lot['price'] = (int) $lot['price'];
        $lot['price_rate'] = (int) $lot['price_rate'];
        $lot['price_step'] = (int) $lot['price_step'];

        $minPrice = ($lot['price_rate'] == 0) ? $lot['price'] :
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
function checkFields(&$value) {
    global $lotFields;
    $diffArray = array_diff_key(array_fill_keys(LOT_FIELDS, ''), $value);
    $value = array_merge($value, $diffArray);
}

/**
 * Добавление ставки и обновление цены лота на значение ставки
 * @param int $lotId
 * @param int $cost
 * @return int|bool
 */
function addBet(int $lotId, int $cost) {
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

    $result = transact($queries);

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

    $sql = 'SELECT b.id, u.name,UNIX_TIMESTAMP(b.data_insert) AS timestamp, b.sum AS price FROM bids b
            JOIN users u ON b.user_id = u.id
            WHERE lot_id = ? ORDER BY ID DESC';

    $stmt = prepareStmt($sql);
    executeStmt($stmt, [$lotId]);

    $data = getAssocResult($stmt, true) ?? [];

    //форматирование ставок
    foreach ($data as &$bet) {
        $ts = $bet['timestamp'];
        $timeDiff = time() - $ts;
        if ($timeDiff < 7000) {
            $timeDiff = floor($timeDiff / 60);
            $ts = ($timeDiff > 60) ? 'Час назад' : $timeDiff . ' минут назад';
        } else {
            $ts = gmdate('y.m.d в H:i', $ts);
        }
        $bet['ts'] = $ts;
    }
    unset($bet);

    return $data;
}

/**
 * Форматирование суммы лота в соответсвии со спецификацией:
 * {
 *   Функция принимает один аргумент - целое число.
 *   Функция возвращает результат - отформатированную сумму вместе со знаком рубля.
 *   Как должна работать функция:
 *   1. Округлить число до целого использую функцию ceil()
 *   2. Если переданное число меньше 1000, то оставить как есть
 *   3. Если число больше 1000, то отделить пробелом 3 последних цифры от остальной части суммы
 *   Пример: заменить 54999 на 54 999
 *   4. Добавить к получившейся строке пробел и знак рубля.
 * }
 * @todo Уточнить TЗ - округление целого числа; не указаны действия если число ровно 1000 и хранение элементов вёрстки!
 *
 * @param  int    $price - стоимость лота, целое число
 * @param  bool   $rub   - сверстанный или текстовый символ рубля
 * @return string
 */
function priceFormat($price, $rub = true) {
    $priceFormat = (int) ceil($price);
    if($priceFormat > 1000) {
        $priceFormat = number_format($priceFormat, 0, '', ' ');
    }
    $priceFormat .= ($rub) ? ' <b class="rub">р</b>' : ' р';

    return $priceFormat;
}

/**
 * Сколько осталось времени до начала новых суток
 * @return string - формат вывода "ЧЧ:МM"
 */
function getLeftMidnight() {
    $midnight = mktime(0, 0, 0, date('n'), date('j') + 1, date('Y'));
    $left = $midnight - time();

    return gmdate('H:i', $left);
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
