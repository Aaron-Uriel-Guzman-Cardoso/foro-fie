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
                self::$instance = new PDO("mysql:host=$host:8000;dbname=$dbname;",
                $username, $password);
            } catch (PDOException $e) {
                print("Excepción generada, mensaje: $e->getMessage()" . PHP_EOL);
            }
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}
?>
