<?php
require_once 'src/init.php';

use function yeticave\user\isAuth;
use function yeticave\lot\getLotHistory;
use function yeticave\lot\check;
use function yeticave\lot\getLots;

if(!isAuth()) {
    errorPage(403);
}

$historyLot = getLotHistory();
$lots = (count($historyLot)) ? check( getLots($historyLot) ) : [];

$content = getTemplate('history.php', ['lots' => $lots ] );

includeTemplate('История просмотров', $content);
