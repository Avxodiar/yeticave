<?php
require_once 'src/init.php';

use function yeticave\user\isAuth;
use function yeticave\user\getName;
use function yeticave\user\getAvatar;

if(!isAuth()) {
    header('Location: /login.php');
    exit();
}

$content = getTemplate(
    'profile.php', [
    'name' => getName(),
    'avatar' => getAvatar()
]);


includeTemplate('Информация о пользователе', $content);
