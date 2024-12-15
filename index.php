<?php
require 'db.php';
session_start();

class Account {
    public $id;
    public $nickname;
    public $desc;
    public $hash;
    public $group;

    public function __set($name, $value) {}
}

if (isset($_POST["nickname"]) && isset($_POST["passwd"])) {
    $passwd = $_POST['passwd'];
    $stmt = DB::get()->prepare('
        SELECT "id", "nickname", "desc", "hash", "group"
        FROM "account"
        WHERE "nickname" LIKE :nickname'
    );
    $stmt->execute([':nickname' => $_POST['nickname']]);
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Account');
    $user = $stmt->fetch();
    if (password_verify($passwd, $user->hash)) {
        $_SESSION['logged-account'] = $user;
        header("Location: /pages/menu.php");
        die();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foro FIE</title>
    <link rel="stylesheet" href="/templates/styles.css"> 
</head>
<body>
    <h1>Bienvenido a Foro FIE</h1>
    <form action="index.php" method="POST">
        <label for="nickname">Nombre de usuario: </label>
        <input type="text" id="nickname" name="nickname"> <br>
        <label for="password">Contraseña: </label>
        <input type="password" id="passwd" name="passwd"> <br>
        <input type="submit" value="Iniciar sesión">
    </form>
</body>
</html>
