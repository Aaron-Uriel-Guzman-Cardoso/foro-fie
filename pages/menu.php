<?php
$host = 'localhost';
$dbname = 'foro_fie';
$username = 'auriel';
$password = 'pwd123';

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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center; 
        }

        /* Barra FORO-FIE */
        .navbar {
            width: 100%;
            background-color: #003366;
            color: #ffffff;
            text-align: center;
            padding: 10px 0;
            font-size: 24px;
            font-weight: bold;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 10;
        }

        /* Principal */
        .container {
            display: flex;
            margin-top: 80px; 
            max-width: 1200px; 
            width: 100%;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
        }

        .menu {
            width: 30%;
            padding: 20px;
            border-right: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        .contenido {
            width: 70%;
            padding: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin: 10px 0;
        }

        .publicacion {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #f4f4f4;
        }

        .publicacion h3 {
            margin: 0 0 10px;
        }

        .categoria-botones {
            display: flex;
            flex-wrap: wrap; 
            gap: 10px; 
            margin-top: 10px;
        }

        .btn-categoria {
            display: inline-block;
            padding: 10px 20px;
            font-size: 14px;
            color: white;
            background-color: #003366; 
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            width: 200px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn-categoria:hover {
            background-color: #191970; 
            transform: scale(1.05); 
}

        .btn-categoria:active {
            background-color: #004085; 
}


    </style>
</head>
<body>
    <!-- Barra FORO-FIE -->
    <div class="navbar">
        FORO-FIE
    </div>

    <div class="container">
        <!-- Parte izquierda. Categorias -->
        <div class="menu">
    <h2>Categorías</h2>
    <div class="categoria-botones">
        <?php if (!empty($categorias)): ?>
            <?php foreach ($categorias as $categoria): ?>
                <a href="category.php?id=<?= htmlspecialchars($categoria['id']) ?>" class="btn-categoria">
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
