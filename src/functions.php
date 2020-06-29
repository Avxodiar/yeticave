<?php
use function yeticave\user\isAuth;
use function yeticave\user\getName;
use function yeticave\user\getAvatar;

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
            'categories' => \yeticave\lot\getCategories()
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
