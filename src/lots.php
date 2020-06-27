<?php
namespace yeticave\lot;

use function yeticave\db\query;
use function yeticave\db\prepareStmt;
use function yeticave\db\executeStmt;
use function yeticave\db\getAssocResult;

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
                lots.price_step, lots.image_url as `pict`, lots.description
            FROM `lots`
            LEFT JOIN `categories` ON lots.category_id = categories.id
            WHERE lots.active = 1 and lots.data_finish > NOW()
            ORDER BY `data_start` DESC
            LIMIT ' . $count;
    $lots = query($sql);

    return check($lots);
}

function getLot(int $id) {
    $sql = 'SELECT lots.id, lots.name, categories.name AS `category`, lots.price_start AS `price`,
                lots.price_step, lots.image_url as `pict`, lots.description
            FROM `lots`
            LEFT JOIN `categories` ON lots.category_id = categories.id
            WHERE lots.id = ? and lots.active = 1 and lots.data_finish > NOW()';
    $stmt = prepareStmt($sql);
    executeStmt($stmt, [$id]);

    return getAssocResult($stmt) ?? [];
}

/**
 * Валидация данных по лотам
 * @todo Добавить изображение лота по умолчанию, если указанное изображение отсутсвует
 * @param array $data - массив со списком лотов
 * @return array
 */
function check(array $data) {
    array_walk($data, '\yeticave\lot\checkFields');
    foreach ($data as $key => &$lot) {
        array_walk($lot, '\yeticave\lot\lotValidator');
        $data[$key]['alt'] = $data[$key]['alt'] ?? $data[$key]['name'];
        $data[$key]['timer'] = getLeftMidnight();
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
 * Валидация полей данных у лота
 * @param        $value - значение
 * @param string $param - ключ/поле валидации
 * @param bool   $rub   - флаг ф-ции priceFormat
 */
function lotValidator(&$value, $param = '', $rub = true) {
    switch ($param) {
        case 'price':
            $value = priceFormat($value, $rub);
            break;
        case 'minPrice':
            $value = priceFormat($value, false);
            break;
        case 'pict':
            $url = pathinfo($value);
            $value = file_exists(IMG_PATH . $url['basename']) ? $value : '';
            break;
        case 'ts':
            $timeDiff = time() - $value;
            if($timeDiff < 3900) {
                $timeDiff = floor($timeDiff / 60);
                $value = ($timeDiff > 60) ? 'Час назад' : $timeDiff . ' минут назад';
            } else {
                $value = gmdate('y.m.d в H:i', $value);
            }
            break;
        default:
            $value = htmlspecialchars($value);
    }
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

    $history = empty($history) ? [] : json_decode($history, false);
    return $history;
}
