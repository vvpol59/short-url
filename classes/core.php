<?php
/**
 *
 * User: vvpol
 * Date: 20.03.2017
 * Time: 21:14
 */

class core
{
    const FLIST = [  // Доступные посетителям функции
        'index',
        'make',
    ];

    public static $data;
    public static $DBH;
    public static $tableName;
    public static $host;

    /**
     * Подключение к БД
     * @throws Exception
     */
    public static function initDB(){
        try {
            $config = [];
            include $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
            self::$tableName = $config['table'];
            self::$DBH = new PDO($config['dsn'], $config['user'], $config['password']);
            self::$DBH -> exec("set names 'utf8'");
            self::$DBH->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        } catch (PDOException $e) {
            self::$lastError = $e->errorInfo;
            throw new Exception($e -> getMessage());
        }
    }

    /**
     * Запись/модификация таблицы
     * @param $sql
     * @param $values
     * @throws Exception
     */
    public static function update($sql, $values){
        try	{
            if (empty(self::$DBH)) self::initDB();
            $STH = self::$DBH->prepare($sql);
            $STH->execute($values);
            self::$lastError = array(0, 0, 0);
        } catch (PDOException $e) {
            self::$lastError = $e -> errorInfo;
            throw new PDOException( $e -> getMessage() . ':' . $sql );
        }
    }

    /**
     * Запуск приложения
     */
    public static function run()
    {
        self::$host = explode('/', $_SERVER['SERVER_PROTOCOL'])[0] . '://' . $_SERVER['HTTP_HOST'];
        session_start();
        if (isset($_REQUEST['route'])){ // На короткую ссылку
            (new Controller())->go($_REQUEST['route']);
        }
        if (!isset($_REQUEST['fun'])) {
            $fun = 'index';
        } else {
            $fun = $_REQUEST['fun'];
        }
        if (isset($_REQUEST['url'])){
            self::$data = $_REQUEST['url'];
        } else {
            self::$data = '';
        }
        if (!in_array($fun, self::FLIST)){ // Неизвестная или не разрешённая
            echo 'Неизвестная или не разрешённая ' . $fun;
            return;
        }
        (new Controller)->$fun();
    }
}
// Автозагрузчик классов
function classAutoload($className){
    $fn = $_SERVER['DOCUMENT_ROOT'] . '/classes/' . $className . '.php';
    if (file_exists($fn)) {
        require_once $fn;
        return true;
    }
    return false;
}
spl_autoload_register('classAutoload');
