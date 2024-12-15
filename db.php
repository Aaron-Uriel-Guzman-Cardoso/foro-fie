<?php
# Inspirado de https://stackoverflow.com/a/5314718
class DB {
    private static $instance = null;
    public static function get() {
        if (self::$instance == null) {
            try {
                self::$instance = new PDO(
                    "mysql:host=localhost:3306;dbname=foro_fie;charset=utf8",
                    'auriel',
                    'pwd123',
                    array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET SQL_MODE="STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION,ANSI_QUOTES,NO_ZERO_IN_DATE,ONLY_FULL_GROUP_BY"')
                );
            } catch (PDOException $e) {
                print($e->getMessage()  . PHP_EOL);
                print($e->getCode() . PHP_EOL); 
                die();
            }
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}
?>
