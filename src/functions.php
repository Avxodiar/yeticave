<?php

use function yeticave\user\{isAuth, getName, getAvatar};
use function yeticave\lot\getCategories;

/**
 * Подключение основного шаблона
 * @param string $title - заголовок страницы
 * @param $content - содержимое страницы
 * @param array  $js - список подключаемы js скриптов
 */
function includeTemplate(string $title, $content, array $js = []) : void
{
    print(
        getTemplate(
        'layout.php', [
            'pageTitle' => $title,
            'logoLink' => MAIN_PAGE ? '' : ' href="/"',
            'isAuth' => isAuth(),
            'userName' => getName(),
            'userAvatar' => getAvatar(),
            'mainContainer' => $content,
            'categories' => getCategories()
            ],
            $js
        )
    );
}

/**
 * Шаблонизатор страниц
 * @param string $template - название файла шаблона из каталога templates
 * @param array  $data     - массив данных для вывода в шаблоне
 * @param array  $js - список подключаемы js скриптов
 * @return string
 */
function getTemplate(string $template, array $data, array $js = []): string
{
    $pathTemplate = TEMPLATE_PATH . $template;
    if (empty($template) || !file_exists($pathTemplate)) {
        return '';
    }

    // данные для вывода в шаблоне
    $data['isAuth'] = isAuth();
    // наличие ошибок валидации в форме
    $hasValidError = isset($data['errors']) ? (bool) count($data['errors']) : false;
    // наличие ошибок валидации у полей формы
    $hasFieldValidError = [];
    if (isset($data['errors'])) {
        foreach ($data['errors'] as $field => $value) {
            $hasFieldValidError[$field] = !empty($value);
        }
    }
    extract($data, EXTR_OVERWRITE);
    $jsList = getJsList($js);

    ob_start();
    include $pathTemplate;

    return ob_get_clean();
}

/**
 * Вывод списка js файлов в шаблоне
 * @param array $js
 * @return string
 */
function getJsList(array $js): string
{
    $jsList = '';

    if (!empty($js)) {
        foreach ($js as $jsFile) {
            if (file_exists(ROOT . '/' . $jsFile)) {
                $jsList .= "<script src='{$jsFile}'></script>\n";
            }
        }
    }

    return $jsList;
}

/**
 * Проверка корректности текущей страницы
 * При не верном значении выполняет редирект на первую или последнюю страницу
 * @param int    $pageId - номер текущей страницы
 * @param string $uri - текущуй адрес
 * @param int    $countElem - суммарное кол-во элементов на всех страницах
 */
function checkPage(int $pageId, string $uri, int $countElem): void
{
    // кол-во страниц
    $pageCount = (int) ceil($countElem / LOTS_ON_PAGE);

    // если указан 0, то показываем начальную страницу раздела
    if ($pageId === 0) {
        header('Location: ' . $uri);
        exit();
    }

    // если указана страница больше максимальной, то показываем последнюю
    if ($pageCount && $pageId > $pageCount) {
        header('Location: ' . $uri . '&page=' . $pageCount);
        exit();
    }
}

/**
 * Генерация отображения блока пагинации
 * @param int    $pageId - номер текущей страницы
 * @param string $uri - текущуй адрес
 * @param int    $countElem - суммарное кол-во элементов на всех страницах
 * @param bool   $hide - не отображать блок пагинации если кол-во элементов меньше
 * @return string
 */
function pagination(int $pageId, string $uri, int $countElem, bool $hide = false): string
{
    if ($countElem < 1 || ($hide && $countElem < LOTS_ON_PAGE)) {
        return '';
    }

    // кол-во страниц
    $pageCount = (int) ceil($countElem / LOTS_ON_PAGE);

    return getTemplate('pagination.php',
        [
            'curPage' => $pageId,
            'pageCount' => $pageCount,
            'uri' => $uri,
            'backHref' => ($pageId === 1) ? '' : 'href="' . $uri . ($pageId - 1) . '"',
            'forwardHref' => ($pageId < $pageCount) ? 'href="' . $uri . ($pageId + 1) . '"' : '',
        ]
    );
}

/**
 * Отображение страницы с ошибкой
 * @param $code - http код ошибки
 */
function errorPage($code): void
{
    http_response_code($code);
    $_SERVER['REDIRECT_STATUS'] = $code;
    require_once ROOT . '/error.php';
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
function errorLog(string $message, string $file = ''): void
{
    if (!empty($file)) {
        error_log($message . PHP_EOL, 3, $file);
    } elseif (defined('ADMIN_MAIL') && !empty(ADMIN_MAIL)) {
        error_log($message, 1, ADMIN_MAIL);
    }

    error_log($message, 0);
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
function priceFormat($price, $rub = true): string
{
    $priceFormat = (int) ceil($price);
    if ($priceFormat > 1000) {
        $priceFormat = number_format($priceFormat, 0, '', ' ');
    }
    $priceFormat .= ($rub) ? ' <b class="rub">р</b>' : ' р';

    return $priceFormat;
}

/**
 * Форматирование вывода времени
 * @param int $timestamp
 * @return string
 */
function timeFormat(int $timestamp): string
{
    $timeDiff = time() - $timestamp;

    // на странице лота времени в истории ставок
    if ($timeDiff >= 0) {
        if ($timeDiff < 7000) {
            $timeDiff = floor($timeDiff / 60);
            $ts = ($timeDiff > 60) ? 'Час назад' : $timeDiff . ' минут назад';
        } else {
            $ts = gmdate('y.m.d в H:i', $timestamp);
        }
    } else {
        // на страницах профиля пользователя в списках лотов и ставок
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
function getLeftMidnight(): string
{
    $midnight = mktime(0, 0, 0, date('n'), date('j') + 1, date('Y'));
    $left = $midnight - time();

    return gmdate('H:i', $left);
}
