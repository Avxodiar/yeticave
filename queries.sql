# Добавление существующего списка категорий
INSERT INTO `categories` (`name`) VALUES ('Доски и лыжи'), ('Крепления'), ('Ботинки'), ('Одежда'), ('Инструменты'), ('Разное');

# Добавление существующего списка пользователей
INSERT INTO `users` (`email`, `name`, `password`, `created_at`) VALUES
('ignat.v@gmail.com', 'Игнат', '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka', NOW()),
('kitty_93@li.ru', 'Леночка', '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa', NOW()),
('warrior07@mail.ru', 'Руслан', '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW', NOW());

# Список объявлений/лотов (не забыть про ссылки на пользователя и категорию)
delimiter //
CREATE FUNCTION rand_day ()
    RETURNS INT
BEGIN
    DECLARE myvar INT;
    SET myvar = CURDATE() + INTERVAL FLOOR(RAND()*7) DAY;
RETURN myvar;
END//

INSERT INTO `lots` (`name`, `category_id`, `price_start`, `price_step`, `image_url`, `data_finish`, `description`, `user_id`)
VALUES ('DC Ply Mens 2016/2017 Snowboard', 1, 12000, '100', 'img/lot-2.jpg', rand_day(), 'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчком и четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.',  1),
('2014 Rossignol District Snowboard', 1, 10999, '100', 'img/lot-1.jpg', rand_day(), '', 2),
('Крепления Union Conact Pro 2015 года размер L/XL', 2, 8000, '100', 'img/lot-3.jpg', rand_day(), '', 3),
('Ботинки для сноуборда DC Mutiny Charocal', 3, 10999, '100', 'img/lot-4.jpg', rand_day(), '', 1),
('Куртка для сноуборда DC Mutiny Charocal', 4, 7500, '100', 'img/lot-5.jpg', rand_day(), '', 2),
('Маска Oakley Canopy', 6, 5400, '100', 'img/lot-6.jpg', rand_day(), '', 3);

# Добавление пары ставок для любого объявления
INSERT INTO `bids` (`user_id`, `lot_id`, `data_insert`, `sum`) VALUES
(1,1, now(), 15000),
(1,1, now(), 14500),
(1,1, now(), 13500),
(1,1, now(), 13000),
(2,2, now(), 12100),
(2,3, now(), 8000),
(1,4, now(), 11100),
(1,4, now(), 10999),
(2,5, now(), 8200),
(2,5, now(), 7500),
(3,6, now(), 5500),
(3,6, now(), 5400);


## Запросы

# Получить список всех категорий
SELECT name FROM `categories`;

# Получить список самых новых открытых лотов.
# Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, кол-во ставок и название категории
SELECT l.id, l.name, l.price_start, l.image_url, MAX(b.sum) AS price, COUNT(b.id) AS count_bids, c.name as category_name
FROM `lots` AS l
JOIN bids AS b ON l.id = b.lot_id
JOIN categories as c ON c.id = l.category_id
GROUP BY b.lot_id;

# Получить лот по его ID c названием категории к которой он принадлежит
SELECT l.id, l.name, c.name AS category_name
FROM `lots` AS l
JOIN categories AS c ON c.id = l.category_id
WHERE l.id = 3;

# Обновить название лота по его ID
UPDATE `lots`
SET name = '2014 Rossignol District Snowboard Black Edition'
WHERE id = 2;

# Получить список самых свежих ставок для лота по его ID
SELECT b.id, b.user_id, b.data_insert, b.sum
FROM bids b
JOIN lots l ON b.lot_id = l.id
WHERE l.id = 4
ORDER BY sum DESC;
