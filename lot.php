<?php
require_once 'src/config.php';
require_once 'src/functions.php';

$id = $_GET['id'] ?? 0;
if(!$id || !isset($lots[$id])) {
    errorPage(404);
}

// проверяем выбранный лот
$lot = $lots[$id];
$lot = current(checkLots([$lot]));

// валидация ставок на корректность заполнения
array_walk_recursive($bets, 'validator', false);

$lotContent = getTemplate(
    'lot.php', [
    'lot' => $lot,
    'bets' => $bets
]);


includeTemplate($lot['name'], $lotContent);
