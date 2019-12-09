<?php
require_once 'src/config.php';
require_once 'src/functions.php';

if(!user\isAuth()) {
    header('Location: /login.php');
    exit();
}

$content = getTemplate(
    'profile.php', [
    'name' => user\getName(),
    'avatar' => user\getAvatar()
]);


includeTemplate('Информация о пользователе', $content);
