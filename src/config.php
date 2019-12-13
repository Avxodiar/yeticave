<?php
date_default_timezone_set('Europe/Moscow');

// корень сайта
define('ROOT', $_SERVER['DOCUMENT_ROOT']);
// путь до каталога шаблонов
define('TEMPLATE_PATH', ROOT . '/templates/');
// путь до каталога изображений
define('IMG_PATH', ROOT . '/img/');

// каталог для загрузки пользовательских изображений
define('USER_UPLOAD_DIR', '/upload');
// каталог загрузки аватарок пользователя
define('AVATARS_UPLOAD_DIR', USER_UPLOAD_DIR . '/avatars/');
// каталог загрузки лотов
define('LOTS_UPLOAD_DIR', USER_UPLOAD_DIR . '/lots/');

// максимальный размер загружаемый файлов 2Мб
define ('UPLOAD_MAX_SIZE', 2097152);
// поддерживаемые форматы изображений
define ('SUPPORTED_IMAGES', ['jpg', 'jpeg', 'png']);

$mainPage = ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php');
define('MAIN_PAGE', $mainPage);

$logoLink = MAIN_PAGE ? '' : ' href="/"';

session_start();
