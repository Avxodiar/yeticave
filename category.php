<?php
require_once 'src/init.php';

use function yeticave\lot\{getLotsCategoryCount, getCategories, getCategoryLots};

$id = (int) $_GET['id'];

$categories = getCategories();
if(!$id || !isset($categories[$id])) {
    errorPage(404);
}

// текущая страница
$pageId = (int) ($_GET['page'] ?? 1 );
// текущий адрес
$uri = $_SERVER['PHP_SELF'] . '?id='. $id;

// кол-во активных лотов (реальное кол-во)
$lotCount = getLotsCategoryCount($id);

// проверка корректности номера текущей страницы
checkPage($pageId, $uri, $lotCount);

// список лотов на указанной странице
$lots = getCategoryLots($id, LOTS_ON_PAGE, ($pageId-1) * LOTS_ON_PAGE);

$content = getTemplate(
    'category.php', [
        'catName' => $categories[$id],
        'lots' => $lots,
        'pagination' => pagination($pageId, $uri, $lotCount)
    ]
);

includeTemplate($categories[$id], $content);
