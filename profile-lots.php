<?php
require_once 'src/init.php';

use function yeticave\user\isAuth;
use function yeticave\lot\getUserLots;

if(!isAuth()) {
    header('Location: /login.php');
    exit();
}

$content = getTemplate(
    'user-lots.php', [
    'lots' => getUserLots()
]);

includeTemplate('Мои лоты', $content);
