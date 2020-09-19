<?php
require_once 'src/init.php';

use function yeticave\lot\search;

$search = $_GET['q'] ?? '';

$error = (strlen($search) < 3) ? 'Запрос слишком короткий.' : '';
$res = [];
if (empty($error)) {
    $res = search($search);
    $error = (empty($res)) ? 'По вашему запросу ничего не найдено.' : '';
}

$content = getTemplate('search.php', ['search' => htmlspecialchars($search), 'elems' => $res, 'error' => $error]);

includeTemplate('Поиск', $content);
