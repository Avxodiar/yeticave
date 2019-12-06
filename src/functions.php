<?php

/**
 * Шаблонизатор страниц
 * @param string $template - название файла шаблона из каталога templates
 * @param array  $data     - массив данных для вывода в шаблоне
 * @return false|string
 */
function getTemplate(string $template, array $data) {
    $pathTemplate = __DIR__. '/../templates/' . $template;
    if(empty($template || !file_exists($pathTemplate))) {
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
    $pathImages = __DIR__. '/../img/';
    foreach ($data as $key => $lot) {
        foreach ($lot as $param => $value) {
            switch ($param) {
                case 'name':
                case 'category':
                    $data[$key][$param] = htmlspecialchars($value);
                    break;
                case 'price':
                    $data[$key][$param] = priceFormat($value);
                    break;
                case 'foto':
                    $url = pathinfo($value);
                    $data[$key][$param] = file_exists($pathImages . $url['basename']) ? $value : '';
            }
        }
    }
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
 * @return string
 */
function priceFormat($price) {
    $priceFormat = (int) ceil($price);
    if($priceFormat > 1000) {
        $priceFormat = number_format($priceFormat, 0, '', ' ');
    }
    return $priceFormat . ' <b class="rub">р</b>';
}
