<?php
require_once __DIR__ . '/../../app/config.php';
require_once __DIR__ . '/../../app/auth.php';

admin_required();

$id = (int) ($_GET['id'] ?? 0);
if ($id) {
    $stmt = db()->prepare("UPDATE carro SET status = 'ativo' WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: /admin/carros/listar.php');
exit;
