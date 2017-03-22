<?php
/**
 * Создание таблицы
 * User: vvpol
 * Date: 21.03.2017
 * Time: 7:26
 */
try {
    require_once '../classes/core.php';
    core::initDB();
    $table = core::$tableName;
    $sql = <<<SQL
CREATE TABLE $table (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  short_url varchar(10) NOT NULL,
  url varchar(250) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE INDEX short_url (short_url),
  UNIQUE INDEX url (url)
)
ENGINE = INNODB
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci;
SQL;
    Model::update($sql, []);
    $sql = 'insert into `' . $table . '` (`url`, `short_url`) values(?, ?)';
    Model::update($sql, ['X', 'AAAAA']);
    die('table ' . $table . ' created');
} catch (Exception $e) {
    die($e->getMessage());
}




