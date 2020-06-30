<?php
use function yeticave\user\isAuth;
use function yeticave\user\getName;
use function yeticave\user\getAvatar;
use function yeticave\lot\getCategories;

/**
 * Подключение основного шаблона
 * @param $title
 * @param $content
 */
function includeTemplate($title, $content) {
    global $logoLink;

    print(
        getTemplate(
        'layout.php', [
            'pageTitle' => $title,
            'logoLink' => $logoLink,
            'isAuth' => isAuth(),
            'userName' => getName(),
            'userAvatar' => getAvatar(),
            'mainContainer' => $content,
            'categories' => getCategories()
            ]
        )
    );
}

/**
 * Шаблонизатор страниц
 * @param string $template - название файла шаблона из каталога templates
 * @param array  $data     - массив данных для вывода в шаблоне
 * @return false|string
 */
function getTemplate(string $template, array $data) {
    $pathTemplate = TEMPLATE_PATH . $template;
    if(empty($template) || !file_exists($pathTemplate)) {
        return '';
    }

    $data['isAuth'] = isAuth();
    extract($data, EXTR_OVERWRITE);

    ob_start();
    include $pathTemplate;

    return ob_get_clean();
}

/**
 * добавление js файла для подключения в шаблон
 * @param string $jsPath
 */
function includeJS(string $jsPath) {
    global $JS;

    if(file_exists(ROOT .'/'. $jsPath)) {
        $JS[] = $jsPath;
    }
}

/**
 * Вывод списка js файлов подключенных через includeJS в шаблоне
 */
function showJS(){
    global $JS;

    if (!empty($JS)) {
        foreach ($JS as $jsFile) {
            echo "<script src='{$jsFile}'></script>";
        }
    }
}

/**
 * Отображение страницы с ошибкой
 * @param $code - http код ошибки
 */
function errorPage($code) {
    http_response_code($code);
    $_SERVER['REDIRECT_STATUS'] = $code;
    require_once ROOT. '/error.php';
    exit();
}

/**
 * Логирование ошибок
 * Если не указан файл, то ошибка отправляется на email администратору
 * Также сообщение message отправляется в системный регистратор PHP, используя механизм логирования операционной
 * системы, или файл, в зависимости от значения директивы error_log в конфигурационном файле.
 * @param string $message - текст ошибки
 * @param string $file - файл для логирования ошибок
 */
function errorLog(string $message, string $file = '') {
    if(!empty($file)) {
        error_log($message . PHP_EOL, 3, $file);
    }
    elseif (defined('ADMIN_MAIL') && !empty(ADMIN_MAIL)) {
        error_log($message, 1, ADMIN_MAIL);
    }

    error_log($message, 0);
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
 * Форматирование вывода времени
 * @param int $timestamp
 * @return false|string
 */
function timeFormat(int $timestamp) {
    $timeDiff = time() - $timestamp;

    // для вывода на странице лота времени в истории ставок
    if($timeDiff >= 0) {
        if ($timeDiff < 7000) {
            $timeDiff = floor($timeDiff / 60);
            $ts = ($timeDiff > 60) ? 'Час назад' : $timeDiff . ' минут назад';
        } else {
            $ts = gmdate('y.m.d в H:i', $timestamp);
        }
    }
    // для вывода на страницах профиля пользователя в списках лотов и ставок
    else {
        $timeDiff = abs($timeDiff);
        if ($timeDiff > 86400) {
            $ts = gmdate('dд Hч iм', $timeDiff);
        } else {
            $ts = gmdate('H:i:s', $timeDiff);
        }
    }
    return $ts;
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
