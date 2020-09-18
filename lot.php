<?php
require_once 'src/init.php';

use function yeticave\user\isAuth;
use function yeticave\lot\{getLots, addLotHistory, addBet, getBets};

function checkAjax() {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $url = parse_url($referer);
    $isAjax = ($_SERVER['REQUEST_METHOD'] === 'POST' && $url['path'] === '/lot.php');
    if(!$isAjax) {
        return false;
    }

    // пользователь не авторизован
    if(!isAuth()) {
        http_response_code(401);
    }

    //id лота указано не верно
    $lotId = isset($_POST['lot']) ? (int) $_POST['lot'] : 0;
    if(!$lotId) {
        http_response_code(400);
    }

    // не верно указана ставка
    $cost = isset($_POST['cost']) ? (int) $_POST['cost'] : 0;
    if(!$cost) {
        http_response_code(412);
    }

    // указанный лот не найден
    $lot = current(getLots([$lotId]));
    if(empty($lot)) {
        http_response_code(410);
    }

    // указанная ставка меньше минимальной
    if($lot['minPrice'] > $cost) {
        http_response_code(409);
    }

    //добавляем ставку
    if ( addBet($lotId, $cost) ) {
        //получаем новые данные по лоту

        $lot = current(getLots([$lotId]));
        $data = [
            'lot' => $lot,
            'priceFormat' => $lot['priceFormat'],
            'minPrice' => $lot['minPrice'],
            'minPriceFormat' => $lot['minPriceFormat'],
            'bets' => getBets($lotId)
        ];

        echo json_encode($data);
        exit();
    }

    http_response_code(503);
    exit();
}

checkAjax();

$id = $_GET['id'] ?? -1;
if($id < 0 ) {
    errorPage(404);
}

// проверяем выбранный лот
$lot = current(getLots([$id]));
if(empty($lot)) {
    errorPage(404);
}

// добавляем лот в список просмотренных для авторизованных пользователей
if(isAuth()) {
    addLotHistory($id);
}

$content = getTemplate(
    'lot.php', [
    'lot' => $lot,
    'bets' => getBets($id)
]);


includeJS('js/backend.js');
includeJS('js/lot.js');
includeTemplate($lot['name'], $content);
