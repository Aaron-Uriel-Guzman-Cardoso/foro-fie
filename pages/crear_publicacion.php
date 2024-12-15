<?php
$host = 'localhost';
$dbname = 'foro_fie';
$username = 'auriel';
$password = 'pwd123';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = intval($_POST['category']);
    $account = NULL; // No hay usuario autenticado

    if ($title === '' || $content === '' || $category === 0) {
        $error = 'Todos los campos son obligatorios.';
    } else {
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("INSERT INTO `post` (`account`, `title`, `content`, `category`) VALUES (:account, :title, :content, :category)");
            $stmt->bindValue(':account', $account, PDO::PARAM_NULL); // Establecer como NULL si no hay usuario
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->bindParam(':category', $category, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: anuncios.php");
            exit();

        } catch (PDOException $e) {
            $error = "Error al crear la publicación: " . $e->getMessage();
        }
    }
}

// Obtener categorías para el formulario
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT id, name FROM category");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Crear Publicación</title>
    <link rel="stylesheet" href="../templates/styles.css">
    <link rel="stylesheet" href="../templates/crear_publicacion.css">
</head>
<body>
    <!-- Barra FORO-FIE -->
    <div class="navbar">
        <a href="anuncios.php" class="btn btn-back">← Regresar a Anuncios</a>
        <span class="navbar-brand">FORO-FIE</span>
    </div>

    <div class="container">
        <div class="crear-publicacion">
            <h2>Crear Nueva Publicación</h2>
            <?php if ($error !== ''): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form action="crear_publicacion.php" method="POST" class="form-publicacion">
                <div>
                    <label for="title">Título:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div>
                    <label for="content">Contenido:</label>
                    <textarea id="content" name="content" rows="5" required></textarea>
                </div>
                <div>
                    <label for="category">Categoría:</label>
                    <select id="category" name="category" required>
                        <option value="">Selecciona una categoría</option>
                        <?php foreach ($categories as $categoria): ?>
                            <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Publicar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>