<?php
require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/auth.php';

login_required();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carro_id = (int) ($_POST['carro_id'] ?? 0);
    $usuario_id = $_SESSION['user_id'];

    $stmt = db()->prepare("SELECT id FROM favorito WHERE usuario_id = ? AND carro_id = ?");
    $stmt->execute([$usuario_id, $carro_id]);
    $fav = $stmt->fetch();

    if ($fav) {
        $stmt = db()->prepare("DELETE FROM favorito WHERE id = ?");
        $stmt->execute([$fav['id']]);
    } else {
        $stmt = db()->prepare("INSERT INTO favorito (usuario_id, carro_id) VALUES (?, ?)");
        $stmt->execute([$usuario_id, $carro_id]);
    }
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/index.php'));
exit;