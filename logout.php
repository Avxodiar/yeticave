<?php
require_once 'src/config.php';
require_once 'src/functions.php';

if(!user\isAuth()) {
    header('Location: /login.php');
    exit();
}

$content = getTemplate(
    'welcome.php',
    [
        'message' => 'Вы авторизованы. Хотите выйти?'
    ]
);

includeTemplate('Авторизация', $content);
