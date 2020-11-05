<?php

use function yeticave\lot\{getFinishedLots, getUserSelectedBids, setLotWinner};

// список последних ставок по лотам без победителей, дата истечения которых меньше или равна текущей дате
$res = getFinishedLots();
$bids = [];
foreach ($res as $bid) {
    $bids[$bid['b_id']] = $bid;
}

// список пользователей победителей по последним ставкам
$bidUsers = getUserSelectedBids($bids);
foreach ($bidUsers as $user) {
    if (isset($bids[$user['b_id']])) {
        $bids[$user['b_id']]['user_id'] = $user['user_id'];
        $bids[$user['b_id']]['user_name'] = $user['user_name'];
        $bids[$user['b_id']]['user_mail'] = $user['user_mail'];
    }
}

foreach ($bids as $bid) {
    // Записываем в лот победителем автора последней ставки
    setLotWinner($bid['lot_id'], $bid['user_id']);

    // Отправка победителям на email письмо – поздравление с победой
    if (!sendWinnerMail($bid['user_mail'], $bid['user_name'], $bid['lot_id'], $bid['lot_name'])) {
        errorLog("error sending mail for winner: {$bid['user_id']}  lot: {$bid['b_id']}");
    }
}
