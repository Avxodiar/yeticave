<?php

use function user\isAuth;

require_once 'data/data.php';
require_once 'data/userdata.php';
require_once 'src/user.php';

/**
 * Подключение основного шаблона
 * @param $title
 * @param $content
 */
function includeTemplate($title, $content) {
    global $User, $categories, $logoLink;

    print(
        getTemplate(
        'layout.php', [
            'pageTitle' => $title,
            'logoLink' => $logoLink,
            'isAuth' => user\isAuth(),
            'userName' => user\getName(),
            'userAvatar' => user\getAvatar(),
            'mainContainer' => $content,
            'categories' => $categories
            ]
        )
    );
}

/**
 * Шаблонизатор страниц
 * @param string $template - название файла шаблона из каталога templates
 * @param array  $data     - массив данных для вывода в шаблоне
 * @return false|string
 * @todo экранирование выводимых данных!
 */
function getTemplate(string $template, array $data) {
    $pathTemplate = TEMPLATE_PATH . $template;
    if(empty($template) || !file_exists($pathTemplate)) {
        return '';
    }

    extract($data, EXTR_OVERWRITE);

    ob_start();
    include $pathTemplate;

    return ob_get_clean();
}

/**
 * Валидация данных по лотам
 * @todo Добавить изображение лота по умолчанию, если указанное изображение отсутсвует
 * @param array $data - массив со списком лотов
 * @return array
 */
function checkLots(array $data) {
    array_walk($data, 'checkFields');
    foreach ($data as $key => &$lot) {
        array_walk($lot, 'validator');
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
    $diffArray = array_diff_key(array_fill_keys($lotFields, ''), $value);
    $value = array_merge($value, $diffArray);
}

/**
 * Валидация полей
 * @param        $value - значение
 * @param string $param - ключ/поле валидации
 * @param bool   $rub   - флаг ф-ции priceFormat
 */
function validator(&$value, $param = '', $rub = true) {
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
 * Имелись ли ошибки при обработке формы
 * @return bool
 */
function hasError() {
    global $errors;
    return (bool)count($errors);
}

/**
 * Проверка наличия ошибки поля при обработке формы
 * возвращает класс для отображения поля ошибки
 * @param $field - поле формы
 * @return string
 */
function checkError($field) {
    global $errors;
    return !empty($errors[$field]) ? 'form__item--invalid' : '';
}
