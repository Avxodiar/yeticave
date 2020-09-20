<?php
require_once 'src/init.php';

use function yeticave\lot\{search, searchCount};

$search = $_GET['q'] ?? '';

$error = (strlen($search) < 3) ? 'Запрос слишком короткий.' : '';

// текущая страница
$pageId = (int) ($_GET['page'] ?? 1 );
// текущий адрес
$uri = $_SERVER['PHP_SELF'] . '?q='. $search;

$searchCount = 0;
$res = [];
if (empty($error)) {
    $searchCount = searchCount($search);
    if($searchCount) {
        $res = search($search, LOTS_ON_PAGE, ($pageId-1) * LOTS_ON_PAGE);
    } else {
        $error = 'По вашему запросу ничего не найдено.';
    }
}


// проверка корректности номера текущей страницы
checkPage($pageId, $uri, $searchCount);

$content = getTemplate(
    'search.php', [
        'search' => htmlspecialchars($search),
        'elems' => $res,
        'error' => $error,
        'pagination' => pagination($pageId, $uri, $searchCount)
    ]
);

includeTemplate('Поиск', $content);
