<?php
require_once 'src/config.php';
require_once 'src/functions.php';

if(!user\isAuth()) {
    header('Location: /login.php');
    exit();
}

if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    user\logout();
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
