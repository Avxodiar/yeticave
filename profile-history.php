<?php
require_once 'src/init.php';

use function yeticave\user\isAuth;
use function yeticave\lot\{getLotHistory, getLotsCount, getLots};

if(!isAuth()) {
    errorPage(403);
}

$historyLot = getLotHistory();

// кол-во активных лотов
$lotCount = getLotsCount($historyLot);
// кол-во страниц
$pages = (int) ceil($lotCount / LOTS_ON_PAGE);

// текущая страница
$pageId = (int) ($_GET['page'] ?? 1 );
// текущий адрес
$uri = $_SERVER['PHP_SELF'];

// проверка корректности номера текущей страницы
checkPage($pageId, $uri, $lotCount);

$lots = (count($historyLot)) ? getLots($historyLot, LOTS_ON_PAGE, ($pageId-1) * LOTS_ON_PAGE) : [];

$content = getTemplate(
    'user-history.php', [
        'lots' => $lots,
        'pagination' => pagination($pageId, $uri, $lotCount)
    ]
);

includeTemplate('История просмотров', $content);
