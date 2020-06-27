<?php
require_once 'src/init.php';

use function yeticave\user\isAuth;
use function yeticave\lot\getLot;
use function yeticave\lot\check;
use function yeticave\lot\addLotHistory;

$id = $_GET['id'] ?? -1;
if($id < 0 ) {
    errorPage(404);
}

// проверяем выбранный лот
$lot = getLot($id);
if(empty($lot)) {
    errorPage(404);
}
$lot = current(check([$lot]));

// добавляем лот в список просмотренных для авторизованных пользователей
if(isAuth()) {
    addLotHistory($id);
}

// валидация ставок на корректность заполнения
array_walk_recursive($bets, '\yeticave\lot\lotValidator', false);

$content = getTemplate(
    'lot.php', [
    'lot' => $lot,
    'bets' => $bets
]);


includeTemplate($lot['name'], $content);
