<?php
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/auth.php';

admin_required();

$id = (int) ($_GET['id'] ?? 0);
if ($id && $id !== $_SESSION['user_id']) {
    $stmt = db()->prepare("UPDATE usuario SET status = 'inativo' WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: /admin/usuarios/listar.php');
exit;
