<?php
require '../account.php';
require '../db.php';

session_start();

if (!isset($_GET['id']) || !isset($_GET['name']) || !isset($_SESSION['logged-account'])) {
    header("Location: /index.php");
    die();
}


$host = 'localhost';
$dbname = 'foro_fie';
$username = 'auriel';
$password = 'pwd123';
$error = '';

// Manejar la adición de un nuevo comentario o respuesta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_submit'])) {
    $post_id = intval($_POST['post_id']);
    $content = trim($_POST['comment_content']);
    $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : null;

    // Verificar si el usuario está autenticado
    if (isset($_SESSION['logged-account'])) {
        $account_id = $_SESSION['logged-account']->id;
    } else {
        $account_id = null; // Asignar NULL para comentarios anónimos
    }

    if (!empty($content)) {
        try {
            // Insertar comentario
            $stmt_insert = DB::get()->prepare('
                INSERT INTO "comment" ("post", "account", "publication", "content")
                VALUES (:post, :account, NOW(), :content)'
            );
            $stmt_insert->bindParam(':post', $post_id, PDO::PARAM_INT);
            if ($account_id !== null) {
                $stmt_insert->bindParam(':account', $account_id, PDO::PARAM_INT);
            } else {
                $stmt_insert->bindValue(':account', null, PDO::PARAM_NULL);
            }
            $stmt_insert->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt_insert->execute();

            $new_comment_id = DB::get()->lastInsertId();

            // Si es una respuesta, insertar en la tabla reply
            if ($parent_id !== null) {
                $stmt_reply = DB::get()->prepare('
                    INSERT INTO "reply" ("comment", "parent")
                    VALUES (:comment, :parent)'
                );
                $stmt_reply->bindParam(':comment', $new_comment_id, PDO::PARAM_INT);
                $stmt_reply->bindParam(':parent', $parent_id, PDO::PARAM_INT);
                $stmt_reply->execute();
            }

            header(sprintf('Location: posts.php#post-%d?id=%d&name=%s', $post_id, $_GET['id'], $_GET['name']));
            exit();

        } catch (PDOException $e) {
            $error = "Error al agregar el comentario: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, completa el comentario.";
    }
}

try {
    $postsStmt = DB::get()->prepare('
        SELECT p.id, p.title, c.name AS categoria, a.nickname AS autor, 
            p.content, p.created_at
        FROM "post" p
        JOIN "category" c ON p.category = c.id
        LEFT JOIN "account" a ON p.account = a.id
        WHERE "c"."id" = :catId
        ORDER BY p.created_at DESC'
    );
    $postsStmt->execute([':catId' => $_GET['id']]);
    $publicaciones = $postsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

//Función para obtener todos los comentarios y sus relaciones de respuesta

function get_all_comments_with_replies($conn, $post_id) {

    // Obtener todos los comentarios del post
    $stmt_comments = DB::get()->prepare('
        SELECT id, post, account, publication, content
        FROM "comment"
        WHERE "post" = :post_id
        ORDER BY "publication" ASC'
    );
    $stmt_comments->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt_comments->execute();
    $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

    // Obtener todas las relaciones de reply para estos comentarios
    $comment_ids = array_column($comments, 'id');
    if (empty($comment_ids)) {
        return [];
    }
    $placeholders = implode(',', array_fill(0, count($comment_ids), '?'));
    $stmt_replies = DB::get()->prepare("
        SELECT comment, parent 
        FROM \"reply\"
        WHERE \"parent\" IN ($placeholders)"
    );
    foreach ($comment_ids as $k => $id) {
        $stmt_replies->bindValue(($k+1), $id, PDO::PARAM_INT);
    }
    $stmt_replies->execute();
    $replies = $stmt_replies->fetchAll(PDO::FETCH_ASSOC);

    // Es el mapeo de padre-hijo de los comentarios
    $reply_map = [];
    foreach ($replies as $reply) {
        $parent = $reply['parent'];
        $child = $reply['comment'];
        if (!isset($reply_map[$parent])) {
            $reply_map[$parent] = [];
        }
        $reply_map[$parent][] = $child;
    }

    // Construir un mapa de comentarios por ID
    $comments_map = [];
    foreach ($comments as $comment) {
        $comment['replies'] = [];
        $comments_map[$comment['id']] = $comment;
    }

    // Asignar los comentarios hijos a sus respectivos padres
    foreach ($reply_map as $parent_id => $child_ids) {
        foreach ($child_ids as $child_id) {
            if (isset($comments_map[$parent_id]) && isset($comments_map[$child_id])) {
                $comments_map[$parent_id]['replies'][] = &$comments_map[$child_id];
            }
        }
    }

    $comment_tree = [];
    foreach ($comments_map as $comment) {
        // Verificar si este comentario no es una respuesta de otro comentario
        $stmt_check_parent = DB::get()->prepare('
            SELECT comment, parent
            FROM "reply"
            WHERE "comment" = :comment_id'
        );
        $stmt_check_parent->bindParam(':comment_id', $comment['id'], PDO::PARAM_INT);
        $stmt_check_parent->execute();
        $is_reply = $stmt_check_parent->fetch(PDO::FETCH_ASSOC);
        if (!$is_reply) {
            $comment_tree[] = $comment;
        }
    }

    return $comment_tree;
}

//Función recursiva para mostrar los comentarios y sus respuestas
function display_comments($comments, $post_id) {
    foreach ($comments as $comment) {
        ?>
        <li class="comentario">
            <strong><?= htmlspecialchars($comment['autor'] ?? 'Anónimo') ?></strong> el <?= date('d/m/Y H:i', strtotime($comment['publication'])) ?>
            <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
            <button class="reply-button" onclick="toggleReplyForm(<?= $comment['id'] ?>)">Responder</button>

            <!-- Formulario de respuesta -->
            <div class="reply-form" id="reply-form-<?= $comment['id'] ?>">
                <form action="<?php sprintf('posts.php#post-%d?id=%d&name=$s', $post_id, $_GET['id'], $_GET['name']) ?>" method="POST" class="form-comentario">
                    <input type="hidden" name="post_id" value="<?= $post_id ?>">
                    <input type="hidden" name="parent_id" value="<?= $comment['id'] ?>">
                    <div>
                        <label for="comment_content-<?= $comment['id'] ?>">Respuesta:</label>
                        <textarea id="comment_content-<?= $comment['id'] ?>" name="comment_content" rows="2" required></textarea>
                    </div>
                    <div>   
                        <button type="submit" name="comment_submit" class="btn-submit">Agregar Respuesta</button>
                    </div>
                    <?php if (isset($error) && $error !== ''): ?>
                        <p class="error"><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Sección de Respuestas -->
            <?php if (!empty($comment['replies'])): ?>
                <ul class="lista-respuestas">
                    <?php display_comments($comment['replies'], $post_id); ?>
                </ul>
            <?php endif; ?>
        </li>
        <?php
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicaciones en Anuncios</title>
    <link rel="stylesheet" href="../templates/styles.css">
    <link rel="stylesheet" href="../templates/anuncios.css">
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2em;
            color: #2c3e50;
        }

        .contenido {
            width: 100%;
            max-width: 800px; 
        }

        .form-comentario {
            margin-top: 20px;
        }

        .form-comentario textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        .lista-comentarios,
        .lista-respuestas {
            list-style-type: none;
            padding: 0;
        }

        .comentario,
        .respuesta {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .respuesta {
            margin-left: 20px;
        }

        .reply-button {
            background-color: transparent;
            color: #007bff;
            border: none;
            cursor: pointer;
            padding: 0;
            font-size: 0.9em;
            text-decoration: underline;
        }

        .reply-form {
            margin-top: 10px;
            display: none;
        }

        .reply-form textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .reply-form .btn-submit {
            background-color: #6c757d;
            color: #fff;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
    <script>
        function toggleReplyForm(commentId) {
            var form = document.getElementById('reply-form-' + commentId);
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <!-- Barra FORO-FIE -->
    <div class="navbar">
        <a href="menu.php" class="navbar-brand">FORO-FIE</a>
        <div class="navbar-buttons">
            <a href="anuncios.php" class="btn btn-primary">Anuncios</a>
            <a href="crear_publicacion.php" class="btn btn-secondary">Crear Publicación</a>
        </div>
    </div>

    <div class="container">
        <!-- Título de Publicaciones -->
        <h2>Publicaciones</h2>

        <!-- Publicaciones de la categoría "Anuncios" -->
        <div class="contenido">
            <?php if (!empty($publicaciones)): ?>
                <?php foreach ($publicaciones as $publicacion): ?>
                    <div class="publicacion" id="post-<?= $publicacion['id'] ?>">
                        <h3><?= htmlspecialchars($publicacion['title']) ?></h3>
                        <div class="meta">
                            Publicado por <strong><?= htmlspecialchars($publicacion['autor'] ?? 'Anónimo') ?></strong> el <?= date('d/m/Y H:i', strtotime($publicacion['created_at'])) ?>
                        </div>
                        <div class="contenido-texto">
                            <?= nl2br(htmlspecialchars($publicacion['content'])) ?>
                        </div>

                        <!-- Sección de Comentarios -->
                        <div class="comentarios">
                            <h4>Comentarios</h4>
                            <?php
                                // Obtener todos los comentarios para esta publicación con sus respuestas
                                $comment_tree = get_all_comments_with_replies($conn, $publicacion['id']);
                            ?>

                            <?php if (!empty($comment_tree)): ?>
                                <ul class="lista-comentarios">
                                    <?php display_comments($comment_tree, $publicacion['id']); ?>
                                </ul>
                            <?php else: ?>
                                <p>No hay comentarios aún.</p>
                            <?php endif; ?>

                            <!-- Formulario para agregar un nuevo comentario principal -->
                            <form action="<?php sprintf('posts.php#post-%d?id=%d&name=$s', $publicacion['id'], $_GET['id'], $_GET['name']) ?>" method="POST" class="form-comentario">
                                <input type="hidden" name="post_id" value="<?= $publicacion['id'] ?>">
                                <div>
                                    <label for="comment_content-<?= $publicacion['id'] ?>">Comentario:</label>
                                    <textarea id="comment_content-<?= $publicacion['id'] ?>" name="comment_content" rows="3" required></textarea>
                                </div>
                                <div>   
                                    <button type="submit" name="comment_submit" class="btn btn-secondary">Agregar Comentario</button>
                                </div>
                                <?php if (isset($error) && $error !== ''): ?>
                                    <p class="error"><?= htmlspecialchars($error) ?></p>
                                <?php endif; ?>
                            </form>
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
