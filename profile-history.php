<?php
require_once 'src/init.php';

use function yeticave\user\isAuth;
use function yeticave\lot\getLotHistory;
use function yeticave\lot\getLots;
use function yeticave\lot\getLotsCount;

if(!isAuth()) {
    errorPage(403);
}

$historyLot = getLotHistory();

// кол-во активных лотов
$count = getLotsCount($historyLot);
// кол-во страниц
$pages = (int) ceil($count / LOTS_ON_PAGE);

$pageId = (int) ($_GET['page'] ?? 1 );
// если указан 0, то показываем начальную страницу
if($pageId === 0) {
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
// если указана страница больше максимальной, то показываем последнюю
$uri = $_SERVER['PHP_SELF'] . '?page=';
if($pageId > $pages) {
    header('Location: ' . $uri . $pages);
    exit();
};

$lots = (count($historyLot)) ? getLots($historyLot, LOTS_ON_PAGE, ($pageId-1) * LOTS_ON_PAGE) : [];

$content = getTemplate(
    'user-history.php', [
        'lots' => $lots,
        'curPage' => $pageId,
        'pages' => $pages,
        'uri' => $uri,
        'backHref' => ($pageId === 1) ? '' : 'href="' . $uri . ($pageId - 1) .'"',
        'forwardHref' => ($pageId < $pages) ? 'href="' . $uri . ($pageId + 1) .'"' : ''
    ]
);

includeTemplate('История просмотров', $content);
