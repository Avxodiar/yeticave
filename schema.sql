DROP DATABASE IF EXISTS yeticave;

CREATE DATABASE yeticave
    DEFAULT CHARACTER SET UTF8
    DEFAULT COLLATE UTF8_GENERAL_CI;

USE yeticave;

CREATE TABLE users
(
    `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `active`      TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `name`        VARCHAR(64)   NOT NULL,
    `email`       VARCHAR(64)   NOT NULL,
    `password`    VARCHAR(255)  NOT NULL,
    `created_at`  DATETIME      NOT NULL,
    `about`       TEXT(2048),
    `avatar_url`  VARCHAR(255),
    PRIMARY KEY(`id`)
) ENGINE = InnoDB;

CREATE TABLE lots
(
    `id`          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `active`      TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
    `name`        VARCHAR(255)   NOT NULL,
    `category_id` INT UNSIGNED   NOT NULL,
    `user_id`     INT UNSIGNED   NOT NULL,
    `image_url`   VARCHAR(255)   NOT NULL,
    `data_start`  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `data_finish` DATETIME       NOT NULL,
    `price_start` DECIMAL(10, 0) NOT NULL,
    `price_step`  DECIMAL (10,0) NOT NULL,
    `like_count`  INT UNSIGNED  DEFAULT '0',
    `winner_id`   INT UNSIGNED  DEFAULT NULL,
    `description` TEXT(2048),
    PRIMARY KEY(`id`)
) ENGINE = InnoDB;

CREATE TABLE categories (
    `id`          INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(255)   NOT NULL,
    PRIMARY KEY(`id`)
) ENGINE = InnoDB;

CREATE TABLE bids (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED  NOT NULL,
  `lot_id`      INT UNSIGNED  NOT NULL,
  `data_insert` DATETIME      NOT NULL,
  `sum`         DECIMAL(10,0) NOT NULL,
  PRIMARY KEY(`id`)
) ENGINE = InnoDB;

CREATE UNIQUE INDEX `email` ON users(`email`);
CREATE UNIQUE INDEX `name` ON categories(`name`);

# Создание индекса полнотекстового поиска
CREATE FULLTEXT INDEX yeti_search ON  lots(name, description)
