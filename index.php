<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';
session_start();

class Account {
    public $id;
    public $nickname;
    public $desc;
    public $hash;
    public $group;
}

if (isset($_POST["nickname"]) && isset($_POST["passwd")) {
    $passwd = $_POST['passwd'];
    $query = DB::get()->prepare('
        SELECT "id", "nickname", "desc", "hash", "group"
        FROM "account"
        WHERE "first_name" LIKE :nickname'
    );
    $query->execute([':nickname' => $_POST['nickname']]);
    $user = $query->fetch(PDO::FETCH_CLASS, 'Account');
    if (password_verify($passwd, $user->$hash)) {
        echo 'Éxito';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foro FIE</title>
    <link rel="stylesheet" href="templates/style.css"> 
</head>
<body>
    <form action="index.php" method="POST">
        <label for="nickname">Nombre de usuario: </label>
        <input type="text" id="nickname" name="nickname"> <br>
        <label for="password">Contraseña: </label>
        <input type="password" id="passwd" name="passwd"> <br>
        <input type="submit" value="Iniciar sesión">
    </form>


    <header>
        <h1>Bienvenido a Foro FIE</h1>
        <nav>
            <ul>
                <li><a href="pages/register.php">Registrarse</a></li>
                <li><a href="pages/login.php">Iniciar Sesión</a></li>
            </ul>
        </nav>
    </header>
