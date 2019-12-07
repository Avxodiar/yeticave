<?php
date_default_timezone_set('Europe/Moscow');

// корень сайта
define('ROOT', $_SERVER['DOCUMENT_ROOT']);
// путь до каталога шаблонов
define('TEMPLATE_PATH', ROOT. '/templates/');
// путь до каталога изображений
define('IMG_PATH', ROOT. '/img/');

$mainPage = ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php');
define('MAIN_PAGE', $mainPage);

$logoLink = MAIN_PAGE ? '' : ' href="/"';

//@TODO Вынести в модуль регистрации
$User = [
  'isAuth' => (bool) rand(0, 1),
  'name' => 'Константин',
  'avatar' =>  'img/user.jpg'
];
