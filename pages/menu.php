<?php
$host = 'localhost';
$dbname = 'foro_fie';
$username = 'auriel';
$password = 'pwd123';

session_start();

/* Nos movemos al login si no hay una sesión iniciada. */
if (!isset($_SESSION['logged-account'])) {
    header("Location: index.php");
    die();
}

try {
    // Establecer conexion con PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultar de categorías
    $query_categorias = "SELECT id, name FROM category ORDER BY name ASC";
    $stmt_categorias = $conn->prepare($query_categorias);
    $stmt_categorias->execute();
    $categorias = $stmt_categorias->fetchAll(PDO::FETCH_ASSOC);

    // Consulta de publicaciones recientes
    $query_publicaciones = "
        SELECT p.title, c.name AS categoria, p.account, p.content 
        FROM post p
        JOIN category c ON p.category = c.id
        ORDER BY p.id DESC
        LIMIT 10";
    $stmt_publicaciones = $conn->prepare($query_publicaciones);
    $stmt_publicaciones->execute();
    $publicaciones = $stmt_publicaciones->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
$conn = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foro FIE</title>
    <link rel="stylesheet" href="/templates/styles.css" type="text/css">
</head>
<body>
    <!-- Barra FORO-FIE -->
    <div class="navbar">
        <a href="menu.php" style="color: white; text-decoration: none;">FORO-FIE</a>
    </div>

    <div class="container">
        <!-- Parte izquierda. Categorias -->
        <div class="menu">
            <h2>Categorías</h2>
            <div class="categoria-botones">
                <?php if (!empty($categorias)): ?>
                    <?php foreach ($categorias as $categoria): ?>
                        <a href="<?= urlencode(strtolower($categoria['name'])) ?>.php" class="btn-categoria">
                            <?= htmlspecialchars($categoria['name']) ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay categorías disponibles</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Parte derecha. Publicaciones recientes-->
        <div class="contenido">
            <h2>Publicaciones recientes</h2>
            <?php if (!empty($publicaciones)): ?>
                <?php foreach ($publicaciones as $publicacion): ?>
                    <div class="publicacion">
                        <h3><?= htmlspecialchars($publicacion['title']) ?></h3>
                        <p><strong>Categoría:</strong> <?= htmlspecialchars($publicacion['categoria']) ?></p>
                        <p><strong>Contenido:</strong> <?= nl2br(htmlspecialchars($publicacion['content'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay publicaciones recientes</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
