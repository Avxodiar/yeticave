<?php
require_once 'src/init.php';

use function yeticave\user\isAuth;

if(!isAuth()) {
    errorPage(403);
}

$historyLot = getLotHistory();

$content = getTemplate(
    'history.php',
    [
        'history' => $historyLot,
        'lots' => checkLots($lots),
    ]
);

includeTemplate('История просмотров', $content);
