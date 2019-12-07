<?php

// список категорий
$categories = array('Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное');

// список полей у лота
$lotFields = ['name', 'category', 'pict', 'alt', 'price', 'minPrice', 'timer', 'description'];
// список ставок
$lots = array(
    array(
        'name' => '2014 Rossignol District Snowboard',
        'category' => 'Доски и лыжи',
        'price' => 10999,
        'pict' => 'img/lot-1.jpg',
        'alt' => 'Сноуборд'
    ),
    array(
        'name' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи',
        'pict' => 'img/lot-2.jpg',
        'alt' => 'Сноуборд',
        'price' => 10999,
        'minPrice' => 12000,
        'timer' => '10:54:12',
        'description' => 'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.'
    ),
    array(
        'name' => 'Крепления Union Conact Pro 2015 года размер L/XL',
        'category' => 'Крепления',
        'price' => 8000,
        'pict' => 'img/lot-3.jpg'
    ),
    array(
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Ботинки',
        'price' => 10999,
        'pict' => 'img/lot-4.jpg'
    ),
    array(
        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'Одежда',
        'price' => 7500,
        'pict' => 'img/lot-5.jpg'
    ),
    array(
        'name' => 'Маска Oakley Canopy',
        'category' => 'Разное',
        'price' => 5400,
        'pict' => 'img/lot-6.jpg'
    )
);

// ставки пользователей, которыми надо заполнить таблицу
$bets = [
    ['name' => 'Иван', 'price' => 11500, 'ts' => strtotime('-' . rand(1, 50) .' minute')],
    ['name' => 'Константин', 'price' => 11000, 'ts' => strtotime('-' . rand(1, 18) .' hour')],
    ['name' => 'Евгений', 'price' => 10500, 'ts' => strtotime('-' . rand(25, 50) .' hour')],
    ['name' => 'Семён', 'price' => 10000, 'ts' => strtotime('last week')]
];
