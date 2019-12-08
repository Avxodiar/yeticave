<?php
require_once 'src/config.php';
require_once 'src/functions.php';

if(user\isAuth()) {
    $indexContent = getTemplate(
        'welcome.php',
        [
            'message' => 'Вы авторизованы. Хотите выйти?'
        ]
    );
} else {
    header('Location: /login.php');
    exit();
}

includeTemplate('Авторизация', $indexContent);
