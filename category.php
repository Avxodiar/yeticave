<?php
require_once 'src/init.php';

use function yeticave\lot\{getLotsCategoryCount, getCategories, getCategoryLots};

$id = (int)$_GET['id'];

$categories = getCategories();
if(!$id || !isset($categories[$id])) {
    errorPage(404);
}

// кол-во активных лотов (реальное кол-во)
$count = getLotsCategoryCount($id);
// кол-во страниц
$pages = (int) ceil($count / LOTS_ON_PAGE);

$pageId = (int) ($_GET['page'] ?? 1 );

$uri = $_SERVER['PHP_SELF'] . '?id='. $id;
// если указан 0, то показываем начальную страницу
if($pageId === 0) {
    header('Location: ' . $uri);
    exit();
}
// если указана страница больше максимальной, то показываем последнюю
$uri .= '&page=';
if($count && $pageId > $pages) {
    header('Location: ' . $uri . $pages);
    exit();
};

$lots = getCategoryLots($id, LOTS_ON_PAGE, ($pageId-1) * LOTS_ON_PAGE);

$content = getTemplate(
    'category.php', [
        'catName' => $categories[$id],
        'lots' => $lots,
        'curPage' => $pageId,
        'pages' => $pages,
        'uri' => $uri,
        'backHref' => ($pageId === 1) ? '' : 'href="' . $uri . ($pageId - 1) .'"',
        'forwardHref' => ($pageId < $pages) ? 'href="' . $uri . ($pageId + 1) .'"' : ''
    ]
);

includeTemplate($categories[$id], $content);
