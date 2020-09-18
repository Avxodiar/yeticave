<?php
require_once 'src/init.php';

use function yeticave\user\{isAuth, getName, getAvatar};
use function yeticave\lot\{getUserLotCount, getUserBetCount};

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
