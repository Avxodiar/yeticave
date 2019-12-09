<?php
require_once 'src/config.php';
require_once 'src/functions.php';

$id = $_GET['id'] ?? -1;
if($id < 0 || !isset($lots[$id])) {
    errorPage(404);
}

// проверяем выбранный лот
$lot = $lots[$id];
$lot = current(checkLots([$lot]));

// добавляем лот в список просмотренных для авторизованных пользователей
if(user\isAuth()) {
    addLotHistory($id);
}

// валидация ставок на корректность заполнения
array_walk_recursive($bets, 'validator', false);

$content = getTemplate(
    'lot.php', [
    'lot' => $lot,
    'bets' => $bets
]);


includeTemplate($lot['name'], $content);
