-- Создаем базу данных
CREATE DATABASE IF NOT EXISTS tree CHARACTER SET utf8 COLLATE utf8_general_ci;

-- SET NAMES utf8;
SET NAMES cp866’;
-- Используем базу данных
USE tree;

-- Категории
CREATE TABLE IF NOT EXISTS client
(
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
    identifier int(4) ZEROFILL NOT NULL DEFAULT '0' COMMENT 'Индетификатор',
    name VARCHAR(255) NOT NULL  COMMENT 'Имя клиента',
    company VARCHAR(255) NULL COMMENT 'Имя компании',
    project_name VARCHAR(255) NULL COMMENT 'Имя проекта',
    description TEXT NULL  COMMENT 'Описание',
    host VARCHAR(255) NOT NULL  COMMENT 'Хост',
    email VARCHAR(255) NULL COMMENT 'Эмейл клиента',
    position INT(10) NOT NULL DEFAULT '0' COMMENT 'Вес страницы',
    visible TINYINT(1) NOT NULL DEFAULT '1' COMMENT '1 = включен',
    CONSTRAINT pkId PRIMARY KEY (id)
)ENGINE = INNODB;

ALTER TABLE client MODIFY identifier int(4) ZEROFILL;