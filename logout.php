<?php
require_once 'src/init.php';

use function yeticave\user\isAuth;
use function yeticave\user\logout;

if(!isAuth()) {
    header('Location: /login.php');
    exit();
}

if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    logout();
    header('Location: /');
    exit();
}

$content = getTemplate(
    'welcome.php',
    [
        'message' => 'Вы авторизованы. Хотите выйти?',
        'button' => 'Выйти',
        'buttonUrl' => '/logout.php?logout=true'
    ]
);

includeTemplate('Завершение сеанса', $content);
