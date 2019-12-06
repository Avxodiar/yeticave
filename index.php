<?php
require_once 'src/config.php';
require_once 'src/functions.php';
require_once 'src/data.php';

$isAuth = (bool) rand(0, 1);
$userName = 'Константин';
$userAvatar = 'img/user.jpg';

$page_content = getTemplate('index.php', ['lots' => checkLots($lots)]);
$page = getTemplate(
    'layout.php', [
    'pageName' => 'Главная',
    'isAuth' => $isAuth,
    'userName' => $userName,
    'userAvatar' => $userAvatar,
    'mainContainer' => $page_content,
    'categories' => $categories
]);

print($page);
