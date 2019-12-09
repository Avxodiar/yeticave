<?php
require_once 'src/config.php';
require_once 'src/functions.php';

if(!user\isAuth()) {
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
