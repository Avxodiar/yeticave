<?php
require_once 'src/config.php';
require_once 'src/functions.php';
require_once 'src/data.php';

$id = (int)$_GET['id'];
if(!isset($lots[$id])) {
    http_response_code(404);
    die;
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
