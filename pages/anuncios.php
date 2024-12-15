<?php
$host = 'localhost';
$dbname = 'foro_fie';
$username = 'auriel';
$password = 'pwd123';

try {
    // Establecer conexión con PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultar publicaciones de la categoría "Anuncios"
    $query_publicaciones = "
        SELECT p.title, c.name AS categoria, a.nickname AS autor, p.content, p.created_at
        FROM post p
        JOIN category c ON p.category = c.id
        JOIN account a ON p.account = a.id
        WHERE c.name = 'Anuncios'
        ORDER BY p.created_at DESC";
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
    <title>Publicaciones en Anuncios</title>
    <link rel="stylesheet" href="../templates/styles.css">
    <link rel="stylesheet" href="../templates/anuncios.css">
</head>
<body>
    <!-- Barra FORO-FIE -->
    <div class="navbar">
        <a href="menu.php" class="navbar-brand" style="color: white; text-decoration: none;">FORO-FIE</a>
    </div>

    <div class="container">
        <!-- Botón para crear una nueva publicación -->
        <div style="text-align: right; margin-bottom: 20px;">
            <a href="crear_publicacion.php" class="btn btn-primary">Crear Publicación</a>
        </div>

        <!-- Título principal -->
        <div class="titulo-principal">
            Anuncios
        </div>

        <!-- Publicaciones de la categoría "Anuncios" -->
        <div class="contenido">
            <?php if (!empty($publicaciones)): ?>
                <?php foreach ($publicaciones as $publicacion): ?>
                    <div class="publicacion">
                        <h3><?= htmlspecialchars($publicacion['title']) ?></h3>
                        <div class="meta">
                            Publicado por <strong><?= htmlspecialchars($publicacion['autor']) ?></strong> el <?= date('d/m/Y H:i', strtotime($publicacion['created_at'])) ?>
                        </div>
                        <div class="contenido-texto">
                            <?= nl2br(htmlspecialchars($publicacion['content'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay publicaciones en esta categoría.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>