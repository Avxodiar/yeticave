<?php
require_once 'src/init.php';

use function yeticave\user\isAuth;
use function yeticave\user\getName;
use function yeticave\user\getAvatar;
use function yeticave\lot\getUserLotCount;
use function yeticave\lot\getUserBetCount;

if(!isAuth()) {
    header('Location: /login.php');
    exit();
}

$content = getTemplate(
    'profile.php', [
    'name' => getName(),
    'avatar' => getAvatar(),
    'lotCount' => getUserLotCount(),
    'betCount' => getUserBetCount()
]);

includeTemplate('Информация о пользователе', $content);
