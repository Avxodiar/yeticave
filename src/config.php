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

$categoryPage = ($_SERVER['SCRIPT_NAME'] === '/category.php');
define('CATEGORY_PAGE', $categoryPage);

$logoLink = MAIN_PAGE ? '' : ' href="/"';

const ADMIN_MAIL = '';

const DB_CONFIG = [
   'host' => 'yeticave',
   'database' => 'yeticave',
   'user' => 'root',
   'password' => '',
   'port' => 3306
];

// кол-во отображаемых лотов на главной странице
const LOTS_ON_INDEX = 9;
// кол-во отображаемых лотов на страницу каталога и истории просмотренных лотов в профиле пользователя
const LOTS_ON_PAGE = 3;

$JS = array();
