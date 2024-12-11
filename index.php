<?php
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
        <input type="text" id="nickname" name="nickname">
        <label for="password">Contraseña: </label>
        <input type="password" id="password" name="password">
        <input type="submit" value="Iniciar sesión">
    </form>
    <?php
        error_reporting(E_ALL);
        require 'db.php';
        $query = NULL;
        assert(isset($_POST['nickname']) && isset($_POST['password']));
        if (isset($_POST['user'])) {
            $query = Hrdb::get()->prepare("SELECT employee_id, first_name, last_name, email, salary FROM employees WHERE first_name LIKE :employee ORDER BY first_name LIMIT 20");
            $query->execute([':employee' => ('%' . $_POST['employee'] . '%')]);
        } else {
            $query = Hrdb::get()->query(" SELECT employee_id, first_name, last_name, email, salary FROM employees ORDER BY first_name LIMIT 20");
        }
        include 'table_header_template.html';
        while ($row = $query->fetch()) {
            printf('<tr><td><a href="user_info.php?id=%s">%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>' . PHP_EOL,
                $row['employee_id'], $row['employee_id'], $row['first_name'], $row['last_name'],
                $row['email'], $row['salary']);
            
        }
        include 'table_footer_template.html';
        ?>


    <header>
        <h1>Bienvenido a Foro FIE</h1>
        <nav>
            <ul>
                <li><a href="pages/register.php">Registrarse</a></li>
                <li><a href="pages/login.php">Iniciar Sesión</a></li>
            </ul>
        </nav>
    </header>
