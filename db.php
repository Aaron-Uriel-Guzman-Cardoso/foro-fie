<?php
$host = 'localhost';
$dbname = 'foro_fie';
$username = 'auriel';
$password = 'pwd123';


# Inspirado de https://stackoverflow.com/a/5314718
class DB {
    private static $instance = null;
    public static function get() {
        if (self::$instance == null) {
            try {
                self::$instance = new PDO(
                    "mysql:host=$host:8000;dbname=$dbname;",
                    $username,
                    $password,
                    array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET SQL_MODE="STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION,ANSI_QUOTES,NO_ZERO_IN_DATE,ONLY_FULL_GROUP_BY"')
                );
            } catch (PDOException $e) {
                print("Conexión fallida: $e->getMessage()" . PHP_EOL);
            }
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}
?>
