<?php

/**
 * Модель для приска url
 * User: vvpol
 * Date: 21.03.2017
 * Time: 7:27
 */
class Model
{
    /**
     * Запись/модификация таблицы
     * @param $sql
     * @param $values
     * @throws Exception
     */
    public static function update($sql, $values){
        try	{
            if (empty(core::$DBH)) core::initDB();
            $STH = core::$DBH->prepare($sql);
            $STH->execute($values);
        } catch (PDOException $e) {
            throw new PDOException( $e -> getMessage() . ':' . $sql );
        }
    }

    /**
     * Функция запросов с получением даныых
     * @param $sql
     * @param $params
     * @return array
     * @throws Exception
     */
    private function select($sql, $params)
    {
        try {
            if (empty(core::$DBH)) core::initDB();
        } catch (PDOException $e) {
            throw new Exception($e -> getMessage() . PHP_EOL . $sql);
        }
        $STH = core::$DBH->prepare($sql);
        $STH -> execute($params);
        $STH->setFetchMode(PDO::FETCH_ASSOC);
        $res = array();
        while($row = $STH->fetch()) {
            $res[] = $row;
        }
        return $res;
    }

    /**
     * Добавление записи в таблицу
     * @param $data
     */
    public function insert($data)
    {
        if (empty(core::$DBH)) core::initDB();
        $sql = 'insert into `' . core::$tableName . '` (`url`, `short_url`) values(:url, :short_url)';
        self::update($sql, $data);
    }

    /**
     * Поиск одиночной записи
     * @param $key
     * @param $val
     * @return array|mixed
     * @throws Exception
     */
    public function findOne($key, $val)
    {
        try {
            if (empty(core::$DBH)) core::initDB();
            if ($key == 'last'){  // Последняя запись
                $sql = 'select * from `' . core::$tableName . '` order by `id` desc limit 1';
            } else {
                $sql = 'select * from `' . core::$tableName . '` where `' . $key . '`=?';
            }
            $res = $this->select($sql, [$val]);
            return (sizeof($res) == 0) ? [] : $res[0];
        } catch (Exception $e){
            throw new Exception($e -> getMessage());
        }
    }

    /**
     * Локирование таблицы
     */
    public function lock()
    {
        if (empty(core::$DBH)) core::initDB();
        $sql = 'LOCK TABLES `' . core::$tableName . '` WRITE';
        self::update($sql, []);
    }

}