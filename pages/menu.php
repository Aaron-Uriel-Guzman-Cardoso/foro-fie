<?php
$host = 'localhost';
$dbname = 'foro_fie';
$username = 'auriel';
$password = 'pwd123';

session_start();

/* Nos movemos al login si no hay una sesión iniciada. */
if (!isset($_SESSION['logged-account'])) {
    header("Location: /index.php");
    die();
}

require '../db.php';

try {
    $categoriesStmt = DB::get()->query('
        SELECT "id", "name"
        FROM "category"
        ORDER BY "name" ASC'
    );
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Imposible obtener categorías" . $e->getMessage());
}
try {
    $postsStmt = DB::get()->query('
        SELECT "title", "name" AS "category-name", "account", "content"
        FROM "post" 
            JOIN "category" ON "post"."category" = "category"."id"
        ORDER BY "post"."id" DESC LIMIT 10');
    $posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Imposible obtener publicaciones" . $e->getMessage());
}
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
                    <?php
                    if (!empty($categories)):
                        foreach ($categories as $category):
                    ?>
                    <a href="posts.php?id=<?=$category['id'];?>&name=<?=$category['name'];?>" class="btn-categoria">
                            <?= htmlspecialchars($category['name']) ?>
                        </a>
                    <?php
                        endforeach;
                    endif;
                    ?>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $categoria): ?>
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
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $publicacion): ?>
                    <div class="publicacion">
                        <h3><?= htmlspecialchars($publicacion['title']) ?></h3>
                        <p><strong>Categoría:</strong> <?= htmlspecialchars($publicacion['category-name']) ?></p>
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
