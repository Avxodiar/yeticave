<?php
require_once 'src/init.php';

use function yeticave\user\isAuth;
use function \yeticave\lot\getUserBets;

if(!isAuth()) {
    header('Location: /login.php');
    exit();
}

$content = getTemplate(
    'my-bets.php', [
    'bets' => getUserBets()
]);

includeTemplate('Мои ставки', $content);
